<?php

namespace App\Console\Commands;

use App\Models\InvoiceHistory;
use Illuminate\Console\Command;

class CleanupExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:cleanup-expired {--hours=2 : Hours after which pending payments are considered expired}';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup expired pending payments to prevent spam in invoice_history table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        
        $this->info("Cleaning up payments older than {$hours} hours...");
        
        // Mark pending payments older than specified hours as expired
        $expiredCount = InvoiceHistory::where('status', 'pending')
            ->where('payment_gateway', 'paypal')
            ->where('created_at', '<', now()->subHours($hours))
            ->update([
                'status' => 'expired',
                'execution_response' => [
                    'error' => "Payment session expired after {$hours} hours",
                    'expired_at' => now()->toISOString()
                ]
            ]);
        
        if ($expiredCount > 0) {
            $this->info("✅ Marked {$expiredCount} pending payments as expired");
        } else {
            $this->info("ℹ️  No expired payments found");
        }
        
        // Optional: Delete very old expired payments (older than 30 days)
        if ($this->confirm('Delete expired payments older than 30 days?', false)) {
            $deletedCount = InvoiceHistory::where('status', 'expired')
                ->where('updated_at', '<', now()->subDays(30))
                ->delete();
                
            if ($deletedCount > 0) {
                $this->info("🗑️  Deleted {$deletedCount} old expired payments");
            }
        }
        
        return Command::SUCCESS;
    }
}
