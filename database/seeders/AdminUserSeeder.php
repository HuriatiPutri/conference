<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) $this->command->info('Seeding admin user (idempotent)...');

        User::updateOrCreate(
            ['email' => 'admin@conference.com'],
            [
                'name' => 'Admin Conference',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($this->command) $this->command->info('Admin user seeded.');
    }
}