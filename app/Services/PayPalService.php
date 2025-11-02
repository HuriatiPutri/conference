<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $mode;

    public function __construct()
    {
        $this->clientId = config('paypal.client_id');
        $this->clientSecret = config('paypal.client_secret');
        $this->mode = config('paypal.mode');
        $this->baseUrl = $this->mode === 'live' ? config('paypal.live_url') : config('paypal.sandbox_url');
        
        // Validate required configuration
        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \Exception('PayPal credentials not configured. Please check your .env file.');
        }
    }

    /**
     * Get PayPal access token
     */
    private function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        // Log the error for debugging
        Log::error('PayPal access token request failed', [
            'status' => $response->status(),
            'response' => $response->json(),
            'mode' => $this->mode,
            'base_url' => $this->baseUrl
        ]);

        throw new \Exception('Failed to get PayPal access token. Please check your PayPal credentials.');
    }

    /**
     * Create PayPal payment
     */
    public function createPayment($amount, $currency, $description, $returnUrl, $cancelUrl)
    {
        $accessToken = $this->getAccessToken();

        $paymentData = [
            'intent' => 'sale',
            'payer' => [
                'payment_method' => 'paypal'
            ],
            'transactions' => [
                [
                    'amount' => [
                        'total' => number_format($amount, 2, '.', ''),
                        'currency' => $currency
                    ],
                    'description' => $description
                ]
            ],
            'redirect_urls' => [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post($this->baseUrl . '/v1/payments/payment', $paymentData);

        // Check if response is successful
        if ($response->successful()) {
            $paymentData = $response->json();
            
            // Debug: Log full response
            Log::info('PayPal Payment Creation Success', $paymentData);
            
            // Find approval URL
            $approvalUrl = null;
            if (isset($paymentData['links'])) {
                foreach ($paymentData['links'] as $link) {
                    if ($link['rel'] === 'approval_url') {
                        $approvalUrl = $link['href'];
                        break;
                    }
                }
            }

            if (!$approvalUrl) {
                Log::error('PayPal approval URL not found', ['payment_data' => $paymentData]);
                throw new \Exception('PayPal approval URL not found in response');
            }

            return [
                'payment_id' => $paymentData['id'],
                'approval_url' => $approvalUrl,
                'status' => $paymentData['state'] ?? 'unknown'
            ];
        }

        // Log the error response
        $errorData = $response->json();
        Log::error('PayPal payment creation failed', [
            'status' => $response->status(),
            'response' => $errorData,
            'request_data' => $paymentData
        ]);
        
        // Extract error message if available
        $errorMessage = 'Failed to create PayPal payment';
        if (isset($errorData['message'])) {
            $errorMessage = $errorData['message'];
        } elseif (isset($errorData['error_description'])) {
            $errorMessage = $errorData['error_description'];
        }
        
        throw new \Exception($errorMessage);
    }

    /**
     * Execute PayPal payment
     */
    public function executePayment($paymentId, $payerId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post($this->baseUrl . "/v1/payments/payment/{$paymentId}/execute", [
                'payer_id' => $payerId
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('PayPal payment execution failed', ['response' => $response->json()]);
        throw new \Exception('Failed to execute PayPal payment');
    }

    /**
     * Get payment details
     */
    public function getPaymentDetails($paymentId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get($this->baseUrl . "/v1/payments/payment/{$paymentId}");

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to get PayPal payment details');
    }
}