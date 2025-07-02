<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            // Kolom untuk metode pembayaran (transfer_bank, payment_gateway)
            $table->enum('payment_method', ['transfer_bank', 'payment_gateway'])
                  ->nullable() // Bisa nullable jika ada kasus yang tidak memerlukan pilihan ini segera
                  ->after('payment_status');

            // Kolom untuk path bukti pembayaran (untuk transfer bank)
            $table->string('payment_proof_path')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_proof_path');
        });
    }
};
