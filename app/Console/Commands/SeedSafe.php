<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedSafe extends Command
{
    /**
     * The name and signature of the console command.
     * Accept comma-separated list of seeder classes via --classes option.
     */
    protected $signature = 'seed:safe {--classes= : Comma-separated seeder classes to run (e.g. "Database\\Seeders\\PackageSeeder,Database\\Seeders\\MembershipBenefitSeeder") }';

    /**
     * The console command description.
     */
    protected $description = 'Run specific seeders safely (idempotent seeders expected).';

    public function handle()
    {
        $classes = $this->option('classes');

        if (! $classes) {
            $this->error('No classes provided. Use --classes="Class1,Class2"');
            return 1;
        }

        $list = array_filter(array_map('trim', explode(',', $classes)));

        foreach ($list as $class) {
            $this->info("Running seeder: {$class}");
            $exit = $this->call('db:seed', ['--class' => $class]);
            if ($exit !== 0) {
                $this->error("Seeder failed: {$class}");
                return $exit;
            }
        }

        $this->info('All requested seeders ran successfully.');
        return 0;
    }
}
