<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('audiences')
            ->where('payment_method', 'transfer_bank')
            ->whereNotNull('paid_fee')
            ->orderBy('id')
            ->chunkById(100, function ($audiences) {

                foreach ($audiences as $audience) {

                    $exists = DB::table('invoice_history')
                        ->where('reference_id', $audience->id)
                        ->where('reference_type', 'App\\Models\\Audience')
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('invoice_history')->insert([
                        'id' => 'TF-' . strtoupper(Str::random(10)),
                        'public_id' => 'INV-' . strtoupper(Str::random(12)),

                        // polymorphic relation
                        'reference_id' => $audience->id,
                        'reference_type' => 'App\\Models\\Audience',

                        'conference_id' => $audience->conference_id,

                        'payment_gateway' => 'manual',
                        'payment_method' => 'transfer_bank',

                        'amount' => $audience->paid_fee,
                        'currency' => 'USD',
                        'payment_proof_path' => $audience->payment_proof_path,
                        'status' => match ($audience->payment_status) {
                            'paid' => 'completed',
                            'pending_payment' => 'pending',
                            'cancelled' => 'failed',
                            'refunded' => 'refunded',
                            default => 'pending'
                        },

                        'description' => 'Manual bank transfer (migrated from audience)',

                        'payment_initiated_at' => $audience->created_at,
                        'payment_completed_at' => $audience->payment_status === 'paid'
                            ? $audience->updated_at
                            : null,

                        'created_at' => $audience->created_at,
                        'updated_at' => $audience->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('invoice_history')
            ->where('payment_gateway', 'manual')
            ->where('description', 'Manual bank transfer (migrated from audience)')
            ->delete();
    }
};