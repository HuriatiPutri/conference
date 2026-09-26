<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->command) $this->command->info('Seeding packages...');

        Package::create([
            'name' => 'Package Platinum',
            'price_idr' => 2000000,
            'price_usd' => 200,
            'duration' => 365,
            'status' => 'active',
        ]);

        Package::create([
            'name' => 'Package Gold',
            'price_idr' => 1200000,
            'price_usd' => 120,
            'duration' => 365,
            'status' => 'active',
        ]);

        Package::create([
            'name' => 'Package Silver',
            'price_idr' => 600000,
            'price_usd' => 60,
            'duration' => 365,
            'status' => 'active',
        ]);
        if ($this->command) $this->command->info('Packages seeded.');
    }
}
