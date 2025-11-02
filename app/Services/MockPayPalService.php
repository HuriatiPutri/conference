<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockPayPalService
{
    /**
     * Create mock PayPal payment for testing
     */
    public function createPayment($amount, $currency, $description, $returnUrl, $cancelUrl)
    {
        // Mock payment creation
        $paymentId = 'MOCK-PAYMENT-' . uniqid();
        
        Log::info('Mock PayPal Payment Created', [
            'payment_id' => $paymentId,
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description
        ]);

        // For testing, return mock approval URL that redirects back with mock data
        $mockApprovalUrl = $returnUrl . '?paymentId=' . $paymentId . '&PayerID=MOCK-PAYER-' . uniqid();
        
        return [
            'payment_id' => $paymentId,
            'approval_url' => $mockApprovalUrl
        ];
    }

    /**
     * Execute mock PayPal payment
     */
    public function executePayment($paymentId, $payerId)
    {
        Log::info('Mock PayPal Payment Executed', [
            'payment_id' => $paymentId,
            'payer_id' => $payerId
        ]);

        // Mock successful payment execution
        return [
            'id' => $paymentId,
            'state' => 'approved',
            'payer' => [
                'payer_info' => [
                    'payer_id' => $payerId
                ]
            ]
        ];
    }

    /**
     * Get mock payment details
     */
    public function getPaymentDetails($paymentId)
    {
        return [
            'id' => $paymentId,
            'state' => 'approved'
        ];
    }
}