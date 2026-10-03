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
        Schema::table('audiences', function (Blueprint $table) {
            if (!Schema::hasColumn('audiences', 'original_fee')) {
                $table->decimal('original_fee', 15, 2)->nullable()->after('presentation_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            if (Schema::hasColumn('audiences', 'original_fee')) {
                $table->dropColumn('original_fee');
            }
        });
    }
};
