<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->string('membership_number')->unique();

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // optional indexes (recommended)
            $table->index('user_id');
            $table->index('membership_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};