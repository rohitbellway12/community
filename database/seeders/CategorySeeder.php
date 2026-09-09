<?php

// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'General', 'slug' => 'general', 'description' => 'Discuss general topics and experiences.', 'sort_order' => 1],
            ['name' => 'Ask a Solution', 'slug' => 'ask-a-solution', 'description' => 'Ask questions and get verified answers from the community.', 'sort_order' => 2],
            ['name' => 'Jobs Info', 'slug' => 'jobs-info', 'description' => 'Career advice, part-time and full-time job openings.', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
