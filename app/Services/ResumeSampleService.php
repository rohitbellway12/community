<?php

namespace App\Services;

use App\Models\Resume;

class ResumeSampleService
{
    /**
     * Get a fully populated mock sample Resume model for preview.
     */
    public static function getSampleResume(string $template = 'modern', string $lang = 'en'): Resume
    {
        $allowedTemplates = ['modern', 'classic', 'creative', 'korean_standard'];
        $template = in_array($template, $allowedTemplates) ? $template : 'modern';
        $isKo = $lang === 'ko';

        $sample = new Resume();
        $sample->id = 0;
        $sample->template = $template;
        $sample->language = $lang;
        $sample->is_watermark_removed = false;

        if ($template === 'korean_standard') {
            return self::buildKoreanStandardSample($sample, $isKo);
        }

        return self::buildWesternSample($sample, $template, $isKo);
    }

    /**
     * Build Korean Standard (입사지원서) sample data.
     */
    protected static function buildKoreanStandardSample(Resume $sample, bool $isKo): Resume
    {
        $sample->title = $isKo ? '한국형 표준 입사지원서 (샘플)' : 'Korean Standard Job Application (Sample)';
        $sample->personal_info = [
            'full_name' => $isKo ? '김민수' : 'Kim Min-soo',
            'english_name' => 'MIN SOO KIM',
            'date_of_birth' => $isKo ? '1998년 05월 14일 (만 26세)' : 'May 14, 1998 (Age 26)',
            'gender' => $isKo ? '남 (Male)' : 'Male',
            'job_title' => $isKo ? '소프트웨어 엔지니어 (백엔드 / 클라우드)' : 'Software Engineer (Backend & Cloud)',
            'total_experience' => $isKo ? '신입 및 인턴 1년 2개월' : '1 Year 2 Months (Intern & Junior)',
            'phone' => '010-4829-1029',
            'email' => 'minsoo.kim.dev@reiac.org',
            'location' => $isKo ? '서울특별시 서초구 강남대로 102' : '102 Gangnam-daero, Seocho-gu, Seoul, Korea',
            'emergency_phone' => '010-9281-3310 (부)',
            'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80',
        ];
        $sample->summary = $isKo 
            ? '안정적이고 확장 가능한 백엔드 시스템 구축에 열정을 가진 개발자입니다. 대용량 트래픽 처리와 클라우드 아키텍처에 강점을 지니고 있습니다.'
            : 'Passionate Backend and Cloud Engineer with hands-on experience designing resilient distributed microservices and modern APIs.';
        
        $sample->education = [
            [
                'period' => '2017.03 ~ 2022.02',
                'degree' => $isKo ? '학사 (졸업)' : 'Bachelor of Engineering (Graduated)',
                'institution' => $isKo ? '서울대학교 (Seoul National Univ)' : 'Seoul National University',
                'major' => $isKo ? '컴퓨터공학부' : 'Computer Science and Engineering',
                'field' => $isKo ? '컴퓨터공학부' : 'Computer Science and Engineering',
                'location' => $isKo ? '서울 본교' : 'Seoul, Korea',
                'grade' => '4.15 / 4.5',
                'status' => $isKo ? '졸업' : 'Graduated',
            ],
            [
                'period' => '2014.03 ~ 2017.02',
                'degree' => $isKo ? '고등학교 (졸업)' : 'High School Diploma',
                'institution' => $isKo ? '한성과학고등학교' : 'Hansung Science High School',
                'major' => $isKo ? '이과계열' : 'Natural Sciences',
                'field' => $isKo ? '이과계열' : 'Natural Sciences',
                'location' => $isKo ? '서울' : 'Seoul, Korea',
                'grade' => '-',
                'status' => $isKo ? '졸업' : 'Graduated',
            ],
        ];

        $sample->experience = [
            [
                'period' => '2022.03 ~ 2023.08',
                'company' => $isKo ? '카카오엔터프라이즈' : 'Kakao Enterprise',
                'role' => $isKo ? '백엔드 엔지니어 (인턴/주니어)' : 'Backend Software Engineer',
                'position' => $isKo ? '백엔드 엔지니어 (인턴/주니어)' : 'Backend Software Engineer',
                'department' => $isKo ? '클라우드 플랫폼 개발팀' : 'Cloud Platform Dev Team',
                'location' => 'Seoul, Korea',
                'responsibilities' => $isKo 
                    ? '대용량 분산 API 서버 구축 및 MSA 마이그레이션 참여 (Spring Boot, Kafka, Redis). 일간 3백만 건의 트래픽을 처리하는 게이트웨이 서비스 병목 구간 30% 개선.'
                    : 'Engineered high-throughput REST APIs and microservices using Spring Boot, Redis, and Kafka. Optimized latency by 30%.',
            ],
            [
                'period' => '2021.07 ~ 2021.12',
                'company' => $isKo ? '네이버 클라우드' : 'NAVER Cloud',
                'role' => $isKo ? '클라우드 인프라 인턴' : 'Cloud Infrastructure Intern',
                'position' => $isKo ? '클라우드 인프라 인턴' : 'Cloud Infrastructure Intern',
                'department' => $isKo ? '인프라 기술연구소' : 'Infra Tech Lab',
                'location' => 'Seongnam, Korea',
                'responsibilities' => $isKo 
                    ? '사내 Kubernetes 클러스터 모니터링 대시보드 구축 및 CI/CD 자동화 파이프라인 수립. 프로메테우스/그라파나 연동 완료.'
                    : 'Configured Kubernetes cluster monitoring dashboards with Prometheus & Grafana and automated deployment pipelines.',
            ],
        ];

        $sample->military_service = [
            'status' => 'completed',
            'branch' => $isKo ? '대한민국 육군 (ROK Army)' : 'Republic of Korea Army',
            'rank' => $isKo ? '병장 만기전역' : 'Sergeant (Discharged)',
            'period' => '2018.06 ~ 2020.02',
            'discharge_reason' => $isKo ? '만기전역 (Normal Discharge)' : 'Honorable Discharge',
        ];

        $sample->language_tests = [
            [
                'test_name' => 'TOEIC',
                'score' => '940 / 990',
                'acquisition_date' => '2023.08.12',
                'organization' => 'ETS (YBM)',
            ],
            [
                'test_name' => 'OPIc (English Speaking)',
                'score' => 'AL (Advanced Low)',
                'acquisition_date' => '2023.09.20',
                'organization' => 'ACTFL',
            ],
            [
                'test_name' => 'JLPT (Japanese)',
                'score' => 'N2 (145/180)',
                'acquisition_date' => '2022.12.04',
                'organization' => 'Japan Foundation',
            ],
        ];

        $sample->certifications = [
            [
                'name' => $isKo ? '정보처리기사' : 'Engineer Information Processing',
                'issuer' => $isKo ? '한국산업인력공단 (HRD Korea)' : 'HRD Korea',
                'date' => '2022.06.17',
                'registration_no' => '22-04-102931',
            ],
            [
                'name' => 'AWS Certified Solutions Architect – Associate',
                'issuer' => 'Amazon Web Services',
                'date' => '2023.04.10',
                'registration_no' => 'AWS-SAA-839210',
            ],
            [
                'name' => $isKo ? 'SQLD (SQL 개발자)' : 'SQL Developer (SQLD)',
                'issuer' => $isKo ? '한국데이터산업진흥원' : 'Korea Data Agency',
                'date' => '2021.11.28',
                'registration_no' => 'SQLD-092811',
            ],
        ];

        $sample->computer_skills = [
            [
                'category' => $isKo ? '프로그래밍 언어' : 'Programming',
                'tool_name' => 'Java, Python, PHP, JavaScript (ES6+)',
                'proficiency' => $isKo ? '상 (Advanced)' : 'Advanced',
                'description' => $isKo ? '객체지향 설계, 자료구조 및 알고리즘 최적화 능숙' : 'OOP design principles, data structures and algorithm optimization',
            ],
            [
                'category' => $isKo ? '프레임워크 & DB' : 'Framework & DB',
                'tool_name' => 'Spring Boot, Laravel, MySQL, Redis',
                'proficiency' => $isKo ? '상 (Advanced)' : 'Advanced',
                'description' => $isKo ? '쿼리 실행 계획 분석, 인덱스 최적화 및 캐싱 전략 수립' : 'Query execution planning, indexing optimization and caching strategy',
            ],
            [
                'category' => $isKo ? '클라우드 & 인프라' : 'Cloud & DevOps',
                'tool_name' => 'AWS (EC2, S3, RDS), Docker, Git CI/CD',
                'proficiency' => $isKo ? '중상 (Upper-Inter.)' : 'Upper-Intermediate',
                'description' => $isKo ? '컨테이너 기반 배포 및 자동화 빌드 파이프라인 구축' : 'Container-based deployment and automated build pipelines',
            ],
            [
                'category' => $isKo ? '문서 & 오피스' : 'Office & Tools',
                'tool_name' => 'MS Excel, PowerPoint, 한글(HWP), Jira/Confluence',
                'proficiency' => $isKo ? '상 (Advanced)' : 'Advanced',
                'description' => $isKo ? '체계적인 기술 기획서 작성 및 데이터 분석 스프레드시트 활용' : 'Technical documentation, KPI tracking, and sprint planning',
            ],
        ];

        $sample->self_introduction = [
            'growth' => $isKo
                ? '엔지니어이신 아버지를 보며 어릴 적부터 문제의 원인을 파고들고 해결하는 집념을 배웠습니다. 고등학교 시절 처음 알고리즘을 접하며 기술이 사람들의 실생활을 어떻게 변화시킬 수 있는지 깊이 체감하였고, 서울대학교 컴퓨터공학부에 진학하여 탄탄한 컴퓨터 기초 역량을 다졌습니다.'
                : 'Raised by an engineer parent, I grew up with an innate curiosity for root-cause problem solving. During university at Seoul National University, I discovered my deep passion for distributed computing and scalable backend systems, driving me to build reliable software solutions.',
            'personality' => $isKo
                ? '매사에 긍정적이며 세심한 주의력을 발휘합니다. 팀 프로젝트 중 예기치 않은 오류가 발생했을 때 감정적으로 흔들리지 않고 시스템 로그와 메트릭을 체계적으로 분석하여 문제를 해결합니다. 동료들의 의견을 경청하고 건강한 코드 리뷰 문화를 만드는 것을 지향합니다.'
                : 'Calm, methodical, and team-oriented. When unexpected production incidents occur, I prioritize systematic log inspection and structured root cause analysis rather than panic, ensuring team stability and continuous learning.',
            'strengths' => $isKo
                ? '카카오엔터프라이즈와 네이버 클라우드 인턴십을 통해 일일 수백만 트랜잭션을 처리하는 대규모 분산 환경을 직접 경험했습니다. 데이터베이스 병목 규명, Redis를 통한 응답 캐싱, 그리고 Kafka 기반 비동기 파이프라인 구축 역량이 검증되어 실무에 즉시 투입될 수 있습니다.'
                : 'Extensive hands-on internship experience at top-tier tech companies. Proven competency in SQL execution plan optimization, Redis caching strategies, and asynchronous event streaming with Apache Kafka.',
            'motivation_and_aspirations' => $isKo
                ? '혁신적인 기술력과 글로벌 확장성을 갖춘 귀사의 엔지니어링 팀에서 함께 성장하고자 지원하였습니다. 입사 후 첫째로 팀의 기존 코드베이스와 배포 인프라를 신속히 습득하고, 둘째로 시스템의 안정성과 처리량을 지속적으로 고도화하여 글로벌 고객들에게 최상의 경험을 선사하는 핵심 엔지니어로 성장하겠습니다.'
                : 'I am eager to contribute to your company\'s forward-thinking technical initiatives. My objective is to rapidly integrate into your development lifecycle, elevate API performance, and deliver robust software that powers international growth.',
        ];
        $sample->skills = ['Java', 'Spring Boot', 'Python', 'AWS', 'Docker', 'Kubernetes', 'MySQL', 'Redis', 'Kafka', 'Git'];
        $sample->languages = ['한국어 (Native)', 'English (Business / TOEIC 940)', '日本語 (Intermediate / JLPT N2)'];

        return $sample;
    }

    /**
     * Build Western ATS Resume sample data (Modern, Classic, Creative).
     */
    protected static function buildWesternSample(Resume $sample, string $template, bool $isKo): Resume
    {
        $sample->title = $isKo 
            ? '샘플 ' . ucfirst($template) . ' 이력서'
            : 'Sample ' . ucfirst($template) . ' CV';

        if ($isKo) {
            $sample->personal_info = [
                'full_name' => '김민수',
                'english_name' => 'MIN SOO KIM',
                'job_title' => '시니어 풀스택 소프트웨어 엔지니어',
                'email' => 'minsoo.kim@example.kr',
                'phone' => '010-9876-5432',
                'location' => '서울특별시 강남구 테헤란로 (Seoul, Korea)',
                'linkedin' => 'linkedin.com/in/minsoo-kim-dev',
                'github' => 'github.com/minsookim',
                'portfolio' => 'minsookim.dev',
            ];
            $sample->summary = '대규모 웹 애플리케이션 및 마이크로서비스 아키텍처 설계와 운영에 4년 이상의 전문 경험을 갖춘 시니어 풀스택 엔지니어입니다. 일일 수백만 건의 트랜잭션을 안정적으로 처리하고 시스템 처리량을 42% 개선한 실적을 보유하고 있습니다. 클린 코드, 확장성 높은 클라우드 인프라(AWS/Docker), 그리고 현대적인 AI 기술 연동에 깊은 열정을 쏟고 있습니다.';
            $sample->education = [
                [
                    'degree' => '공학사 (학사 졸업)',
                    'field' => '컴퓨터공학부',
                    'institution' => '서울대학교 (Seoul National University)',
                    'start_date' => '2018.03',
                    'end_date' => '2022.02',
                    'grade' => '학점: 3.92 / 4.3 (우등 졸업)',
                ],
                [
                    'degree' => '고등학교 졸업',
                    'field' => '이공계열',
                    'institution' => '서울과학고등학교',
                    'start_date' => '2015.03',
                    'end_date' => '2018.02',
                    'grade' => '수석 졸업',
                ],
            ];
            $sample->experience = [
                [
                    'title' => '시니어 풀스택 소프트웨어 엔지니어',
                    'position' => '시니어 풀스택 소프트웨어 엔지니어',
                    'company' => '카카오엔터프라이즈 (Kakao Enterprise)',
                    'location' => '경기도 성남시 판교',
                    'start_date' => '2022.07',
                    'end_date' => '현재 재직중',
                    'is_current' => 1,
                    'points' => [
                        'Laravel 및 Redis 기반 이벤트 기반 마이크로서비스 설계로 일일 250만 건 이상의 API 트랜잭션 무장애 처리 달성.',
                        'Vue.js와 Tailwind CSS 기반의 실시간 모니터링 인터랙티브 대시보드를 구축하여 신규 이용자 이탈률 35% 감소.',
                        'AWS ECS 및 Docker 기반 자동화 배포 파이프라인(CI/CD)을 주도 구축하여 릴리스 주기를 4시간에서 18분으로 단축.',
                        '주간 코드 리뷰와 클린 아키텍처 가이드라인을 도입하여 주니어 개발자 6명의 역량 향상 멘토링 수행.'
                    ]
                ],
                [
                    'title' => '소프트웨어 개발 엔지니어',
                    'position' => '소프트웨어 개발 엔지니어',
                    'company' => '네이버 클라우드 (NAVER Cloud)',
                    'location' => '경기도 성남시 분당',
                    'start_date' => '2021.08',
                    'end_date' => '2022.06',
                    'points' => [
                        '대용량 트래픽 데이터베이스 쿼리 실행 계획 분석 및 캐싱 레이어 최적화로 p99 응답 속도 420ms에서 95ms로 77% 개선.',
                        '주요 결제 및 인증 서비스 전반에 E2E 자동화 단위 테스트 스위트를 구축하여 88%의 높은 테스트 커버리지 유지.',
                        '기획 및 UI/UX 디자인 팀과 긴밀한 애자일 스프린트 협업을 통해 12개의 핵심 신규 피처를 기한 내 출시.'
                    ]
                ]
            ];
            $sample->skills = [
                'PHP / Laravel', 'Java', 'Spring Boot', 'JavaScript (ES6+)', 'TypeScript',
                'Vue.js', 'React.js', 'MySQL', 'Redis', 'Docker',
                'AWS (ECS, RDS, S3)', 'RESTful APIs', 'Git CI/CD', 'Agile / Scrum'
            ];
            $sample->projects = [
                [
                    'title' => 'AI 기반 구직자 역량 분석 및 이력서 최적화 플랫폼',
                    'tech' => 'Laravel, OpenAI API, Alpine.js, Tailwind CSS',
                    'description' => '15,000명 이상의 학생 및 구직자를 대상으로 실시간 한국어/영어 ATS 이력서 분석 및 워터마크 자동 제어 PDF 생성 플랫폼 개발.'
                ],
                [
                    'title' => '초당 수천 건 주문 처리 대규모 마켓플레이스',
                    'tech' => 'PHP 8.2, MySQL, Redis, WebSocket',
                    'description' => '웹소켓 실시간 주문 추적 시스템과 무중단 자동 데이터베이스 장애 복구(Failover) 아키텍처 구축.'
                ]
            ];
            $sample->certifications = [
                [
                    'name' => 'AWS Certified Solutions Architect – Associate',
                    'issuer' => 'Amazon Web Services (AWS)',
                    'year' => '2023',
                    'date' => '2023.04'
                ],
                [
                    'name' => '정보처리기사 (Engineer Information Processing)',
                    'issuer' => '한국산업인력공단 (HRD Korea)',
                    'year' => '2022',
                    'date' => '2022.06'
                ],
                [
                    'name' => 'SQLD (SQL 개발자 자격증)',
                    'issuer' => '한국데이터산업진흥원',
                    'year' => '2021',
                    'date' => '2021.11'
                ]
            ];
            $sample->languages = ['한국어 (모국어 / Native)', '영어 (비즈니스 회화 가능 / TOEIC 940)'];
        } else {
            $sample->personal_info = [
                'full_name' => 'Alex Johnson',
                'job_title' => 'Senior Full Stack Software Engineer',
                'email' => 'alex.johnson@example.com',
                'phone' => '+1 (555) 234-5678',
                'location' => 'San Francisco, CA, USA',
                'linkedin' => 'linkedin.com/in/alex-johnson-dev',
                'github' => 'github.com/alexjohnson',
                'portfolio' => 'alexjohnson.dev',
            ];
            $sample->summary = 'Innovative and results-driven Senior Full Stack Software Engineer with 4+ years of expertise in architecting high-traffic web applications, microservices, and reactive user interfaces. Proven track record of boosting system throughput by 42% and mentoring high-performing engineering teams. Passionate about clean architecture, scalable cloud infrastructure, and modern AI integration.';
            $sample->education = [
                [
                    'degree' => 'Bachelor of Science (B.S.)',
                    'field' => 'Computer Science & Engineering',
                    'institution' => 'University of California, Berkeley',
                    'start_date' => '2018',
                    'end_date' => '2022',
                    'grade' => 'GPA: 3.88 / 4.0',
                ],
                [
                    'degree' => 'High School Diploma',
                    'field' => 'Science & Advanced Mathematics',
                    'institution' => 'Lowell High School, San Francisco',
                    'start_date' => '2014',
                    'end_date' => '2018',
                    'grade' => 'Honors Graduate',
                ],
            ];
            $sample->experience = [
                [
                    'title' => 'Senior Full Stack Engineer',
                    'position' => 'Senior Full Stack Engineer',
                    'company' => 'NexGen Tech Cloud',
                    'location' => 'San Francisco, CA',
                    'start_date' => 'July 2022',
                    'end_date' => 'Present',
                    'is_current' => 1,
                    'points' => [
                        'Architected and deployed resilient event-driven microservices using Laravel and Redis, processing over 2.5 million daily API transactions.',
                        'Engineered dynamic interactive dashboards in Vue.js and Tailwind CSS, reducing user onboarding friction by 35%.',
                        'Spearheaded automated CI/CD pipeline modernization on AWS ECS, reducing production deployment release cycles from 4 hours to 18 minutes.',
                        'Mentored 6 junior and mid-level software developers through structured weekly code reviews and clean code best practices.'
                    ]
                ],
                [
                    'title' => 'Software Development Engineer',
                    'position' => 'Software Development Engineer',
                    'company' => 'InfraScale Systems',
                    'location' => 'San Jose, CA',
                    'start_date' => 'August 2021',
                    'end_date' => 'June 2022',
                    'points' => [
                        'Designed scalable RESTful APIs with database query optimization, reducing p99 response times from 420ms to 95ms.',
                        'Implemented automated end-to-end unit testing suites achieving 88% overall test coverage across mission-critical services.',
                        'Collaborated cross-functionally with UI/UX designers and product managers to roll out 12 high-priority product features on schedule.'
                    ]
                ]
            ];
            $sample->skills = [
                'PHP / Laravel', 'JavaScript (ES6+)', 'TypeScript', 'React.js', 'Vue.js',
                'Tailwind CSS', 'MySQL', 'PostgreSQL', 'Redis', 'Docker',
                'Amazon Web Services (AWS)', 'RESTful APIs', 'Git / GitHub CI/CD', 'Agile / Scrum'
            ];
            $sample->projects = [
                [
                    'title' => 'AI-Powered Resume & Career Optimizer',
                    'tech' => 'Laravel, OpenAI API, Alpine.js, Tailwind CSS',
                    'description' => 'Built an intelligent career portal that provides real-time ATS scoring, automatic bullet enhancement, and watermark-free PDF generation for over 15,000 active students.'
                ],
                [
                    'title' => 'High-Concurrency Marketplace Platform',
                    'tech' => 'PHP 8.2, MySQL, Redis Caching, Stripe Payments',
                    'description' => 'Engineered a multi-vendor digital product marketplace with real-time websocket order tracking and zero downtime automated database failover.'
                ]
            ];
            $sample->certifications = [
                [
                    'name' => 'AWS Certified Solutions Architect – Associate',
                    'issuer' => 'Amazon Web Services',
                    'year' => '2023',
                    'date' => '2023.04'
                ],
                [
                    'name' => 'Laravel Certified Developer',
                    'issuer' => 'Laravel LLC',
                    'year' => '2022',
                    'date' => '2022.06'
                ]
            ];
            $sample->languages = ['English (Fluent / Native)', 'Spanish (Conversational)'];
        }

        return $sample;
    }
}
