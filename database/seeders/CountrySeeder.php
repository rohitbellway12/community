<?php

// database/seeders/CountrySeeder.php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'India', 'code' => 'IND', 'iso_code' => 'IN', 'flag' => '🇮🇳'],
            ['name' => 'Bangladesh', 'code' => 'BGD', 'iso_code' => 'BD', 'flag' => '🇧🇩'],
            ['name' => 'Nepal', 'code' => 'NPL', 'iso_code' => 'NP', 'flag' => '🇳🇵'],
            ['name' => 'Sri Lanka', 'code' => 'LKA', 'iso_code' => 'LK', 'flag' => '🇱🇰'],
            ['name' => 'South Korea', 'code' => 'KOR', 'iso_code' => 'KR', 'flag' => '🇰🇷'],
            ['name' => 'Canada', 'code' => 'CAN', 'iso_code' => 'CA', 'flag' => '🇨🇦'],
            ['name' => 'USA', 'code' => 'USA', 'iso_code' => 'US', 'flag' => '🇺🇸'],
            ['name' => 'UK', 'code' => 'GBR', 'iso_code' => 'GB', 'flag' => '🇬🇧'],
            ['name' => 'Australia', 'code' => 'AUS', 'iso_code' => 'AU', 'flag' => '🇦🇺'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }
    }
}
