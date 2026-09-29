<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('discount_value_usd', 8, 2)->nullable()->default(0)->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('discount_value_usd');
        });
    }
};
