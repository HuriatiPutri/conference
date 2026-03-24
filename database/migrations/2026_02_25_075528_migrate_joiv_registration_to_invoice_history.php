<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('joiv_registrations')
            ->whereNotNull('paid_fee')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {

                foreach ($rows as $row) {

                    // skip jika sudah ada
                    $exists = DB::table('invoice_history')
                        ->where('reference_id', $row->id)
                        ->where('reference_type', 'App\\Models\\JoivRegistration')
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('invoice_history')->insert([
                        // 🔹 ID mengikuti standar kamu
                        'id' => 'TF-' . strtoupper(uniqid()),
                        'public_id' => 'INV-' . strtoupper(uniqid()),

                        // polymorphic
                        'reference_id' => $row->id,
                        'reference_type' => 'App\\Models\\JoivRegistration',

                        'conference_id' => $row->conference_id ?? null,

                        'payment_gateway' => 'manual',
                        'payment_method' => 'transfer_bank',

                        'amount' => $row->paid_fee,
                        'currency' => $row->currency ?? 'USD',

                        'status' => match ($row->payment_status) {
                            'paid' => 'completed',
                            'pending_payment' => 'pending',
                            'cancelled' => 'failed',
                            'refunded' => 'refunded',
                            default => 'pending'
                        },

                        'description' => 'Migrated from joiv_registration',

                        // 🔹 pindahkan bukti pembayaran
                        'payment_proof_path' => $row->payment_proof_path ?? null,

                        'payment_initiated_at' => $row->created_at,
                        'payment_completed_at' => $row->payment_status === 'paid'
                            ? $row->updated_at
                            : null,

                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('invoice_history')
            ->where('description', 'Migrated from joiv_registration')
            ->delete();
    }
};