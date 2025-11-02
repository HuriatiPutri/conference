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
        Schema::table('invoice_history', function (Blueprint $table) {
            // Add new columns for PayPal integration
            $table->string('public_id')->unique()->after('id');
            $table->unsignedBigInteger('conference_id')->nullable()->after('audience_id');
            $table->string('payment_gateway')->default('paypal')->after('conference_id');
            
            // Add PayPal specific columns
            $table->string('transaction_id')->nullable()->after('payment_method'); // PayPal payment ID
            $table->string('payer_id')->nullable()->after('transaction_id'); // PayPal payer ID
            $table->string('invoice_number')->nullable()->after('payer_id'); // Our internal invoice number
            
            // Update amount to decimal
            $table->decimal('amount', 10, 2)->change();
            $table->string('currency', 3)->default('USD')->after('amount');
            
            // Add JSON columns for PayPal responses
            $table->json('gateway_response')->nullable()->after('status'); // Full PayPal response
            $table->json('execution_response')->nullable()->after('gateway_response'); // PayPal execution response
            
            // Add description and URL columns
            $table->text('description')->nullable()->after('execution_response');
            $table->string('return_url')->nullable()->after('description');
            $table->string('cancel_url')->nullable()->after('return_url');
            
            // Add payment timestamps
            $table->timestamp('payment_initiated_at')->nullable()->after('cancel_url');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_initiated_at');
            
            // Add indexes without foreign keys for now
            $table->index(['transaction_id', 'payment_gateway']);
            $table->index(['status', 'payment_gateway']);
        });
        
        // Update existing records to have proper conference_id from audience relationship
        DB::statement('
            UPDATE invoice_history ih 
            JOIN audiences a ON ih.audience_id = a.id 
            SET ih.conference_id = a.conference_id 
            WHERE ih.conference_id IS NULL
        ');
        
        // Now add foreign keys after data is clean
        Schema::table('invoice_history', function (Blueprint $table) {
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            // Drop foreign keys and indexes
            $table->dropForeign(['conference_id']);
            $table->dropIndex(['transaction_id', 'payment_gateway']);
            $table->dropIndex(['status', 'payment_gateway']);
            
            // Drop new columns
            $table->dropColumn([
                'public_id', 'conference_id', 'payment_gateway', 'transaction_id', 
                'payer_id', 'invoice_number', 'currency', 'gateway_response', 
                'execution_response', 'description', 'return_url', 'cancel_url',
                'payment_initiated_at', 'payment_completed_at'
            ]);
            
            // Revert amount back to integer
            $table->integer('amount')->change();
        });
    }
};
