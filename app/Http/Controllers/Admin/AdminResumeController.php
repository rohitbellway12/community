<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\ResumeSetting;
use App\Models\ResumeShareVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminResumeController extends Controller
{
    /**
     * Resumes list & stats.
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'resumes');
        if (!in_array($activeTab, ['resumes', 'verifications', 'settings'])) {
            $activeTab = 'resumes';
        }

        $search = $request->query('search');
        $watermarkFilter = $request->query('watermark');

        $query = Resume::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($watermarkFilter === 'clean') {
            $query->where('is_watermark_removed', true);
        } elseif ($watermarkFilter === 'watermarked') {
            $query->where('is_watermark_removed', false);
        }

        $resumes = $query->paginate(15, ['*'], 'resumes_page')->withQueryString();

        $stats = [
            'total' => Resume::count(),
            'clean' => Resume::where('is_watermark_removed', true)->count(),
            'pending_verifications' => ResumeShareVerification::where('status', 'pending')->count(),
            'downloads' => (int) Resume::sum('download_count'),
        ];

        // Verifications query
        $verificationStatus = $request->query('verification_status', 'pending');
        $vQuery = ResumeShareVerification::with(['user', 'resume', 'reviewer'])->latest();
        if (in_array($verificationStatus, ['pending', 'approved', 'rejected'])) {
            $vQuery->where('status', $verificationStatus);
        }
        $verifications = $vQuery->paginate(15, ['*'], 'verifications_page')->withQueryString();

        $verificationCounts = [
            'pending' => ResumeShareVerification::where('status', 'pending')->count(),
            'approved' => ResumeShareVerification::where('status', 'approved')->count(),
            'rejected' => ResumeShareVerification::where('status', 'rejected')->count(),
        ];

        // Global Resume Settings
        $setting = ResumeSetting::config();

        return view('admin.resumes.index', compact(
            'resumes',
            'stats',
            'search',
            'watermarkFilter',
            'activeTab',
            'verifications',
            'verificationStatus',
            'verificationCounts',
            'setting'
        ));
    }

    /**
     * Reshare verifications queue (Redirects to unified hub tab).
     */
    public function verifications(Request $request)
    {
        return redirect()->route('admin.resumes.index', array_merge($request->query(), ['tab' => 'verifications']));
    }

    /**
     * Approve verification and remove watermark.
     */
    public function approveVerification(ResumeShareVerification $verification)
    {
        $verification->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $verification->resume->update([
            'is_watermark_removed' => true,
            'watermark_removed_at' => now(),
        ]);

        return redirect()->route('admin.resumes.index', ['tab' => 'verifications'])
            ->with('success', "Verification for {$verification->user->name} approved! Watermark permanently removed.");
    }

    /**
     * Reject verification.
     */
    public function rejectVerification(Request $request, ResumeShareVerification $verification)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $verification->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.resumes.index', ['tab' => 'verifications'])
            ->with('success', "Verification rejected. Rejection reason recorded.");
    }

    /**
     * Edit Resume & Watermark Settings (Redirects to unified hub tab).
     */
    public function settings()
    {
        return redirect()->route('admin.resumes.index', ['tab' => 'settings']);
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'openai_api_key' => 'nullable|string|max:255',
            'openai_model' => 'required|string|max:100',
            'watermark_text' => 'required|string|max:190',
            'watermark_enabled' => 'nullable|boolean',
            'verification_mode' => 'required|in:both,instant,manual',
            'reshare_guidelines' => 'nullable|string|max:3000',
            'reshare_content' => 'nullable|string|max:1000',
            'reshare_target_url' => 'nullable|url|max:255',
        ]);

        $validated['watermark_enabled'] = $request->has('watermark_enabled');

        $setting = ResumeSetting::firstOrCreate([]);
        $setting->update($validated);

        ResumeSetting::clearCache();

        return redirect()->route('admin.resumes.index', ['tab' => 'settings'])
            ->with('success', 'Resume Builder & Watermark settings saved successfully!');
    }
}
