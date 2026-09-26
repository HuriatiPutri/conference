<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('conference_id')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code', 6)->nullable()->after('voucher_id');
        });

        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('loa_volume_id')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code', 6)->nullable()->after('voucher_id');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('package_id')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code', 6)->nullable()->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });

        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });
    }
};
