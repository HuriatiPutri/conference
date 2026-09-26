<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('benefit_type');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('benefit_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_benefits');
    }
};
