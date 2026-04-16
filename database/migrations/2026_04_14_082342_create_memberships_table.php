<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');
              
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('institution')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('country');

            $table->foreignId('package_id')
                  ->constrained('packages')
                  ->onDelete('cascade');

            $table->date('start_date'); // start_at -> start_date
            $table->date('end_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};