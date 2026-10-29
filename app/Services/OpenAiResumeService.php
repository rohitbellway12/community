<?php

namespace App\Services;

use App\Models\ResumeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiResumeService
{
    /**
     * Polish career objective / professional summary.
     */
    public function polishSummary(string $rawSummary, ?string $targetRole = null): string
    {
        $rawSummary = trim($rawSummary);
        if (empty($rawSummary)) {
            return '';
        }

        $cacheKey = 'ai_sum_' . md5($rawSummary . '|' . ($targetRole ?? ''));
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($rawSummary, $targetRole) {
            $apiKey = $this->getApiKey();
            if ($apiKey) {
                try {
                    $roleContext = $targetRole ? "Targeting the role: {$targetRole}." : "";
                    $systemPrompt = "You are a professional executive resume writer. Polish and elevate the candidate's summary/objective. Make it concise, professional, grammatically flawless, and impactful. STRICT RULE: Rely ONLY on the facts provided; NEVER invent qualifications, experience, degrees, or achievements. Return ONLY the polished paragraph.";
                    $userPrompt = "{$roleContext}\n\nCandidate's draft:\n{$rawSummary}";

                    $payload = $this->buildPayload($systemPrompt, $userPrompt, 250, 0.5);

                    $response = Http::withToken($apiKey)
                        ->timeout(15)
                        ->post('https://api.openai.com/v1/chat/completions', $payload);

                    if ($response->successful()) {
                        $content = $response->json('choices.0.message.content');
                        if (!empty($content)) {
                            return trim($content);
                        }
                    } else {
                        Log::warning('OpenAI Resume Summary Polish API Error: ' . $response->body());
                    }
                } catch (\Throwable $e) {
                    Log::warning('OpenAI Resume Summary Polish Exception: ' . $e->getMessage());
                }
            }

            // High quality deterministic fallback
            return $this->smartFallbackSummary($rawSummary, $targetRole);
        });
    }

    /**
     * Polish experience bullet points.
     */
    public function polishExperience(string $rawNotes, ?string $jobTitle = null, ?string $company = null): array
    {
        $rawNotes = trim($rawNotes);
        if (empty($rawNotes)) {
            return [];
        }

        $cacheKey = 'ai_exp_' . md5($rawNotes . '|' . ($jobTitle ?? '') . '|' . ($company ?? ''));
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($rawNotes, $jobTitle, $company) {
            $apiKey = $this->getApiKey();
            if ($apiKey) {
                try {
                    $context = trim(($jobTitle ? "Role: {$jobTitle}. " : "") . ($company ? "Company: {$company}." : ""));
                    $systemPrompt = "You are an expert corporate resume writer. Convert the provided notes into 3 to 4 impactful, professional resume bullet points. Begin each bullet point with a powerful past-tense action verb (e.g. Developed, Orchestrated, Optimized, Designed, Spearheaded). STRICT RULE: Do NOT invent fake metrics, projects, or accomplishments. Return each bullet point on a separate line without numbers or prefixes.";
                    $userPrompt = "{$context}\n\nCandidate's notes:\n{$rawNotes}";

                    $payload = $this->buildPayload($systemPrompt, $userPrompt, 300, 0.5);

                    $response = Http::withToken($apiKey)
                        ->timeout(15)
                        ->post('https://api.openai.com/v1/chat/completions', $payload);

                    if ($response->successful()) {
                        $content = $response->json('choices.0.message.content');
                        if (!empty($content)) {
                            $lines = array_filter(array_map('trim', explode("\n", $content)));
                            $cleaned = [];
                            foreach ($lines as $line) {
                                $clean = ltrim($line, "-*•0123456789. \t");
                                if (!empty($clean)) {
                                    $cleaned[] = $clean;
                                }
                            }
                            if (!empty($cleaned)) {
                                return array_values($cleaned);
                            }
                        }
                    } else {
                        Log::warning('OpenAI Resume Experience Polish API Error: ' . $response->body());
                    }
                } catch (\Throwable $e) {
                    Log::warning('OpenAI Resume Experience Polish Exception: ' . $e->getMessage());
                }
            }

            // High quality deterministic fallback
            return $this->smartFallbackExperience($rawNotes, $jobTitle, $company);
        });
    }

    /**
     * Suggest relevant skills based on job title / domain.
     */
    public function suggestSkills(string $jobTitle): array
    {
        $jobTitle = trim($jobTitle);
        if (empty($jobTitle)) {
            return [];
        }

        $cacheKey = 'ai_skills_' . md5(strtolower($jobTitle));
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($jobTitle) {
            $apiKey = $this->getApiKey();
            if ($apiKey) {
                try {
                    $systemPrompt = 'Provide a JSON list of 8 to 12 top industry skills (both hard & soft) relevant for the specified job title. Return ONLY valid JSON array of strings.';
                    $userPrompt = "Job Title: {$jobTitle}";

                    $payload = $this->buildPayload($systemPrompt, $userPrompt, 200, 0.4);

                    $response = Http::withToken($apiKey)
                        ->timeout(12)
                        ->post('https://api.openai.com/v1/chat/completions', $payload);

                    if ($response->successful()) {
                        $content = trim($response->json('choices.0.message.content'));
                        $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                        $content = preg_replace('/\s*```$/i', '', $content);
                        $decoded = json_decode($content, true);
                        if (is_array($decoded)) {
                            return array_slice($decoded, 0, 12);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('OpenAI Suggest Skills Exception: ' . $e->getMessage());
                }
            }

            return $this->smartFallbackSkills($jobTitle);
        });
    }

    /**
     * Get API key from DB settings or environment.
     */
    protected function getApiKey(): ?string
    {
        $setting = ResumeSetting::config();
        $key = trim($setting->openai_api_key ?? '');
        if (!empty($key)) {
            return $key;
        }

        $envKey = trim(env('OPENAI_API_KEY', ''));
        return !empty($envKey) ? $envKey : null;
    }

    /**
     * Get model identifier.
     */
    protected function getModel(): string
    {
        return ResumeSetting::config()->openai_model ?: 'gpt-6-luna';
    }

    /**
     * Build appropriate payload based on model capabilities.
     * GPT-6 Luna and modern reasoning models require max_completion_tokens
     * and do not support custom temperature (default 1).
     */
    protected function buildPayload(string $systemPrompt, string $userPrompt, int $maxTokens = 250, float $temperature = 0.5): array
    {
        $model = $this->getModel();
        $isLunaOrReasoning = str_contains($model, 'luna') || str_starts_with($model, 'o1') || str_starts_with($model, 'o3') || str_starts_with($model, 'gpt-6');

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];

        if ($isLunaOrReasoning) {
            $payload['max_completion_tokens'] = $maxTokens;
        } else {
            $payload['temperature'] = $temperature;
            $payload['max_tokens'] = $maxTokens;
        }

        return $payload;
    }

    /**
     * Intelligent heuristic fallback for summary.
     */
    protected function smartFallbackSummary(string $raw, ?string $role): string
    {
        $rolePrefix = $role ? "Dedicated and results-oriented {$role} with a strong foundation in collaborative problem-solving and modern practices. " : "Motivated and adaptable professional with a proven track record of initiative and dedication. ";

        // Clean out previously generated fallback prefixes & boilerplate if user clicks AI multiple times
        $cleaned = preg_replace('/^(?:Dedicated and results-oriented.*?practices\.\s*|Motivated and adaptable.*?dedication\.\s*|Experienced in\s*)+/i', '', $raw);
        $cleaned = preg_replace('/\s*Committed to driving operational efficiency.*?contributions\.?/i', '', $cleaned);
        $cleaned = ucfirst(trim($cleaned, " \t\n\r\0\x0B."));

        if (empty($cleaned) || strlen($cleaned) < 5) {
            return "{$rolePrefix}Committed to driving operational excellence, continuous learning, and delivering high-value contributions.";
        }

        if (!str_ends_with($cleaned, '.')) {
            $cleaned .= '.';
        }

        return "{$rolePrefix}Experienced in {$cleaned} Committed to driving operational efficiency, continuous learning, and delivering high-value contributions.";
    }

    /**
     * Intelligent heuristic fallback for experience bullets.
     */
    protected function smartFallbackExperience(string $raw, ?string $role, ?string $company = null): array
    {
        $lines = preg_split('/[\r\n]+|[•\*\-]|\.\s+/', $raw);
        $bullets = [];
        $actionVerbs = ['Spearheaded', 'Coordinated', 'Implemented', 'Streamlined', 'Delivered', 'Collaborated on'];
        $idx = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line, " \t\n\r\0\x0B.,;");

            // Strip any previously prepended action verbs (greedy/repeating) to prevent "Spearheaded spearheaded..."
            $cleaned = preg_replace('/^(?:\s*(?:spearheaded|coordinated|implemented|streamlined|delivered|collaborated\s+on|successfully\s+executed|applied|resolved|managed|developed|led)\s*)+/i', '', $trimmed);
            // Strip repetitive fallback trailing clauses
            $cleaned = preg_replace('/,\s*(?:ensuring high quality standards|maintaining high productivity|contributing directly to ongoing process improvements).*$/i', '', $cleaned);
            $cleaned = trim($cleaned, " \t\n\r\0\x0B.,;");

            if (strlen($cleaned) >= 10) {
                $verb = $actionVerbs[$idx % count($actionVerbs)];
                $lowerFirst = lcfirst($cleaned);
                $bullets[] = "{$verb} {$lowerFirst}, ensuring high quality standards and adherence to project objectives.";
                $idx++;
            }
        }

        if (empty($bullets)) {
            $roleLabel = ($role && strlen($role) > 2) ? $role : 'assigned';
            $companyLabel = ($company && strlen($company) > 1) ? " at {$company}" : "";

            $bullets[] = "Spearheaded key technical and operational responsibilities for the {$roleLabel} position{$companyLabel}, collaborating closely with cross-functional teams.";
            $bullets[] = "Applied modern industry best practices and quality standards to optimize daily workflows and elevate overall team velocity.";
            $bullets[] = "Implemented reliable solutions for complex challenges proactively, contributing directly to ongoing process and delivery improvements.";
        }

        // Deduplicate any repeated bullets
        $bullets = array_values(array_unique($bullets));

        return array_slice($bullets, 0, 4);
    }

    /**
     * Intelligent fallback skills based on keyword matching across major industries.
     */
    protected function smartFallbackSkills(string $title): array
    {
        $titleLower = strtolower($title);

        // 1. Software / Tech / Developers
        if (str_contains($titleLower, 'developer') || str_contains($titleLower, 'software') || str_contains($titleLower, 'coder') || str_contains($titleLower, 'programmer') || str_contains($titleLower, 'frontend') || str_contains($titleLower, 'backend') || str_contains($titleLower, 'fullstack')) {
            return ['JavaScript / TypeScript', 'PHP / Laravel', 'RESTful APIs', 'Database Design (MySQL/PostgreSQL)', 'Git Version Control', 'Agile / Scrum', 'Problem Solving', 'Unit Testing'];
        }

        // 2. Finance, Accounting & Banking
        if (str_contains($titleLower, 'account') || str_contains($titleLower, 'finance') || str_contains($titleLower, 'audit') || str_contains($titleLower, 'bank') || str_contains($titleLower, 'tax') || str_contains($titleLower, 'ca')) {
            return ['Financial Accounting', 'Auditing & Compliance', 'Taxation & GST', 'Balance Sheet Reconciliation', 'Tally / ERP', 'Advanced Excel Modeling', 'Budgeting & Forecasting', 'Financial Reporting'];
        }

        // 3. Human Resources & Recruitment
        if (str_contains($titleLower, 'hr') || str_contains($titleLower, 'human resource') || str_contains($titleLower, 'recruit') || str_contains($titleLower, 'talent') || str_contains($titleLower, 'people')) {
            return ['Talent Acquisition', 'End-to-End Recruitment', 'Employee Relations', 'Payroll Management', 'HR Policies & Compliance', 'Onboarding & Training', 'Performance Appraisal', 'Conflict Resolution'];
        }

        // 4. Healthcare, Medical & Nursing
        if (str_contains($titleLower, 'nurse') || str_contains($titleLower, 'medical') || str_contains($titleLower, 'doctor') || str_contains($titleLower, 'health') || str_contains($titleLower, 'pharma') || str_contains($titleLower, 'clinic')) {
            return ['Patient Care & Safety', 'Clinical Documentation', 'Vital Signs Monitoring', 'Emergency Response', 'Medication Administration', 'Infection Control', 'Medical Terminology', 'Healthcare Compliance'];
        }

        // 5. Teaching & Education
        if (str_contains($titleLower, 'teach') || str_contains($titleLower, 'educat') || str_contains($titleLower, 'professor') || str_contains($titleLower, 'tutor') || str_contains($titleLower, 'trainer') || str_contains($titleLower, 'faculty')) {
            return ['Curriculum Development', 'Classroom Management', 'Lesson Planning', 'Student Assessment & Evaluation', 'Educational Technology', 'Pedagogy', 'Interactive Learning', 'Communication & Mentorship'];
        }

        // 6. Design, UI/UX & Creative
        if (str_contains($titleLower, 'design') || str_contains($titleLower, 'ui') || str_contains($titleLower, 'ux') || str_contains($titleLower, 'graphic') || str_contains($titleLower, 'animat') || str_contains($titleLower, 'art')) {
            return ['Figma', 'UI/UX Design', 'Wireframing & Prototyping', 'Design Systems', 'User Research', 'Adobe Creative Suite', 'Responsive Design', 'Visual Communication'];
        }

        // 7. Marketing, Sales & Growth
        if (str_contains($titleLower, 'market') || str_contains($titleLower, 'sales') || str_contains($titleLower, 'growth') || str_contains($titleLower, 'seo') || str_contains($titleLower, 'brand') || str_contains($titleLower, 'business develop')) {
            return ['Digital Marketing', 'SEO / SEM', 'Content Strategy', 'Social Media Management', 'Google Analytics', 'CRM Systems', 'Lead Generation', 'Client Relationship & Negotiation'];
        }

        // 8. Data & Analytics
        if (str_contains($titleLower, 'data') || str_contains($titleLower, 'analyst') || str_contains($titleLower, 'bi') || str_contains($titleLower, 'business intelligence')) {
            return ['Data Analysis', 'SQL Queries', 'Python / R', 'Data Visualization (PowerBI / Tableau)', 'Excel Modeling', 'Statistical Analysis', 'Critical Thinking', 'Business Intelligence'];
        }

        // 9. Civil, Mechanical & Core Engineering
        if (str_contains($titleLower, 'civil') || str_contains($titleLower, 'mechanical') || str_contains($titleLower, 'electrical') || str_contains($titleLower, 'construction') || str_contains($titleLower, 'architect')) {
            return ['AutoCAD / SolidWorks', 'Site Inspection & Supervision', 'Project Planning & Estimation', 'Quality Assurance & Safety', 'Structural Analysis', 'Vendor Management', 'Technical Drawing', 'Compliance & Codes'];
        }

        // 10. Customer Support & Operations
        if (str_contains($titleLower, 'support') || str_contains($titleLower, 'customer') || str_contains($titleLower, 'client') || str_contains($titleLower, 'service') || str_contains($titleLower, 'call center') || str_contains($titleLower, 'bpo')) {
            return ['Customer Relationship Management (CRM)', 'Helpdesk / Ticketing Systems', 'Active Listening & Empathy', 'Conflict Resolution', 'Service Level Agreement (SLA) Adherence', 'Client Retention', 'Problem Solving', 'Multi-channel Support'];
        }

        // 11. Hospitality, Hotel & Culinary
        if (str_contains($titleLower, 'hotel') || str_contains($titleLower, 'chef') || str_contains($titleLower, 'cook') || str_contains($titleLower, 'hospitality') || str_contains($titleLower, 'restaurant') || str_contains($titleLower, 'food')) {
            return ['Food Safety & Hygiene (HACCP)', 'Menu Planning & Execution', 'Guest Relations & Hospitality', 'Inventory & Cost Control', 'Team Leadership', 'Fine Dining Standards', 'Kitchen Operations', 'Customer Satisfaction'];
        }

        // 12. Supply Chain, Logistics & Warehouse
        if (str_contains($titleLower, 'logistics') || str_contains($titleLower, 'supply') || str_contains($titleLower, 'warehouse') || str_contains($titleLower, 'inventory') || str_contains($titleLower, 'procurement')) {
            return ['Supply Chain Optimization', 'Inventory Control & Auditing', 'Logistics & Dispatch Coordination', 'Vendor Negotiation', 'Warehouse Management Systems (WMS)', 'Route Planning', 'Quality Inspection', 'Procurement'];
        }

        // Default General Professional Competencies
        return ['Team Leadership', 'Project Management', 'Cross-functional Collaboration', 'Analytical Problem Solving', 'Strategic Planning', 'Written & Verbal Communication', 'Time Management', 'Process Optimization'];
    }
}
