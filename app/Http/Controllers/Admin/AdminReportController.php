<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    /**
     * Display reports listing.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Report::with(['reporter.profile', 'reportable', 'reviewer.profile']);

        if ($status && in_array($status, ['pending', 'reviewing', 'resolved', 'dismissed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', ReportStatus::PENDING->value)->count(),
            'resolved' => Report::where('status', ReportStatus::RESOLVED->value)->count(),
            'dismissed' => Report::where('status', ReportStatus::DISMISSED->value)->count(),
        ];

        return view('admin.reports', compact('reports', 'stats'));
    }

    /**
     * Update report status (e.g. reviewing, resolved, dismissed).
     */
    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,resolved,dismissed',
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Report status updated to {$validated['status']}.");
    }

    /**
     * Delete the reported content (Post or Comment) and resolve report.
     */
    public function deleteContent(Report $report): RedirectResponse
    {
        $content = $report->reportable;

        if ($content) {
            $content->delete();
        }

        $report->update([
            'status' => ReportStatus::RESOLVED->value,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Reported content has been removed and report is resolved.');
    }

    /**
     * Ban/block the author of the reported content.
     */
    public function banUser(Report $report): RedirectResponse
    {
        $content = $report->reportable;
        $user = $content?->user;

        if (!$user) {
            return back()->with('error', 'Author could not be found.');
        }

        $user->update([
            'status' => 'blocked',
        ]);

        $report->update([
            'status' => ReportStatus::RESOLVED->value,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "User {$user->name} has been blocked and report resolved.");
    }

    /**
     * Delete report permanently.
     */
    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();
        return back()->with('success', 'Report deleted successfully.');
    }
}
