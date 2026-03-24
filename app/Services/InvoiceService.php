<?php
namespace App\Services;

use App\Models\Audience;
use App\Models\InvoiceHistory;

class InvoiceService
{
    public function createForAudience(
        Audience $audience,
        string $gateway,
        string $method,
        float $amount,
        ?string $proof = null,
        string $status = 'pending'
    ): InvoiceHistory {

        return $audience->invoices()->create([
            'payment_gateway' => $gateway,
            'payment_method' => $method,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => $status,
            'payment_proof' => $proof,
            'payment_initiated_at' => now(),
        ]);
    }
}