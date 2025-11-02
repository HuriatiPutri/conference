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
            $table->text('loa_authors')->nullable()->after('loa_approved_at');
            $table->string('loa_joiv_volume')->nullable()->after('loa_authors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->dropColumn(['loa_authors', 'loa_joiv_volume']);
        });
    }
};
