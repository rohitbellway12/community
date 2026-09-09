<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminProfileSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();

        if (!$admin) {
            $this->command->error('Admin user with email admin@gmail.com was not found.');
            return;
        }

        Profile::updateOrCreate(
            [
                'user_id' => $admin->id,
            ],
            [
                'username' => 'admin',
                'bio' => 'REIAC Community Administrator',
                'avatar' => null,
                'cover_image' => null,
                'country_id' => null,
                'location' => 'Global',
            ]
        );

        $this->command->info('Admin profile created/updated successfully.');
    }
}