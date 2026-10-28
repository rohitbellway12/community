<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\ResumeSetting;
use App\Models\ResumeShareVerification;
use App\Services\OpenAiResumeService;
use App\Services\ResumeSampleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResumeBuilderController extends Controller
{
    /**
     * Landing / Template Selection.
     */
    public function index()
    {
        $setting = ResumeSetting::config();
        $templates = [
            [
                'id' => 'korean_standard',
                'name' => 'Korean Standard Job Application (이력서 & 자기소개서)',
                'tagline' => 'Official tabular layout with photo box, language tests, military service, and 4-part self-introduction. Bilingual (English & 한국어).',
                'badge' => 'Bilingual (KR / EN)',
                'bg_preview' => 'from-emerald-900 to-slate-900',
            ],
            [
                'id' => 'modern',
                'name' => 'Modern Professional',
                'tagline' => 'Clean dual-column layout with subtle gold/navy accents',
                'badge' => 'Most Popular',
                'bg_preview' => 'from-slate-900 to-slate-800',
            ],
            [
                'id' => 'classic',
                'name' => 'Executive ATS Classic',
                'tagline' => 'Structured, corporate single-column layout optimized for ATS scanners',
                'badge' => 'ATS Friendly',
                'bg_preview' => 'from-amber-700 to-amber-900',
            ],
            [
                'id' => 'creative',
                'name' => 'Creative & Impact',
                'tagline' => 'Vibrant sidebar layout featuring visual skill badges and project timeline',
                'badge' => 'Standout',
                'bg_preview' => 'from-indigo-900 to-slate-900',
            ],
        ];

        $userResumesCount = Auth::check() ? Resume::where('user_id', Auth::id())->count() : 0;

        return view('resume-builder.index', compact('templates', 'setting', 'userResumesCount'));
    }

    /**
     * Resume Creation Wizard.
     */
    public function create(Request $request)
    {
        $template = $request->query('template', 'korean_standard');
        $setting = ResumeSetting::config();

        // Prefill from user profile if available
        $user = Auth::user();
        $prefillData = [
            'title' => 'My Professional Resume',
            'template' => in_array($template, ['modern', 'classic', 'creative', 'korean_standard']) ? $template : 'korean_standard',
            'language' => $request->query('language', 'en'),
            'personal_info' => [
                'full_name' => $user->name ?? '',
                'english_name' => '',
                'date_of_birth' => '',
                'gender' => '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'location' => $user->profile->location ?? '',
                'job_title' => $user->profile->headline ?? '',
                'linkedin' => '',
                'github' => '',
                'portfolio' => '',
                'photo_url' => '',
                'total_experience' => '',
                'emergency_phone' => '',
                'declaration_date' => date('F d, Y'),
                'signature_name' => $user->name ?? '',
            ],
            'summary' => $user->profile->bio ?? '',
            'education' => [],
            'experience' => [],
            'skills' => ['Communication', 'Teamwork', 'Problem Solving'],
            'projects' => [],
            'certifications' => [],
            'languages' => ['English'],
            'military_service' => [
                'status' => 'exempted',
                'branch' => '',
                'rank' => '',
                'period' => '',
                'discharge_reason' => '',
            ],
            'language_tests' => [],
            'computer_skills' => [],
            'self_introduction' => [
                'growth' => '',
                'personality' => '',
                'strengths' => '',
                'motivation_and_aspirations' => '',
            ],
        ];

        return view('resume-builder.editor', [
            'resume' => null,
            'initialData' => $prefillData,
            'setting' => $setting,
        ]);
    }

    /**
     * Edit existing resume.
     */
    public function edit(Resume $resume)
    {
        if ($resume->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to resume.');
        }

        $setting = ResumeSetting::config();

        $initialData = [
            'id' => $resume->id,
            'title' => $resume->title,
            'template' => $resume->template,
            'language' => $resume->language ?? 'en',
            'personal_info' => $resume->personal_info ?? [],
            'summary' => $resume->summary ?? '',
            'education' => $resume->education ?? [],
            'experience' => $resume->experience ?? [],
            'skills' => $resume->skills ?? [],
            'projects' => $resume->projects ?? [],
            'certifications' => $resume->certifications ?? [],
            'languages' => $resume->languages ?? [],
            'military_service' => $resume->military_service ?? [],
            'language_tests' => $resume->language_tests ?? [],
            'computer_skills' => $resume->computer_skills ?? [],
            'self_introduction' => $resume->self_introduction ?? [],
            'is_watermark_removed' => (bool) $resume->is_watermark_removed,
        ];

        return view('resume-builder.editor', [
            'resume' => $resume,
            'initialData' => $initialData,
            'setting' => $setting,
        ]);
    }

    /**
     * Save or update resume.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'title' => 'required|string|max:190',
            'template' => 'required|string|in:modern,classic,creative,korean_standard',
            'language' => 'nullable|string|in:en,ko',
            'personal_info' => 'required|array',
            'personal_info.full_name' => 'required|string|max:190',
            'personal_info.english_name' => 'nullable|string|max:190',
            'personal_info.date_of_birth' => 'nullable|string|max:50',
            'personal_info.gender' => 'nullable|string|max:50',
            'personal_info.email' => 'required|email|max:190',
            'personal_info.phone' => 'nullable|string|max:50',
            'personal_info.location' => 'nullable|string|max:190',
            'personal_info.job_title' => 'nullable|string|max:190',
            'personal_info.total_experience' => 'nullable|string|max:100',
            'personal_info.emergency_phone' => 'nullable|string|max:50',
            'personal_info.photo_url' => 'nullable|string',
            'personal_info.linkedin' => 'nullable|string|max:255',
            'personal_info.github' => 'nullable|string|max:255',
            'personal_info.portfolio' => 'nullable|string|max:255',
            'summary' => 'nullable|string|max:5000',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'skills' => 'nullable|array',
            'projects' => 'nullable|array',
            'certifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'military_service' => 'nullable|array',
            'language_tests' => 'nullable|array',
            'computer_skills' => 'nullable|array',
            'self_introduction' => 'nullable|array',
        ]);

        $resumeId = $request->input('id');

        if ($resumeId) {
            $resume = Resume::where('id', $resumeId)->where('user_id', Auth::id())->firstOrFail();
            $resume->update($validated);
        } else {
            $validated['user_id'] = Auth::id();
            $resume = Resume::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resume saved successfully!',
            'resume_id' => $resume->id,
            'redirect_url' => route('resume-builder.edit', $resume->id),
        ]);
    }

    /**
     * Upload candidate passport/ID photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('photo')->store('resumes/photos', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path,
            'message' => 'Candidate photo uploaded successfully!',
        ]);
    }

    /**
     * Student's "My Resumes" dashboard.
     */
    public function myResumes()
    {
        $resumes = Resume::where('user_id', Auth::id())
            ->with(['latestVerification'])
            ->latest()
            ->paginate(12);

        $setting = ResumeSetting::config();

        return view('resume-builder.my-resumes', compact('resumes', 'setting'));
    }

    /**
     * Preview sample dummy resume for a template.
     */
    public function samplePreview(Request $request, string $template = 'modern')
    {
        $lang = $request->query('lang', $template === 'korean_standard' ? 'ko' : 'en');
        $sampleResume = ResumeSampleService::getSampleResume($template, $lang);

        return view("resume-builder.templates.{$sampleResume->template}", [
            'resume' => $sampleResume,
            'setting' => ResumeSetting::config(),
            'isClean' => true,
            'forPdf' => false,
            'isSample' => true,
            'isEmbed' => $request->boolean('embed'),
        ]);
    }

    public function preview(Resume $resume)
    {
        $role = Auth::user() ? (is_object(Auth::user()->role) ? Auth::user()->role->value : Auth::user()->role) : null;
        $isAdmin = $role && trim(strtolower($role)) === 'admin';
        if ($resume->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $setting = ResumeSetting::config();
        $isClean = $resume->is_watermark_removed || !$setting->watermark_enabled;
        $isEmbed = request()->boolean('embed');

        return view("resume-builder.templates.{$resume->template}", [
            'resume' => $resume,
            'setting' => $setting,
            'isClean' => $isClean,
            'forPdf' => false,
            'isEmbed' => $isEmbed,
        ]);
    }

    /**
     * Download PDF with or without watermark.
     */
    public function downloadPdf(Resume $resume)
    {
        $role = Auth::user() ? (is_object(Auth::user()->role) ? Auth::user()->role->value : Auth::user()->role) : null;
        $isAdmin = $role && trim(strtolower($role)) === 'admin';
        if ($resume->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $setting = ResumeSetting::config();
        $isClean = $resume->is_watermark_removed || !$setting->watermark_enabled;

        $resume->increment('download_count');

        // Resolve candidate photo to base64 so DomPDF renders it instantly without HTTP calls
        $personal = $resume->personal_info ?? [];
        if (!empty($personal['photo_url'])) {
            $photo = $personal['photo_url'];
            if (!str_starts_with($photo, 'data:image')) {
                $path = parse_url($photo, PHP_URL_PATH);
                $cleanPath = preg_replace('#^/?(community/)?(public/)?#', '', $path);
                $abs = public_path($cleanPath);
                if (file_exists($abs)) {
                    $type = pathinfo($abs, PATHINFO_EXTENSION);
                    $data = file_get_contents($abs);
                    $personal['photo_url'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $resume->personal_info = $personal;
                }
            }
        }

        $pdf = Pdf::loadView("resume-builder.templates.{$resume->template}", [
            'resume' => $resume,
            'setting' => $setting,
            'isClean' => $isClean,
            'forPdf' => true,
        ])->setPaper('a4', 'portrait')
          ->setOption([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'isPhpEnabled' => true,
              'dpi' => 96,
              'defaultFont' => 'DejaVu Sans',
              'enable_font_subsetting' => true,
          ]);

        $fileName = \Illuminate\Support\Str::slug($resume->personal_info['full_name'] ?? 'Resume') . '-CV.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Submit Share verification (instant or screenshot upload).
     */
    public function submitShare(Request $request, Resume $resume)
    {
        if ($resume->user_id !== Auth::id()) {
            abort(403);
        }

        $setting = ResumeSetting::config();

        $validated = $request->validate([
            'verification_method' => 'required|in:instant,screenshot',
            'platform' => 'required|string|max:50',
            'post_url' => 'nullable|string|max:255',
            'proof_image' => 'nullable|image|max:5120', // max 5MB
        ]);

        if ($validated['verification_method'] === 'instant') {
            // Check if instant verification is permitted
            if ($setting->verification_mode === 'manual') {
                return response()->json([
                    'success' => false,
                    'message' => 'Instant verification is disabled. Please upload a screenshot proof of your share.',
                ], 422);
            }

            // Record verification as automatically approved
            ResumeShareVerification::create([
                'resume_id' => $resume->id,
                'user_id' => Auth::id(),
                'platform' => $validated['platform'],
                'verification_method' => 'instant',
                'status' => 'approved',
                'reviewed_at' => now(),
            ]);

            // Unlock watermark
            $resume->update([
                'is_watermark_removed' => true,
                'watermark_removed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for sharing! The REIAC watermark has been permanently removed from your resume.',
                'is_watermark_removed' => true,
            ]);
        }

        // Screenshot proof verification
        if (!$request->hasFile('proof_image')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a screenshot file showing your share.',
            ], 422);
        }

        $path = $request->file('proof_image')->store('resume-proofs', 'public');

        ResumeShareVerification::create([
            'resume_id' => $resume->id,
            'user_id' => Auth::id(),
            'platform' => $validated['platform'],
            'verification_method' => 'screenshot',
            'proof_image' => $path,
            'post_url' => $validated['post_url'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your share proof has been submitted! Our admin team will verify it shortly to remove the watermark.',
            'is_watermark_removed' => false,
            'is_pending' => true,
        ]);
    }

    /**
     * AI Endpoint: Polish Summary.
     */
    public function aiPolishSummary(Request $request, OpenAiResumeService $aiService)
    {
        $validated = $request->validate([
            'summary' => 'required|string|max:3000',
            'target_role' => 'nullable|string|max:190',
        ]);

        $polished = $aiService->polishSummary($validated['summary'], $validated['target_role'] ?? null);

        return response()->json([
            'success' => true,
            'polished_summary' => $polished,
        ]);
    }

    /**
     * AI Endpoint: Polish Experience Bullets.
     */
    public function aiPolishExperience(Request $request, OpenAiResumeService $aiService)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:3000',
            'job_title' => 'nullable|string|max:190',
            'company' => 'nullable|string|max:190',
        ]);

        $bullets = $aiService->polishExperience($validated['notes'], $validated['job_title'] ?? null, $validated['company'] ?? null);

        return response()->json([
            'success' => true,
            'bullets' => $bullets,
        ]);
    }

    /**
     * AI Endpoint: Suggest Skills.
     */
    public function aiSuggestSkills(Request $request, OpenAiResumeService $aiService)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:190',
        ]);

        $skills = $aiService->suggestSkills($validated['job_title']);

        return response()->json([
            'success' => true,
            'skills' => $skills,
        ]);
    }

    /**
     * Delete resume.
     */
    public function destroy(Resume $resume)
    {
        if ($resume->user_id !== Auth::id()) {
            abort(403);
        }

        $resume->delete();

        return redirect()->route('resume-builder.my-resumes')
            ->with('success', 'Resume deleted successfully.');
    }
}
