<?php

// database/seeders/VisaServiceSeeder.php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\VisaService;
use Illuminate\Database\Seeder;

class VisaServiceSeeder extends Seeder
{
    public function run(): void
    {
        $southKorea = Country::where('code', 'KOR')->first();

        if ($southKorea) {
            VisaService::updateOrCreate(
                ['slug' => 'south-korea-visa-services'],
                [
                    'country_id' => $southKorea->id,
                    'title' => 'South Korea Visa Services',
                    'slug' => 'south-korea-visa-services',
                    'description' => 'Official REIAC specialized visa and immigration assistance for South Korea.',
                    'status' => true,
                    'sort_order' => 1,
                ]
            );
        }
    }
}
