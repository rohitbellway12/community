<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategoryAndServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Visa & Immigration',
                'description' => 'Comprehensive visa application guidance, documentation review, and immigration points assessment.',
                'icon' => 'passport',
                'sort_order' => 1,
            ],
            [
                'name' => 'University Admissions',
                'description' => 'End-to-end assistance with foreign university applications, SOP preparation, and scholarship guidance.',
                'icon' => 'academic-cap',
                'sort_order' => 2,
            ],
            [
                'name' => 'Language & Test Prep',
                'description' => 'TOPIK, EPS, IELTS, and PTE preparation courses with expert instructors and mock assessments.',
                'icon' => 'book-open',
                'sort_order' => 3,
            ],
            [
                'name' => 'Career & Job Assistance',
                'description' => 'Job placement, resume building, EPS registration, and interview coaching for foreign careers.',
                'icon' => 'briefcase',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $catData) {
            $cat = ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'icon' => $catData['icon'],
                    'sort_order' => $catData['sort_order'],
                    'is_active' => true,
                ]
            );

            if ($cat->name === 'Visa & Immigration') {
                Service::firstOrCreate(
                    ['slug' => 'f-2-7-points-based-resident-visa-consultation'],
                    [
                        'service_category_id' => $cat->id,
                        'title' => 'F-2-7 Points-Based Resident Visa Consultation',
                        'short_description' => 'One-on-one expert assessment of your South Korea F-2-7 visa points, document verification, and eligibility optimization.',
                        'description' => "Our senior immigration consultants analyze your age, education degree, Korean language proficiency (KIIP / TOPIK), and annual income to help you cross the 80-point threshold required for the prestigious F-2-7 Long-term Resident Visa in South Korea.\n\nKey Highlights:\n- Complete point calculation & gap analysis\n- KIIP completion strategy\n- Income tax document preparation (So-deuk-geum-aek Jeung-myeong)\n- Direct coordination with Immigration authorities",
                        'whatsapp_number' => '+821021482834',
                        'whatsapp_message' => 'Hello REIAC Consultancy, I want to book a consultation for South Korea F-2-7 Points-Based Resident Visa. Please share the details.',
                        'phone' => '+82 10-2148-2834',
                        'email' => 'visa@reiaconsultancy.com',
                        'sort_order' => 1,
                        'is_published' => true,
                    ]
                );

                Service::firstOrCreate(
                    ['slug' => 'd-2-student-visa-processing'],
                    [
                        'service_category_id' => $cat->id,
                        'title' => 'D-2 Student Visa Processing & Documentation',
                        'short_description' => 'Complete visa filing support for Korean University admissions, bank balance certificate verification, and embassy interview coaching.',
                        'description' => "Securing your D-2 Student Visa requires precise paperwork including Certificate of Admission (CoA), bank statement apostille, and sponsor verification. We ensure 100% compliant documentation to maximize your visa approval chances.\n\nIncluded in this service:\n- Document checklist & apostille guidance\n- Financial affidavit and bank verification\n- Embassy appointment scheduling\n- Pre-departure orientation session",
                        'whatsapp_number' => '+821021482834',
                        'whatsapp_message' => 'Hi REIAC team, I need assistance with D-2 Student Visa processing for South Korea. Please guide me.',
                        'phone' => '+82 10-2148-2834',
                        'email' => 'admissions@reiaconsultancy.com',
                        'sort_order' => 2,
                        'is_published' => true,
                    ]
                );
            }

            if ($cat->name === 'Language & Test Prep') {
                Service::firstOrCreate(
                    ['slug' => 'topik-exam-preparation-masterclass'],
                    [
                        'service_category_id' => $cat->id,
                        'title' => 'TOPIK Exam Preparation Masterclass',
                        'short_description' => 'Intensive training for TOPIK I and II with past exam paper reviews, mock tests, and Korean native teacher sessions.',
                        'description' => "Master the Test of Proficiency in Korean (TOPIK) to boost your points for University scholarships and F-2-7 visa eligibility. Our structured course covers Reading, Listening, and Writing sections with guaranteed score improvements.\n\nCourse Features:\n- Live interactive classes + recorded video archives\n- Weekly mock tests with instant scoring and feedback\n- Specialized essay writing workshops for TOPIK II\n- Comprehensive study materials and vocabulary lists",
                        'whatsapp_number' => '+919876543210',
                        'whatsapp_message' => 'Hello REIAC team, I am interested in enrolling in the TOPIK Exam Preparation Masterclass. Please send schedule & fees.',
                        'phone' => '+91 98765 43210',
                        'email' => 'training@reiaconsultancy.com',
                        'sort_order' => 1,
                        'is_published' => true,
                    ]
                );
            }
        }
    }
}
