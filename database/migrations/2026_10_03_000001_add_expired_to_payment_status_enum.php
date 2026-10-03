<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE joiv_registrations MODIFY COLUMN payment_status ENUM('pending_payment', 'paid', 'cancelled', 'refunded', 'expired') NOT NULL DEFAULT 'pending_payment'");
        DB::statement("ALTER TABLE audiences MODIFY COLUMN payment_status ENUM('pending_payment', 'paid', 'cancelled', 'refunded', 'expired') NOT NULL DEFAULT 'pending_payment'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE joiv_registrations MODIFY COLUMN payment_status ENUM('pending_payment', 'paid', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending_payment'");
        DB::statement("ALTER TABLE audiences MODIFY COLUMN payment_status ENUM('pending_payment', 'paid', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending_payment'");
    }
};
