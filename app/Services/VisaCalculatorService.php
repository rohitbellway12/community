<?php

namespace App\Services;

class VisaCalculatorService
{
    public const PASSING_SCORE = 80;
    public const MAX_BONUS_POINTS = 40;
    public const MAX_EXPECTED_SCORE = 150;

    /**
     * Get all categories, fields, and options with points.
     */
    public function getOptions(): array
    {
        return [
            'meta' => [
                'title'              => 'South Korea F-2-7 Visa Points Calculator',
                'title_ko'           => '대한민국 F-2-7 점수제 거주비자 계산기',
                'description'        => 'Assess your eligibility for the F-2-7 Points-Based Residence Visa. A minimum of 80 points is required to qualify.',
                'passing_score'      => self::PASSING_SCORE,
                'max_bonus_points'   => self::MAX_BONUS_POINTS,
                'max_expected_score' => self::MAX_EXPECTED_SCORE,
            ],
            'categories' => [
                'core' => [
                    'id'          => 'core',
                    'step'        => 1,
                    'title'       => 'Core Criteria',
                    'title_ko'    => '기본 평가 항목',
                    'description' => 'Essential parameters for the points assessment',
                    'fields'      => [
                        'age' => [
                            'key'         => 'age',
                            'label'       => 'Age',
                            'label_ko'    => '나이',
                            'required'    => true,
                            'placeholder' => 'Select Age Range',
                            'options'     => [
                                ['value' => '18-24', 'label' => '18 – 24 years', 'points' => 23],
                                ['value' => '25-29', 'label' => '25 – 29 years', 'points' => 25],
                                ['value' => '30-34', 'label' => '30 – 34 years', 'points' => 23],
                                ['value' => '35-39', 'label' => '35 – 39 years', 'points' => 20],
                                ['value' => '40-44', 'label' => '40 – 44 years', 'points' => 12],
                                ['value' => '45-50', 'label' => '45 – 50 years', 'points' => 8],
                                ['value' => '51+',   'label' => '51+ years',     'points' => 3],
                            ],
                        ],
                        'education' => [
                            'key'         => 'education',
                            'label'       => 'Education',
                            'label_ko'    => '학력',
                            'required'    => true,
                            'placeholder' => 'Select Highest Education',
                            'options'     => [
                                ['value' => 'phd-stem',          'label' => 'PhD - STEM Field or Dual Degree',    'points' => 25],
                                ['value' => 'phd-non-stem',      'label' => 'PhD - Non-STEM Field',              'points' => 20],
                                ['value' => 'master-stem',       'label' => 'Master - STEM Field or Dual Degree', 'points' => 20],
                                ['value' => 'master-non-stem',   'label' => 'Master - Non-STEM Field',           'points' => 17],
                                ['value' => 'bachelor-stem',     'label' => 'Bachelor - STEM Field or Dual Degree','points' => 17],
                                ['value' => 'bachelor-non-stem', 'label' => 'Bachelor - Non-STEM Field',         'points' => 15],
                                ['value' => 'associate-stem',    'label' => 'Associate - STEM Field',            'points' => 15],
                                ['value' => 'associate-non-stem','label' => 'Associate - Non-STEM Field',        'points' => 10],
                            ],
                        ],
                        'qualifications' => [
                            'key'         => 'qualifications',
                            'label'       => 'Basic Competence (Language)',
                            'label_ko'    => '기본소양 (한국어 능력)',
                            'required'    => true,
                            'placeholder' => 'Select Language Level',
                            'options'     => [
                                ['value' => 'level-5', 'label' => 'TOPIK Level 5+ or KIIP Level 5 Completed', 'points' => 20],
                                ['value' => 'level-4', 'label' => 'TOPIK Level 4 or KIIP Level 4 Completed',  'points' => 15],
                                ['value' => 'level-3', 'label' => 'TOPIK Level 3 or KIIP Level 3 Completed',  'points' => 10],
                                ['value' => 'level-2', 'label' => 'TOPIK Level 2 or KIIP Level 2 Completed',  'points' => 5],
                                ['value' => 'level-1', 'label' => 'TOPIK Level 1 or KIIP Level 1 Completed',  'points' => 3],
                                ['value' => 'none',    'label' => 'None / No Certification',                  'points' => 0],
                            ],
                        ],
                        'income' => [
                            'key'         => 'income',
                            'label'       => 'Annual Income',
                            'label_ko'    => '연간소득',
                            'required'    => true,
                            'placeholder' => 'Select Annual Income Range',
                            'options'     => [
                                ['value' => '100m',       'label' => 'KRW 100 Million+',        'points' => 60],
                                ['value' => '90m-100m',   'label' => 'KRW 90M – 100M',          'points' => 58],
                                ['value' => '80m-90m',    'label' => 'KRW 80M – 90M',           'points' => 56],
                                ['value' => '70m-80m',    'label' => 'KRW 70M – 80M',           'points' => 53],
                                ['value' => '60m-70m',    'label' => 'KRW 60M – 70M',           'points' => 50],
                                ['value' => '50m-60m',    'label' => 'KRW 50M – 60M',           'points' => 45],
                                ['value' => '40m-50m',    'label' => 'KRW 40M – 50M',           'points' => 40],
                                ['value' => '30m-40m',    'label' => 'KRW 30M – 40M',           'points' => 30],
                                ['value' => 'min-30m',    'label' => 'Minimum Wage – 30M',      'points' => 10],
                                ['value' => 'below-min',  'label' => 'Below Minimum Wage',      'points' => 0],
                            ],
                        ],
                    ],
                ],
                'bonus' => [
                    'id'          => 'bonus',
                    'step'        => 2,
                    'title'       => 'Bonus Criteria',
                    'title_ko'    => '가점 항목',
                    'description' => 'Additional elements to boost your score (Capped at 40 points)',
                    'max_cap'     => self::MAX_BONUS_POINTS,
                    'fields'      => [
                        'bonus_allied' => [
                            'key'         => 'bonus_allied',
                            'label'       => 'Allied Country / Recommendation',
                            'label_ko'    => '참전국 우수인재 / 정부추천',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',           'label' => 'Not Applicable',                                  'points' => 0],
                                ['value' => 'outstanding',    'label' => 'Outstanding Talent / Korean War Allied Country', 'points' => 20],
                                ['value' => 'recommendation', 'label' => 'Central Government Recommendation',               'points' => 20],
                                ['value' => 'both',           'label' => 'Allied Country Talent + Gov Recommendation',     'points' => 40],
                            ],
                        ],
                        'bonus_degree' => [
                            'key'         => 'bonus_degree',
                            'label'       => 'Degree Bonus (Top Univ / Korea)',
                            'label_ko'    => '학위 가점 (세계우수대학 / 국내대학)',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',            'label' => 'Not Applicable',                               'points' => 0],
                                ['value' => 'phd-top',         'label' => 'PhD from Top Global University',               'points' => 30],
                                ['value' => 'phd-korea',       'label' => 'PhD from a Korean University',                 'points' => 10],
                                ['value' => 'master-top',      'label' => 'Master\'s from Top Global University',          'points' => 20],
                                ['value' => 'master-korea',    'label' => 'Master\'s from a Korean University',            'points' => 7],
                                ['value' => 'bachelor-top',    'label' => 'Bachelor\'s from Top Global University',        'points' => 15],
                                ['value' => 'bachelor-korea',  'label' => 'Bachelor\'s from a Korean University',          'points' => 5],
                            ],
                        ],
                        'bonus_kiip' => [
                            'key'         => 'bonus_kiip',
                            'label'       => 'KIIP Bonus',
                            'label_ko'    => '사회통합프로그램 가점',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',      'label' => 'Not Applicable',                     'points' => 0],
                                ['value' => 'completed', 'label' => 'Completed KIIP Level 5 or higher',   'points' => 10],
                            ],
                        ],
                        'bonus_volunteer' => [
                            'key'         => 'bonus_volunteer',
                            'label'       => 'Volunteer Service',
                            'label_ko'    => '사회봉사활동',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',  'label' => 'Not Applicable',                    'points' => 0],
                                ['value' => '3yr',   'label' => '3+ Years of Volunteer Service',    'points' => 7],
                                ['value' => '2-3yr', 'label' => '2 – 3 Years of Volunteer Service', 'points' => 5],
                                ['value' => '1-2yr', 'label' => '1 – 2 Years of Volunteer Service', 'points' => 1],
                            ],
                        ],
                    ],
                ],
                'deductions' => [
                    'id'          => 'deductions',
                    'step'        => 3,
                    'title'       => 'Deductions (Minus Points)',
                    'title_ko'    => '감점 항목',
                    'description' => 'Penalties for immigration or criminal violations',
                    'fields'      => [
                        'deduction_immigration' => [
                            'key'         => 'deduction_immigration',
                            'label'       => 'Immigration Violations',
                            'label_ko'    => '출입국관리법 위반',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',           'label' => 'Not Applicable',                                   'points' => 0],
                                ['value' => 'fine-3m',        'label' => 'Fine ≥ KRW 3 Million or Departure Order',          'points' => -30],
                                ['value' => 'fine-2m-3m',     'label' => 'Fine KRW 2 Million – 3 Million',                   'points' => -20],
                                ['value' => 'fine-under-2m',  'label' => 'Fine < KRW 2 Million',                             'points' => -10],
                            ],
                        ],
                        'deduction_criminal' => [
                            'key'         => 'deduction_criminal',
                            'label'       => 'Criminal Record',
                            'label_ko'    => '형사처벌 (기타 법률 위반)',
                            'required'    => false,
                            'options'     => [
                                ['value' => 'none',           'label' => 'Not Applicable',                    'points' => 0],
                                ['value' => 'fine-3m',        'label' => 'Fine ≥ KRW 3 Million',             'points' => -40],
                                ['value' => 'fine-1m-3m',     'label' => 'Fine KRW 1 Million – 3 Million',    'points' => -30],
                                ['value' => 'fine-0.5m-1m',   'label' => 'Fine KRW 0.5 Million – 1 Million',  'points' => -20],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Calculate points based on submitted inputs.
     */
    public function calculate(array $input): array
    {
        $pointsMap = [
            'age' => [
                '18-24' => 23, '25-29' => 25, '30-34' => 23, '35-39' => 20,
                '40-44' => 12, '45-50' => 8,  '51+'   => 3,
            ],
            'education' => [
                'phd-stem' => 25, 'phd-non-stem' => 20, 'master-stem' => 20, 'master-non-stem' => 17,
                'bachelor-stem' => 17, 'bachelor-non-stem' => 15, 'associate-stem' => 15, 'associate-non-stem' => 10,
            ],
            'qualifications' => [
                'level-5' => 20, 'level-4' => 15, 'level-3' => 10, 'level-2' => 5, 'level-1' => 3, 'none' => 0,
            ],
            'income' => [
                '100m' => 60, '90m-100m' => 58, '80m-90m' => 56, '70m-80m' => 53,
                '60m-70m' => 50, '50m-60m' => 45, '40m-50m' => 40, '30m-40m' => 30,
                'min-30m' => 10, 'below-min' => 0,
            ],
            'bonus_allied' => [
                'none' => 0, 'outstanding' => 20, 'recommendation' => 20, 'both' => 40,
            ],
            'bonus_degree' => [
                'none' => 0, 'phd-top' => 30, 'phd-korea' => 10, 'master-top' => 20,
                'master-korea' => 7, 'bachelor-top' => 15, 'bachelor-korea' => 5,
            ],
            'bonus_kiip' => [
                'none' => 0, 'completed' => 10,
            ],
            'bonus_volunteer' => [
                'none' => 0, '3yr' => 7, '2-3yr' => 5, '1-2yr' => 1,
            ],
            'deduction_immigration' => [
                'none' => 0, 'fine-3m' => -30, 'fine-2m-3m' => -20, 'fine-under-2m' => -10,
            ],
            'deduction_criminal' => [
                'none' => 0, 'fine-3m' => -40, 'fine-1m-3m' => -30, 'fine-0.5m-1m' => -20,
            ],
        ];

        // Normalise alternative parameter keys (e.g. bonus-allied to bonus_allied)
        $normalized = [];
        foreach ($input as $k => $v) {
            $normalizedKey = str_replace('-', '_', $k);
            $normalized[$normalizedKey] = (string) $v;
        }

        // 1. Core points
        $ageKey = $normalized['age'] ?? null;
        $eduKey = $normalized['education'] ?? null;
        $qualKey = $normalized['qualifications'] ?? null;
        $incKey = $normalized['income'] ?? null;

        $isComplete = !empty($ageKey) && !empty($eduKey) && !empty($qualKey) && !empty($incKey);

        $agePts = $pointsMap['age'][$ageKey] ?? 0;
        $eduPts = $pointsMap['education'][$eduKey] ?? 0;
        $qualPts = $pointsMap['qualifications'][$qualKey] ?? 0;
        $incPts = $pointsMap['income'][$incKey] ?? 0;

        $basePoints = $agePts + $eduPts + $qualPts + $incPts;

        // 2. Bonus points
        $bonAllied = $pointsMap['bonus_allied'][$normalized['bonus_allied'] ?? 'none'] ?? 0;
        $bonDegree = $pointsMap['bonus_degree'][$normalized['bonus_degree'] ?? 'none'] ?? 0;
        $bonKiip = $pointsMap['bonus_kiip'][$normalized['bonus_kiip'] ?? 'none'] ?? 0;
        $bonVolunteer = $pointsMap['bonus_volunteer'][$normalized['bonus_volunteer'] ?? 'none'] ?? 0;

        $rawBonus = $bonAllied + $bonDegree + $bonKiip + $bonVolunteer;
        $cappedBonus = min($rawBonus, self::MAX_BONUS_POINTS);
        $bonusCapApplied = ($rawBonus > self::MAX_BONUS_POINTS);

        // 3. Deductions
        $dedImmigration = $pointsMap['deduction_immigration'][$normalized['deduction_immigration'] ?? 'none'] ?? 0;
        $dedCriminal = $pointsMap['deduction_criminal'][$normalized['deduction_criminal'] ?? 'none'] ?? 0;
        $deductionsSum = $dedImmigration + $dedCriminal;

        // 4. Final Total Score
        $totalScore = $basePoints + $cappedBonus + $deductionsSum;

        // 5. Result status & messaging
        $isPassing = ($totalScore >= self::PASSING_SCORE);
        $pointsNeeded = $isPassing ? 0 : max(0, self::PASSING_SCORE - $totalScore);

        if (!$isComplete) {
            $status = 'incomplete';
            $statusText = 'Incomplete Form';
            $statusTextKo = '필수 항목 미입력';
        } elseif ($isPassing) {
            $status = 'eligible';
            $statusText = 'Eligible to Apply (Pass)';
            $statusTextKo = '신청 가능 (합격)';
        } else {
            $status = 'needs_points';
            $statusText = "Needs {$pointsNeeded} more points to Pass";
            $statusTextKo = "합격까지 {$pointsNeeded}점 부족";
        }

        return [
            'total_score'         => $totalScore,
            'is_passing'          => $isPassing,
            'passing_score'       => self::PASSING_SCORE,
            'points_needed'       => $pointsNeeded,
            'status'              => $status,
            'status_text'         => $statusText,
            'status_text_ko'      => $statusTextKo,
            'is_complete'         => $isComplete,
            'bonus_cap_applied'   => $bonusCapApplied,
            'summary'             => [
                'base_points'     => $basePoints,
                'bonus_points'    => $cappedBonus,
                'raw_bonus_points'=> $rawBonus,
                'deductions'      => $deductionsSum,
            ],
            'breakdown'           => [
                'age'                   => ['value' => $ageKey, 'points' => $agePts],
                'education'             => ['value' => $eduKey, 'points' => $eduPts],
                'qualifications'        => ['value' => $qualKey, 'points' => $qualPts],
                'income'                => ['value' => $incKey, 'points' => $incPts],
                'bonus_allied'          => ['value' => $normalized['bonus_allied'] ?? 'none', 'points' => $bonAllied],
                'bonus_degree'          => ['value' => $normalized['bonus_degree'] ?? 'none', 'points' => $bonDegree],
                'bonus_kiip'            => ['value' => $normalized['bonus_kiip'] ?? 'none', 'points' => $bonKiip],
                'bonus_volunteer'       => ['value' => $normalized['bonus_volunteer'] ?? 'none', 'points' => $bonVolunteer],
                'deduction_immigration' => ['value' => $normalized['deduction_immigration'] ?? 'none', 'points' => $dedImmigration],
                'deduction_criminal'    => ['value' => $normalized['deduction_criminal'] ?? 'none', 'points' => $dedCriminal],
            ],
        ];
    }
}
