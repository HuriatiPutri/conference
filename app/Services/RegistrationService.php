<?php
namespace App\Services;

use App\Models\Audience;
use App\Models\Conference;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function registerWithBankTransfer(
        Conference $conference,
        array $registrationData,
        string $paymentProofPath
    ): Audience {
        return DB::transaction(function () use ($conference, $registrationData, $paymentProofPath) {

            $audience = app(AudienceService::class)
                ->create($conference, $registrationData, 'transfer_bank', $paymentProofPath);

            app(InvoiceService::class)->createForAudience(
                audience: $audience,
                gateway: 'manual',
                method: 'transfer_bank',
                amount: $audience->paid_fee,
                proof: $paymentProofPath,
                status: 'pending'
            );

            return $audience;
        });
    }

    public function registerPendingGateway(
        Conference $conference,
        array $registrationData
    ): Audience {
        return DB::transaction(function () use ($conference, $registrationData) {

            $audience = app(AudienceService::class)
                ->create($conference, $registrationData, 'payment_gateway', null, 'pending_payment');

            app(InvoiceService::class)->createForAudience(
                audience: $audience,
                gateway: 'paypal',
                method: 'payment_gateway',
                amount: $audience->paid_fee,
                status: 'pending'
            );

            return $audience;
        });
    }
}