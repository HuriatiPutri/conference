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
            // Kolom 'Inisial'
            $table->string('initial')->nullable()->after('name');

            // Kolom 'Cover Poster' (untuk menyimpan path file)
            $table->string('cover_poster_path')->nullable()->after('initial');

            // Kolom 'Tanggal' (tipe date)
            $table->date('date')->nullable()->after('cover_poster_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            // Hapus kolom saat rollback
            $table->dropColumn('initial');
            $table->dropColumn('cover_poster_path');
            $table->dropColumn('date');
        });
    }
};
