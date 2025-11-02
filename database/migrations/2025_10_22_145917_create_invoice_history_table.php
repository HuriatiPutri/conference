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
        Schema::create('invoice_history', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->unsignedBigInteger('audience_id');
            $table->unsignedBigInteger('conference_id');
            
            // Payment Gateway Information
            $table->string('payment_gateway')->default('paypal'); // paypal, stripe, etc
            $table->string('payment_method')->default('payment_gateway'); // payment_gateway, transfer_bank
            
            // Transaction Details
            $table->string('transaction_id')->nullable(); // PayPal payment ID
            $table->string('payer_id')->nullable(); // PayPal payer ID
            $table->string('invoice_number')->nullable(); // Our internal invoice number
            
            // Payment Information
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status'); // pending, completed, failed, cancelled
            
            // PayPal Response Data
            $table->json('gateway_response')->nullable(); // Full PayPal response
            $table->json('execution_response')->nullable(); // PayPal execution response
            
            // Additional Information
            $table->text('description')->nullable();
            $table->string('return_url')->nullable();
            $table->string('cancel_url')->nullable();
            
            // Timestamps
            $table->timestamp('payment_initiated_at')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('audience_id')->references('id')->on('audiences')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            
            // Indexes
            $table->index(['transaction_id', 'payment_gateway']);
            $table->index(['status', 'payment_gateway']);
            $table->index('audience_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_history');
    }
};
