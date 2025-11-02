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
        // Update existing records to have proper conference_id from audience relationship
        DB::statement('
            UPDATE invoice_history ih 
            JOIN audiences a ON ih.audience_id = a.id 
            SET ih.conference_id = a.conference_id 
            WHERE ih.conference_id IS NULL OR ih.conference_id = 0
        ');
        
        Schema::table('invoice_history', function (Blueprint $table) {
            // Add indexes for better performance (if not exists)
            $table->index(['transaction_id', 'payment_gateway']);
            $table->index(['status', 'payment_gateway']);
            $table->index('audience_id');
            
            // Add foreign keys
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
            $table->dropIndex(['audience_id']);
            
            // Add back old columns
            $table->string('snap_token', 225)->nullable();
            $table->string('capture_id', 225)->nullable();
            $table->string('redirect_url', 225)->nullable();
            $table->integer('expired_at')->nullable();
        });
    }
};
