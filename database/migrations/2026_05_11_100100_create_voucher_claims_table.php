<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('voucher_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
            $table->string('email');
            $table->enum('transaction_type', ['conference_registration', 'joiv_article', 'membership_registration']);
            $table->timestamps();

            $table->unique(['voucher_id', 'email', 'transaction_type'], 'voucher_claims_unique_per_flow');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_claims');
    }
};
