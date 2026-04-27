<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Membership Regular (1 Year)',
            'price_idr' => 500000,
            'price_usd' => 50,
            'duration' => 365,
            'status' => 'active',
        ]);

        Package::create([
            'name' => 'Membership Premium (2 Years)',
            'price_idr' => 900000,
            'price_usd' => 90,
            'duration' => 730,
            'status' => 'active',
        ]);
        
        Package::create([
            'name' => 'International Member (1 Year)',
            'price_idr' => 750000,
            'price_usd' => 50,
            'duration' => 365,
            'status' => 'active',
        ]);
    }
}
