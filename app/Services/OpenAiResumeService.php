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

                    $response = Http::withToken($apiKey)
                        ->timeout(15)
                        ->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->getModel(),
                            'messages' => [
                                ['role' => 'system', 'content' => $systemPrompt],
                                ['role' => 'user', 'content' => "{$roleContext}\n\nCandidate's draft:\n{$rawSummary}"],
                            ],
                            'temperature' => 0.5,
                            'max_tokens' => 250,
                        ]);

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

                    $response = Http::withToken($apiKey)
                        ->timeout(15)
                        ->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->getModel(),
                            'messages' => [
                                ['role' => 'system', 'content' => $systemPrompt],
                                ['role' => 'user', 'content' => "{$context}\n\nCandidate's notes:\n{$rawNotes}"],
                            ],
                            'temperature' => 0.5,
                            'max_tokens' => 300,
                        ]);

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
            return $this->smartFallbackExperience($rawNotes, $jobTitle);
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
                    $response = Http::withToken($apiKey)
                        ->timeout(12)
                        ->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->getModel(),
                            'messages' => [
                                ['role' => 'system', 'content' => 'Provide a JSON list of 8 to 12 top industry skills (both hard & soft) relevant for the specified job title. Return ONLY valid JSON array of strings.'],
                                ['role' => 'user', 'content' => "Job Title: {$jobTitle}"],
                            ],
                            'temperature' => 0.4,
                            'max_tokens' => 200,
                        ]);

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
        return ResumeSetting::config()->openai_model ?: 'gpt-4o-mini';
    }

    /**
     * Intelligent heuristic fallback for summary.
     */
    protected function smartFallbackSummary(string $raw, ?string $role): string
    {
        $rolePrefix = $role ? "Dedicated and results-oriented {$role} with a strong foundation in collaborative problem-solving and modern practices. " : "Motivated and adaptable professional with a proven track record of initiative and dedication. ";
        
        $cleaned = ucfirst(trim($raw, " \t\n\r\0\x0B."));
        if (!str_ends_with($cleaned, '.')) {
            $cleaned .= '.';
        }

        return "{$rolePrefix}Experienced in {$cleaned} Committed to driving operational efficiency, continuous learning, and delivering high-value contributions.";
    }

    /**
     * Intelligent heuristic fallback for experience bullets.
     */
    protected function smartFallbackExperience(string $raw, ?string $role): array
    {
        $lines = preg_split('/[\r\n]+|[•\*\-]|\.\s+/', $raw);
        $bullets = [];
        $actionVerbs = ['Spearheaded', 'Coordinated', 'Implemented', 'Streamlined', 'Delivered', 'Collaborated on'];
        $idx = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line, " \t\n\r\0\x0B.,;");
            if (strlen($trimmed) >= 10) {
                $verb = $actionVerbs[$idx % count($actionVerbs)];
                $lowerFirst = lcfirst($trimmed);
                $bullets[] = "{$verb} {$lowerFirst}, ensuring high quality standards and adherence to project objectives.";
                $idx++;
            }
        }

        if (empty($bullets)) {
            $bullets[] = "Successfully executed key responsibilities for the " . ($role ?: 'assigned') . " position while collaborating effectively across teams.";
            $bullets[] = "Applied industry best practices to streamline daily workflows and maintain high productivity standards.";
            $bullets[] = "Resolved complex challenges proactively and contributed to ongoing process improvements.";
        }

        return array_slice($bullets, 0, 4);
    }

    /**
     * Intelligent fallback skills based on keyword matching.
     */
    protected function smartFallbackSkills(string $title): array
    {
        $titleLower = strtolower($title);

        if (str_contains($titleLower, 'developer') || str_contains($titleLower, 'software') || str_contains($titleLower, 'engineer')) {
            return ['JavaScript / TypeScript', 'PHP / Laravel', 'RESTful APIs', 'Database Design (MySQL/PostgreSQL)', 'Git Version Control', 'Agile / Scrum', 'Problem Solving', 'Unit Testing'];
        }

        if (str_contains($titleLower, 'design') || str_contains($titleLower, 'ui') || str_contains($titleLower, 'ux')) {
            return ['Figma', 'UI/UX Design', 'Wireframing & Prototyping', 'Design Systems', 'User Research', 'Adobe Creative Suite', 'Responsive Design', 'Visual Communication'];
        }

        if (str_contains($titleLower, 'market') || str_contains($titleLower, 'sales') || str_contains($titleLower, 'growth')) {
            return ['Digital Marketing', 'SEO / SEM', 'Content Strategy', 'Social Media Management', 'Google Analytics', 'CRM Systems', 'Lead Generation', 'Communication & Negotiation'];
        }

        if (str_contains($titleLower, 'data') || str_contains($titleLower, 'analyst')) {
            return ['Data Analysis', 'SQL', 'Python / R', 'Data Visualization (PowerBI / Tableau)', 'Excel Modeling', 'Statistical Analysis', 'Critical Thinking', 'Business Intelligence'];
        }

        return ['Team Leadership', 'Project Management', 'Cross-functional Collaboration', 'Analytical Problem Solving', 'Strategic Planning', 'Written & Verbal Communication', 'Time Management', 'Process Optimization'];
    }
}
