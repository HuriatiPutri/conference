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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            $table->foreignId('conference_id') // Foreign key ke tabel 'conferences'
                  ->constrained('conferences')
                  ->onDelete('cascade'); // Jika conference dihapus, registrasi juga dihapus

            $table->string('first_name');
            $table->string('last_name');
            $table->string('paper_title');
            $table->string('institution');
            $table->string('email');
            $table->string('phone_number'); 
            $table->string('country');
            $table->string('presentation_type'); // 'online_author', 'onsite', 'participant_only'
            $table->decimal('paid_fee', 10, 2)->nullable(); // Biaya yang dibayar (opsional, bisa dihitung otomatis)
            $table->string('full_paper_path')->nullable(); // Path ke file paper yang diupload

            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
