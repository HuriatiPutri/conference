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
        Schema::table('invoice_history', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['conference_id']);
        });
        
        // Drop primary key and modify column
        DB::statement('ALTER TABLE invoice_history DROP PRIMARY KEY');
        DB::statement('ALTER TABLE invoice_history MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
        DB::statement('ALTER TABLE invoice_history ADD PRIMARY KEY (id)');
        
        Schema::table('invoice_history', function (Blueprint $table) {
            // Re-add foreign key
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            $table->dropForeign(['conference_id']);
        });
        
        // Revert id back to varchar
        DB::statement('ALTER TABLE invoice_history MODIFY id VARCHAR(100) NOT NULL');
        
        Schema::table('invoice_history', function (Blueprint $table) {
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
    }
};
