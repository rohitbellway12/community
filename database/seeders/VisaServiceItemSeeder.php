<?php

// database/seeders/VisaServiceItemSeeder.php

namespace Database\Seeders;

use App\Models\VisaService;
use App\Models\VisaServiceItem;
use Illuminate\Database\Seeder;

class VisaServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $service = VisaService::where('slug', 'south-korea-visa-services')->first();

        if ($service) {
            $items = [
                [
                    'visa_code' => 'D-2',
                    'title' => 'Study Visa (D-2)',
                    'description' => 'For students pursuing regular degree programs at undergraduate or graduate levels in South Korea.',
                    'requirements' => ['Admission Letter', 'Certificate of Tuition Fees', 'Financial Proof', 'Academic Transcripts'],
                    'sort_order' => 1,
                ],
                [
                    'visa_code' => 'D-4',
                    'title' => 'Language Course Visa (D-4)',
                    'description' => 'For individuals enrolled in general language training programs at Korean university language institutes.',
                    'requirements' => ['Language Institute Admission Certificate', 'Bank Statement', 'Study Plan'],
                    'sort_order' => 2,
                ],
                [
                    'visa_code' => 'H-1',
                    'title' => 'Working Holiday Visa (H-1)',
                    'description' => 'Allows youth to holiday and work temporarily in South Korea to supplement travel funds.',
                    'requirements' => ['Valid Passport', 'Round-trip Air Ticket', 'Travel Itinerary', 'Financial Proof'],
                    'sort_order' => 3,
                ],
                [
                    'visa_code' => 'C-3',
                    'title' => 'Visit Visa (C-3)',
                    'description' => 'Short-term general visa for tourism, visiting family, or attending brief conferences.',
                    'requirements' => ['Passport', 'Visa Application Form', 'Invitation Letter (if applicable)', 'Employment Proof'],
                    'sort_order' => 4,
                ],
                [
                    'visa_code' => 'C-2',
                    'title' => 'Business Visa (C-2)',
                    'description' => 'Short-term business visa for corporate meetings, market research, or trade negotiations.',
                    'requirements' => ['Business Invitation Letter', 'Certificate of Business Registration', 'Dispatch Letter from Employer'],
                    'sort_order' => 5,
                ],
            ];

            foreach ($items as $item) {
                VisaServiceItem::updateOrCreate(
                    ['visa_service_id' => $service->id, 'visa_code' => $item['visa_code']],
                    $item
                );
            }
        }
    }
}
