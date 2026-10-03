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
        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->decimal('original_fee', 10, 2)->nullable()->after('full_paper_path');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('original_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->dropColumn(['original_fee', 'discount_amount']);
        });
    }
};
