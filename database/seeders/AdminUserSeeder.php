<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'Admin',
                'password' => '12345678',
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );
    }
}