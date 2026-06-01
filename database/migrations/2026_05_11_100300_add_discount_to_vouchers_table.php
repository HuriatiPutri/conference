<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent')->after('applies_to');
            $table->decimal('discount_value', 8, 2)->default(0)->after('discount_type');
            $table->text('discount_description')->nullable()->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_description']);
        });
    }
};
