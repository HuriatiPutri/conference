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
            // Change audience_id to nullable since PayPal payments are created before audience
            $table->unsignedBigInteger('audience_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            // Revert audience_id back to not nullable
            $table->unsignedBigInteger('audience_id')->nullable(false)->change();
        });
    }
};
