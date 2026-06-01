<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherValidationController extends Controller
{
    public function check(Request $request, string $code)
    {
        $request->validate([
            'transaction_type' => 'required|in:conference_registration,joiv_article,membership_registration',
            'email' => 'required|email',
        ]);

        $normalizedCode = strtoupper(trim($code));
        $voucher = Voucher::where('code', $normalizedCode)->first();

        $response = $this->validateVoucherCode($voucher, $request);

        return response()->json($response, 200);
    }

    private function validateVoucherCode($voucher, $request): array
    {
        if (!$voucher) {
            return ['valid' => false, 'message' => 'Voucher code is invalid.'];
        }

        if (!$voucher->isValidFor($request->transaction_type) || $this->alreadyClaimed($voucher, $request)) {
            $message = !$voucher->isValidFor($request->transaction_type)
                ? 'Voucher is not valid for this transaction or quota has ended.'
                : 'You have already claimed this voucher for this transaction type.';
            return ['valid' => false, 'message' => $message];
        }

        return [
            'valid' => true,
            'message' => 'Valid voucher code.',
            'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value,
            'discount_description' => $voucher->discount_description,
        ];
    }

    private function alreadyClaimed($voucher, $request): bool
    {
        return \App\Models\VoucherClaim::where('voucher_id', $voucher->id)
            ->where('email', $request->email)
            ->where('transaction_type', $request->transaction_type)
            ->exists();
    }
}
