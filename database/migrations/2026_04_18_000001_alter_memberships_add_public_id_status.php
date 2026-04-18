<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            // Add public_id for URL-safe routing
            $table->string('public_id')->unique()->after('id');

            // Membership activation status (payment is tracked in invoice_history)
            $table->enum('status', ['pending', 'active', 'expired'])->default('pending')->after('end_date');

            // user_id is nullable until set-password step is completed
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['public_id', 'status']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
