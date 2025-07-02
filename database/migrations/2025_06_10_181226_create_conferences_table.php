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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom 'id' sebagai primary key (auto-incrementing)
            $table->string('name'); // Nama konferensi
            $table->string('initial')->nullable(); // Inisial konferensi, boleh kosong
            $table->string('cover_poster_path')->nullable(); // Path ke file poster, boleh kosong
            $table->date('date')->nullable(); // Tanggal konferensi (YYYY-MM-DD), boleh kosong
            $table->string('city'); // Kota penyelenggara
            $table->string('country'); // Negara penyelenggara
            $table->integer('year'); // Tahun penyelenggaraan
            $table->decimal('online_fee', 10, 2); // Biaya pendaftaran online (maks 10 digit total, 2 digit di belakang koma)
            $table->decimal('onsite_fee', 10, 2); // Biaya pendaftaran onsite
            $table->decimal('participant_fee', 10, 2); // Biaya untuk peserta saja
            $table->timestamps(); // Ini akan otomatis membuat kolom 'created_at' dan 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences'); // Jika di-rollback, tabel 'conferences' akan dihapus
    }
};