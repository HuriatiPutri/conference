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
        Schema::create('joiv_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('institution');
            $table->string('country', 2);
            $table->string('paper_id')->nullable();
            $table->string('paper_title');
            $table->string('full_paper_path')->nullable();
            $table->enum('payment_status', ['pending_payment', 'paid', 'cancelled', 'refunded'])->default('pending_payment');
            $table->enum('payment_method', ['transfer_bank', 'payment_gateway'])->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->decimal('paid_fee', 10, 2)->default(0);
            $table->string('public_id')->unique();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('email_address');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joiv_registrations');
    }
};
