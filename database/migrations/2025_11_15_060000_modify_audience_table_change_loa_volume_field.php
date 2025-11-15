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
            // Check if loa_joiv_volume column exists and drop it
            if (Schema::hasColumn('audiences', 'loa_joiv_volume')) {
                $table->dropColumn('loa_joiv_volume');
            }
            
            // Add new loa_volume_id column as foreign key
            $table->unsignedBigInteger('loa_volume_id')->nullable()->after('loa_status');
            
            // Add foreign key constraint
            $table->foreign('loa_volume_id')->references('id')->on('loa_volume')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['loa_volume_id']);
            
            // Drop the column
            $table->dropColumn('loa_volume_id');
            
            // Re-add the old column if needed
            $table->string('loa_joiv_volume')->nullable();
        });
    }
};