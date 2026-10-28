<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\ResumeSetting;
use App\Models\ResumeShareVerification;
use App\Services\OpenAiResumeService;
use App\Services\ResumeSampleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResumeApiController extends Controller
{
    use ApiResponse;

    /**
     * Get list of available resume templates with metadata.
     * GET /api/v1/resume/templates
     */
    public function templates(): JsonResponse
    {
        $templates = [
            [
                'id' => 'korean_standard',
                'name' => 'Korean Standard Application',
                'korean_name' => '표준 입사지원서 (이력서 & 자기소개서)',
                'badge' => 'Official Standard',
                'description' => 'Official tabular Korean format with 3x4 photo upload, military status, language test scores (TOEIC/JLPT), and 4-part self-introduction (자기소개서).',
                'features' => ['3x4 Photo Upload', 'Official Korean Table', '4-Part Statement', 'Bilingual KR/EN'],
                'steps_count' => 7,
                'popular' => false,
                'category' => 'korean',
                'sample_url' => route('api.v1.resume.sample', ['template' => 'korean_standard']),
                'web_preview_url' => url('/resume-builder/sample/korean_standard?embed=1'),
            ],
            [
                'id' => 'modern',
                'name' => 'Modern Professional',
                'korean_name' => '모던 프로페셔널 ATS',
                'badge' => 'Most Popular',
                'description' => 'Clean dual-accent layout with navy typography, subtle gold badges, and high-density executive balance optimized for ATS systems.',
                'features' => ['Balanced 1-Page Layout', 'Tech & Corporate', 'Candidate Photo Frame', '98%+ ATS Pass Rate'],
                'steps_count' => 7,
                'popular' => true,
                'category' => 'ats',
                'sample_url' => route('api.v1.resume.sample', ['template' => 'modern']),
                'web_preview_url' => url('/resume-builder/sample/modern?embed=1'),
            ],
            [
                'id' => 'classic',
                'name' => 'Executive Classic ATS',
                'korean_name' => '이그제큐티브 클래식 (Harvard/Wharton)',
                'badge' => '100% ATS Safe',
                'description' => 'Single-column Harvard/Wharton standard layout strictly compliant with automated Applicant Tracking Systems. Text-first design.',
                'features' => ['Harvard Standard', 'Fortune 500 Safe', 'Serif Executive Typography', 'Text-First Compliance'],
                'steps_count' => 7,
                'popular' => false,
                'category' => 'ats',
                'sample_url' => route('api.v1.resume.sample', ['template' => 'classic']),
                'web_preview_url' => url('/resume-builder/sample/classic?embed=1'),
            ],
            [
                'id' => 'creative',
                'name' => 'Creative & Impact',
                'korean_name' => '크리에이티브 임팩트 (2-Column)',
                'badge' => 'High Impact',
                'description' => 'Bold header banner with amber highlights, project showcase badges, and 2-column sidebar layout perfect for tech & startups.',
                'features' => ['Dark Header Banner', '2-Column Modern Grid', 'Project Showcase', 'Visual Tech Stack Tags'],
                'steps_count' => 7,
                'popular' => false,
                'category' => 'creative',
                'sample_url' => route('api.v1.resume.sample', ['template' => 'creative']),
                'web_preview_url' => url('/resume-builder/sample/creative?embed=1'),
            ],
        ];

        return $this->successResponse($templates, 'Resume templates retrieved successfully.');
    }

    /**
     * Get sample dummy data for a template.
     * GET /api/v1/resume/templates/{template}/sample?lang=ko|en
     */
    public function templateSample(Request $request, string $template = 'modern'): JsonResponse
    {
        $validTemplates = ['modern', 'classic', 'creative', 'korean_standard'];
        if (!in_array($template, $validTemplates)) {
            $template = 'korean_standard';
        }

        $lang = $request->query('lang', $template === 'korean_standard' ? 'ko' : 'en');
        $sampleResume = ResumeSampleService::getSampleResume($template, $lang);

        return $this->successResponse([
            'template' => $template,
            'language' => $lang,
            'sample_data' => $sampleResume,
            'web_preview_url' => url("/resume-builder/sample/{$template}?embed=1&lang={$lang}"),
        ], 'Sample data retrieved successfully.');
    }

    /**
     * Get global resume configuration (watermark, settings).
     * GET /api/v1/resume/config
     */
    public function config(): JsonResponse
    {
        $setting = ResumeSetting::config();

        return $this->successResponse([
            'watermark_enabled' => (bool) $setting->watermark_enabled,
            'watermark_text' => $setting->watermark_text ?: 'REIAC COMMUNITY',
            'share_channels' => ['kakao', 'whatsapp', 'linkedin', 'facebook', 'twitter', 'link_copy'],
            'max_photo_size_mb' => 5,
            'supported_languages' => [
                ['code' => 'en', 'label' => 'English'],
                ['code' => 'ko', 'label' => '한국어 (Korean)'],
            ],
        ], 'Resume configuration retrieved.');
    }

    /**
     * Get prefilled initial data for creating a new resume for authenticated user.
     * GET /api/v1/resumes/initial-data?template=korean_standard&language=ko
     */
    public function initialData(Request $request): JsonResponse
    {
        $user = $request->user();
        $template = $request->query('template', 'korean_standard');
        $language = $request->query('language', $template === 'korean_standard' ? 'ko' : 'en');

        $initialData = [
            'id' => null,
            'title' => 'My Professional Resume',
            'template' => in_array($template, ['modern', 'classic', 'creative', 'korean_standard']) ? $template : 'korean_standard',
            'language' => $language,
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
                'declaration_date' => date($language === 'ko' ? 'Y년 m월 d일' : 'F d, Y'),
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
                'status' => '군필 (Completed)',
                'branch' => '육군 (Army)',
                'rank' => '병장 (Sergeant)',
                'period' => '',
                'discharge_reason' => '만기전역',
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

        return $this->successResponse($initialData, 'Initial resume structure generated.');
    }

    /**
     * List user's resumes.
     * GET /api/v1/resumes
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);

        $resumes = Resume::where('user_id', $user->id)
            ->with(['latestVerification'])
            ->latest()
            ->paginate($perPage);

        $setting = ResumeSetting::config();

        // Transform collection to include preview and download URLs
        $items = collect($resumes->items())->map(function ($r) use ($setting) {
            $isClean = $r->is_watermark_removed || !$setting->watermark_enabled;
            return [
                'id' => $r->id,
                'title' => $r->title,
                'template' => $r->template,
                'language' => $r->language,
                'candidate_name' => $r->personal_info['full_name'] ?? 'Candidate',
                'target_role' => $r->personal_info['job_title'] ?? '',
                'photo_url' => $r->personal_info['photo_url'] ?? null,
                'is_watermark_removed' => (bool) $r->is_watermark_removed,
                'is_clean' => $isClean,
                'download_count' => (int) $r->download_count,
                'created_at' => $r->created_at?->toIso8601String(),
                'updated_at' => $r->updated_at?->toIso8601String(),
                'preview_url' => url("/api/v1/resumes/{$r->id}/preview"),
                'download_pdf_url' => url("/api/v1/resumes/{$r->id}/download"),
                'verification_status' => $r->latestVerification?->status ?? null,
            ];
        });

        $paginatedData = [
            'data' => $items,
            'current_page' => $resumes->currentPage(),
            'last_page' => $resumes->lastPage(),
            'per_page' => $resumes->perPage(),
            'total' => $resumes->total(),
        ];

        return $this->successResponse($paginatedData, 'Resumes retrieved successfully.');
    }

    /**
     * Get a single resume by ID.
     * GET /api/v1/resumes/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $resume = Resume::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['latestVerification'])
            ->first();

        if (!$resume) {
            return $this->errorResponse('Resume not found or unauthorized access.', 404);
        }

        $setting = ResumeSetting::config();
        $isClean = $resume->is_watermark_removed || !$setting->watermark_enabled;

        $response = $resume->toArray();
        $response['is_clean'] = $isClean;
        $response['preview_url'] = url("/api/v1/resumes/{$resume->id}/preview");
        $response['download_pdf_url'] = url("/api/v1/resumes/{$resume->id}/download");

        return $this->successResponse($response, 'Resume details retrieved.');
    }

    /**
     * Create or update a resume (Step-by-step or full save).
     * POST /api/v1/resumes
     */
    public function save(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
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
            'personal_info.declaration_date' => 'nullable|string|max:100',
            'personal_info.signature_name' => 'nullable|string|max:190',
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

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $validated = $validator->validated();

        // Ensure experience rawNotes and description consistency
        if (!empty($validated['experience']) && is_array($validated['experience'])) {
            foreach ($validated['experience'] as &$exp) {
                if (!empty($exp['rawNotes']) && empty($exp['description'])) {
                    $exp['description'] = $exp['rawNotes'];
                }
            }
        }

        $resumeId = $request->input('id');

        if ($resumeId) {
            $resume = Resume::where('id', $resumeId)->where('user_id', $user->id)->first();
            if (!$resume) {
                return $this->errorResponse('Resume not found or unauthorized.', 404);
            }
            $resume->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            $resume = Resume::create($validated);
        }

        $setting = ResumeSetting::config();
        $isClean = $resume->is_watermark_removed || !$setting->watermark_enabled;

        return $this->successResponse([
            'resume_id' => $resume->id,
            'title' => $resume->title,
            'template' => $resume->template,
            'language' => $resume->language,
            'is_clean' => $isClean,
            'preview_url' => url("/api/v1/resumes/{$resume->id}/preview"),
            'download_pdf_url' => url("/api/v1/resumes/{$resume->id}/download"),
        ], 'Resume saved successfully!');
    }

    /**
     * Upload candidate portrait photo.
     * POST /api/v1/resume/upload-photo
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Invalid image file', 422, $validator->errors());
        }

        $path = $request->file('photo')->store('resumes/photos', 'public');
        $url = asset('storage/' . $path);

        return $this->successResponse([
            'url' => $url,
            'path' => $path,
        ], 'Candidate photo uploaded successfully!');
    }

    /**
     * Delete a resume.
     * DELETE /api/v1/resumes/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $resume = Resume::where('id', $id)->where('user_id', $user->id)->first();

        if (!$resume) {
            return $this->errorResponse('Resume not found or unauthorized.', 404);
        }

        $resume->delete();

        return $this->successResponse(null, 'Resume deleted successfully.');
    }

    /**
     * AI: Polish Professional Summary or Self-Introduction Statement.
     * POST /api/v1/resume/ai/polish-summary
     */
    public function aiPolishSummary(Request $request, OpenAiResumeService $aiService): JsonResponse
    {
        $summary = $request->input('summary', '');
        $targetRole = $request->input('target_role') ?: ($request->input('job_title') ?: 'Professional');

        if (empty(trim($summary))) {
            return $this->errorResponse('Summary text is required for AI enhancement.', 422);
        }

        $polished = $aiService->polishSummary($summary, $targetRole);

        return $this->successResponse([
            'polished_summary' => $polished,
            'original_summary' => $summary,
            'target_role' => $targetRole,
        ], 'Summary polished successfully.');
    }

    /**
     * AI: Enhance Work Experience Notes into High-Impact Bullets.
     * POST /api/v1/resume/ai/enhance-bullets
     */
    public function aiPolishExperience(Request $request, OpenAiResumeService $aiService): JsonResponse
    {
        $notes = $request->input('notes', '');
        $role = $request->input('role') ?: ($request->input('job_title') ?: ($request->input('target_role') ?: 'Professional'));
        $company = $request->input('company', '');

        if (empty(trim($notes))) {
            return $this->errorResponse('Experience notes are required for AI enhancement.', 422);
        }

        $bullets = $aiService->polishExperience($notes, $role, $company);

        return $this->successResponse([
            'bullets' => $bullets,
            'raw_notes' => $notes,
            'role' => $role,
        ], 'Experience bullets generated successfully.');
    }

    /**
     * AI: Suggest Relevant Skills for Target Job.
     * POST /api/v1/resume/ai/suggest-skills
     */
    public function aiSuggestSkills(Request $request, OpenAiResumeService $aiService): JsonResponse
    {
        $role = $request->input('target_role') ?: ($request->input('job_title') ?: ($request->input('role') ?: 'Professional'));
        $existing = $request->input('existing_skills', []);

        if (!is_array($existing)) {
            $existing = [];
        }

        $suggested = $aiService->suggestSkills($role);
        if (!empty($existing)) {
            $suggested = array_values(array_diff($suggested, $existing));
        }

        return $this->successResponse([
            'skills' => $suggested,
            'target_role' => $role,
        ], 'Skills suggested successfully.');
    }

    /**
     * Submit community share to remove watermark.
     * POST /api/v1/resumes/{id}/submit-share
     */
    public function submitShare(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $resume = Resume::where('id', $id)->where('user_id', $user->id)->first();

        if (!$resume) {
            return $this->errorResponse('Resume not found or unauthorized.', 404);
        }

        $validator = Validator::make($request->all(), [
            'channel' => 'required|string|in:kakao,whatsapp,linkedin,facebook,twitter,link_copy',
            'share_link' => 'nullable|string|url|max:500',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $channel = $request->input('channel');
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('resumes/share_proofs', 'public');
        }

        // Auto-approve link_copy, kakao, whatsapp for instant frictionless mobile user experience
        $instantChannels = ['link_copy', 'kakao', 'whatsapp'];
        $isInstant = in_array($channel, $instantChannels);

        $verification = ResumeShareVerification::create([
            'resume_id' => $resume->id,
            'user_id' => $user->id,
            'channel' => $channel,
            'share_link' => $request->input('share_link'),
            'screenshot_path' => $screenshotPath,
            'status' => $isInstant ? 'approved' : 'pending',
            'verified_at' => $isInstant ? now() : null,
        ]);

        if ($isInstant) {
            $resume->update([
                'is_watermark_removed' => true,
                'watermark_removed_at' => now(),
            ]);
        }

        return $this->successResponse([
            'verification_id' => $verification->id,
            'status' => $verification->status,
            'is_watermark_removed' => (bool) $resume->is_watermark_removed,
            'preview_url' => url("/api/v1/resumes/{$resume->id}/preview"),
            'download_pdf_url' => url("/api/v1/resumes/{$resume->id}/download"),
        ], $isInstant ? 'Watermark successfully removed!' : 'Proof submitted for verification.');
    }

    /**
     * Preview resume via API (HTML embed for mobile WebView or PDF stream).
     * GET /api/v1/resumes/{id}/preview
     */
    public function preview(Request $request, $id)
    {
        // Authenticate via Sanctum Bearer token OR ?token query parameter
        $user = Auth::guard('sanctum')->user();
        if (!$user && $request->has('token')) {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($tokenModel) {
                $user = $tokenModel->tokenable;
            }
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $resume = Resume::where('id', $id)->first();
        if (!$resume) {
            return response()->json(['success' => false, 'message' => 'Resume not found.'], 404);
        }

        $role = is_object($user->role) ? $user->role->value : $user->role;
        $isAdmin = $role && trim(strtolower($role)) === 'admin';
        if ($resume->user_id !== $user->id && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $setting = ResumeSetting::config();
        $isClean = $resume->is_watermark_removed || !$setting->watermark_enabled;

        if ($request->query('format') === 'pdf' || $request->header('Accept') === 'application/pdf') {
            return $this->downloadPdf($request, $id);
        }

        return view("resume-builder.templates.{$resume->template}", [
            'resume' => $resume,
            'setting' => $setting,
            'isClean' => $isClean,
            'forPdf' => false,
            'isEmbed' => true,
        ]);
    }

    /**
     * Download resume PDF directly via API (Binary PDF stream).
     * GET /api/v1/resumes/{id}/download
     */
    public function downloadPdf(Request $request, $id)
    {
        // Authenticate via Sanctum Bearer token OR ?token query parameter
        $user = Auth::guard('sanctum')->user();
        if (!$user && $request->has('token')) {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($tokenModel) {
                $user = $tokenModel->tokenable;
            }
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $resume = Resume::where('id', $id)->first();
        if (!$resume) {
            return response()->json(['success' => false, 'message' => 'Resume not found.'], 404);
        }

        $role = is_object($user->role) ? $user->role->value : $user->role;
        $isAdmin = $role && trim(strtolower($role)) === 'admin';
        if ($resume->user_id !== $user->id && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("resume-builder.templates.{$resume->template}", [
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
}

