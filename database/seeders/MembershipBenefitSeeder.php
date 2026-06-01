<?php

namespace Database\Seeders;

use App\Models\MembershipBenefit;
use App\Models\Package;
use App\Models\PackageBenefit;
use Illuminate\Database\Seeder;

class MembershipBenefitSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->command) $this->command->info('Seeding membership benefits and package assignments...');

        // Master benefits
        $masters = [
            ['code' => 'DISC-15', 'name' => 'Member Discount 15%', 'benefit_type' => 'discount', 'description' => 'Diskon 15% pada setiap transaksi.'],
            ['code' => 'DISC-10', 'name' => 'Member Discount 10%', 'benefit_type' => 'discount', 'description' => 'Diskon 10% pada setiap transaksi.'],
            ['code' => 'DISC-5', 'name' => 'Member Discount 5%', 'benefit_type' => 'discount', 'description' => 'Diskon 5% pada setiap transaksi.'],
            ['code' => 'SOUVENIR', 'name' => 'Souvenir Package', 'benefit_type' => 'souvenir', 'description' => 'Souvenir yang diberikan sesuai paket.'],
            ['code' => 'KEYNOTE', 'name' => 'Keynote Opportunity', 'benefit_type' => 'opportunity', 'description' => 'Kesempatan untuk menjadi keynote speaker.'],
        ];

        $masterMap = [];
        foreach ($masters as $m) {
            $mb = MembershipBenefit::updateOrCreate(
                ['code' => $m['code']],
                ['name' => $m['name'], 'benefit_type' => $m['benefit_type'], 'description' => $m['description']]
            );

            $masterMap[$m['code']] = $mb;
        }

        // Package -> assigned benefits configuration
        $packageConfigs = [
            'Package Platinum' => [
                ['code' => 'DISC-15', 'value_type' => 'percentage', 'value' => 15, 'notes' => null, 'quota' => null, 'max_value' => null],
                ['code' => 'SOUVENIR', 'value_type' => 'item', 'value' => null, 'notes' => 'kaos, sticker, tumbler', 'quota' => null, 'max_value' => null],
                ['code' => 'KEYNOTE', 'value_type' => 'quota', 'value' => 1, 'notes' => 'Eligible to be selected as keynote', 'quota' => 1, 'max_value' => null],
            ],
            'Package Gold' => [
                ['code' => 'DISC-10', 'value_type' => 'percentage', 'value' => 10, 'notes' => null, 'quota' => null, 'max_value' => null],
                ['code' => 'SOUVENIR', 'value_type' => 'item', 'value' => null, 'notes' => 'kaos', 'quota' => null, 'max_value' => null],
            ],
            'Package Silver' => [
                ['code' => 'DISC-5', 'value_type' => 'percentage', 'value' => 5, 'notes' => null, 'quota' => null, 'max_value' => null],
            ],
        ];

        foreach ($packageConfigs as $packageName => $assigns) {
            $package = Package::where('name', $packageName)->first();
            if (! $package) {
                continue;
            }

            foreach ($assigns as $assign) {
                $mb = $masterMap[$assign['code']] ?? null;
                if (! $mb) continue;

                PackageBenefit::updateOrCreate(
                    ['package_id' => $package->id, 'membership_benefit_id' => $mb->id],
                    [
                        'value_type' => $assign['value_type'],
                        'value' => $assign['value'],
                        'max_value' => $assign['max_value'],
                        'quota' => $assign['quota'],
                        'notes' => $assign['notes'],
                    ]
                );
            }
        }

        if ($this->command) $this->command->info('Membership benefits and package assignments seeded.');
    }
}