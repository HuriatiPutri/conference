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
            $table->text('loa_authors')->nullable()->after('paper_title');
            $table->unsignedBigInteger('loa_volume_id')->nullable()->after('loa_authors');
            $table->timestamp('loa_approved_at')->nullable()->after('loa_volume_id');
            
            // Foreign key
            $table->foreign('loa_volume_id')->references('id')->on('loa_volume')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->dropForeign(['loa_volume_id']);
            $table->dropColumn(['loa_authors', 'loa_volume_id', 'loa_approved_at']);
        });
    }
};
