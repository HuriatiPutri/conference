<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')
                ->constrained('packages')
                ->cascadeOnDelete();
            $table->foreignId('membership_benefit_id')
                ->constrained('membership_benefits')
                ->cascadeOnDelete();
            $table->string('value_type')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->decimal('max_value', 15, 2)->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['package_id', 'membership_benefit_id']);
            $table->index('value_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_benefits');
    }
};
