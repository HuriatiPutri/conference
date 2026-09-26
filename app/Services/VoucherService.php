<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    public function claimOrFail(?string $code, string $transactionType, string $email): ?Voucher
    {
        if (!$code) {
            return null;
        }

        $normalizedCode = strtoupper(trim($code));

        return DB::transaction(function () use ($normalizedCode, $transactionType, $email) {
            $voucher = Voucher::where('code', $normalizedCode)->lockForUpdate()->first();

            if (!$voucher) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Voucher code is invalid.',
                ]);
            }

            if (!$voucher->isValidFor($transactionType)) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Voucher is not valid for this transaction or quota has ended.',
                ]);
            }

            $existingClaim = VoucherClaim::where('voucher_id', $voucher->id)
                ->where('email', $email)
                ->where('transaction_type', $transactionType)
                ->exists();

            if ($existingClaim) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'You have already claimed this voucher for this transaction type.',
                ]);
            }

            $voucher->increment('used_count');

            VoucherClaim::create([
                'voucher_id' => $voucher->id,
                'email' => $email,
                'transaction_type' => $transactionType,
            ]);

            return $voucher->fresh();
        });
    }

    public function calculateDiscount(?Voucher $voucher, float $baseAmount): float
    {
        if (!$voucher) {
            return 0;
        }

        if ($voucher->discount_type === 'percent') {
            return round(($baseAmount * $voucher->discount_value) / 100, 2);
        }

        if ($voucher->discount_type === 'fixed') {
            return min($voucher->discount_value, $baseAmount);
        }

        return 0;
    }
}
