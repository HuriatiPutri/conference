<?php

namespace Database\Seeders;

use App\Models\JoivRegistrationFee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JoivRegistrationFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create one if doesn't exist
        $admin = User::first();

        if (!$admin) {
            // If no users exist, you might want to skip or create an admin
            $this->command->warn('No users found in database. Please create an admin user first.');
            return;
        }

        // Check if a fee already exists
        if (JoivRegistrationFee::count() > 0) {
            $this->command->info('Fee records already exist. Skipping seeder.');
            return;
        }

        // Create initial fee record with both USD and IDR amounts
        JoivRegistrationFee::create([
            'usd_amount' => 150.00,
            'idr_amount' => 2250000.00, // Approximately 150 USD * 15000 IDR exchange rate
            'notes' => 'Initial JOIV registration fee',
            'created_by' => $admin->id,
        ]);

        $this->command->info('Initial JOIV registration fee created successfully.');
    }
}
