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
        Schema::table('conferences', function (Blueprint $table) {
            if (!Schema::hasColumn('conferences', 'registration_start_date')) {
                $table->date('registration_start_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('conferences', 'registration_end_date')) {
                $table->date('registration_end_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('conferences', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            if (Schema::hasColumn('conferences', 'registration_end_date')) {
                $table->dropColumn('registration_end_date');
            }
            if (Schema::hasColumn('conferences', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
