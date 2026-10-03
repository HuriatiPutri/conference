<?php

namespace App\Console\Commands;

use App\Models\Audience;
use App\Models\InvoiceHistory;
use App\Models\JoivRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:cleanup-expired 
                            {--hours=2 : Hours after which pending paypal sessions are considered expired}
                            {--days=2 : Days after which pending registrations are considered expired}';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup expired pending payments and registrations after 2 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $days = (int) $this->option('days');
        
        $this->info("Cleaning up PayPal pending sessions older than {$hours} hours...");
        
        // 1. Mark pending PayPal invoices older than specified hours as expired
        $expiredPaypalCount = InvoiceHistory::where('status', 'pending')
            ->where('payment_gateway', 'paypal')
            ->where('created_at', '<', now()->subHours($hours))
            ->update([
                'status' => 'expired',
                'execution_response' => [
                    'error' => "Payment session expired after {$hours} hours",
                    'expired_at' => now()->toISOString()
                ]
            ]);
        
        $this->info("✅ Marked {$expiredPaypalCount} pending PayPal sessions as expired");

        // 2. Mark uncompleted pending JOIV registrations older than specified days (default 2 days) as expired
        // Only expire if:
        // - No payment proof uploaded for bank transfer / unselected method
        // - Or paypal / payment gateway not completed
        $this->info("Cleaning up uncompleted pending JOIV registrations older than {$days} days...");
        $expiredJoivRegistrations = JoivRegistration::where('payment_status', 'pending_payment')
            ->where('created_at', '<', now()->subDays($days))
            ->where(function ($query) {
                $query->whereNull('payment_method')
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'transfer_bank')
                          ->whereNull('payment_proof_path');
                    })
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'payment_gateway')
                          ->whereNull('payment_proof_path');
                    });
            })
            ->get();

        $expiredJoivCount = 0;
        foreach ($expiredJoivRegistrations as $registration) {
            $registration->update(['payment_status' => 'expired']);

            // Expire related pending invoices
            $registration->invoices()
                ->where('status', 'pending')
                ->update([
                    'status' => 'expired',
                    'execution_response' => [
                        'error' => "Registration expired after {$days} days without payment continuation",
                        'expired_at' => now()->toISOString()
                    ]
                ]);

            // Notify user via email
            try {
                $registration->sendPaymentConfirmationEmail();
            } catch (\Exception $e) {
                Log::error("Failed to send expired payment email for JOIV registration {$registration->id}: " . $e->getMessage());
            }

            $expiredJoivCount++;
        }
        $this->info("✅ Marked {$expiredJoivCount} pending JOIV registrations as expired");

        // 3. Mark uncompleted pending Audience registrations older than specified days as expired
        $this->info("Cleaning up uncompleted pending Audience registrations older than {$days} days...");
        $expiredAudiences = Audience::where('payment_status', 'pending_payment')
            ->where('created_at', '<', now()->subDays($days))
            ->where(function ($query) {
                $query->whereNull('payment_method')
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'transfer_bank')
                          ->whereNull('payment_proof_path');
                    })
                    ->orWhere(function ($q) {
                        $q->where('payment_method', 'payment_gateway')
                          ->whereNull('payment_proof_path');
                    });
            })
            ->get();

        $expiredAudienceCount = 0;
        foreach ($expiredAudiences as $audience) {
            $audience->update(['payment_status' => 'expired']);

            $audience->invoices()
                ->where('status', 'pending')
                ->update([
                    'status' => 'expired',
                    'execution_response' => [
                        'error' => "Registration expired after {$days} days without payment continuation",
                        'expired_at' => now()->toISOString()
                    ]
                ]);

            $expiredAudienceCount++;
        }
        $this->info("✅ Marked {$expiredAudienceCount} pending Audience registrations as expired");

        // Optional: Delete very old expired payments (older than 30 days)
        if ($this->input->isInteractive() && $this->confirm('Delete expired payments older than 30 days?', false)) {
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
