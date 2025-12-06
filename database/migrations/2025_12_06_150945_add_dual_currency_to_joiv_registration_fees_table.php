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
        Schema::table('joiv_registration_fees', function (Blueprint $table) {
            // Rename fee_amount to usd_amount for clarity
            $table->renameColumn('fee_amount', 'usd_amount');
            
            // Add IDR amount column
            $table->decimal('idr_amount', 15, 2)->after('usd_amount');
            
            // Remove currency column as we now have specific amounts for each currency
            $table->dropColumn('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joiv_registration_fees', function (Blueprint $table) {
            // Restore currency column
            $table->string('currency', 3)->default('USD')->after('usd_amount');
            
            // Remove IDR amount column
            $table->dropColumn('idr_amount');
            
            // Rename back to fee_amount
            $table->renameColumn('usd_amount', 'fee_amount');
        });
    }
};
