<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Log;

class TestPayPalConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PayPal connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing PayPal Configuration...');
        
        // Check environment variables
        $this->info('=== Environment Variables ===');
        $mode = config('paypal.mode');
        $clientId = config('paypal.client_id');
        $clientSecret = config('paypal.client_secret');
        
        $this->info("Mode: {$mode}");
        $this->info("Client ID: " . ($clientId ? substr($clientId, 0, 10) . '...' : 'NOT SET'));
        $this->info("Client Secret: " . ($clientSecret ? substr($clientSecret, 0, 10) . '...' : 'NOT SET'));
        $this->info("Base URL: " . ($mode === 'live' ? config('paypal.live_url') : config('paypal.sandbox_url')));
        
        if (empty($clientId) || empty($clientSecret)) {
            $this->error('❌ PayPal credentials are not configured!');
            $this->info('Please set the following environment variables:');
            if ($mode === 'sandbox') {
                $this->info('- PAYPAL_SANDBOX_CLIENT_ID');
                $this->info('- PAYPAL_SANDBOX_CLIENT_SECRET');
            } else {
                $this->info('- PAYPAL_LIVE_CLIENT_ID');
                $this->info('- PAYPAL_LIVE_CLIENT_SECRET');
            }
            return 1;
        }
        
        // Test PayPal service
        $this->info('=== Testing PayPal Service ===');
        try {
            $paypalService = new PayPalService();
            $this->info('✅ PayPal service initialized successfully');
            
            // Test creating a dummy payment (this will fail at payment creation but will test auth)
            $this->info('Testing PayPal API connection...');
            
            // Use reflection to test getAccessToken method
            $reflection = new \ReflectionClass($paypalService);
            $method = $reflection->getMethod('getAccessToken');
            $method->setAccessible(true);
            
            $accessToken = $method->invoke($paypalService);
            
            if ($accessToken) {
                $this->info('✅ Successfully obtained PayPal access token');
                $this->info("Token preview: " . substr($accessToken, 0, 20) . '...');
            } else {
                $this->error('❌ Failed to obtain access token');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ PayPal connection failed: ' . $e->getMessage());
            $this->info('Check the Laravel logs for more details.');
            return 1;
        }
        
        $this->info('=== Test Completed Successfully ===');
        $this->info('PayPal is configured correctly and accessible.');
        
        return 0;
    }
}