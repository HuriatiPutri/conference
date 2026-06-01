<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('membership_benefit_id')->nullable()->constrained('membership_benefits')->nullOnDelete();
            $table->foreignId('package_benefit_id')->nullable()->constrained('package_benefits')->nullOnDelete();
            $table->string('benefit_type');
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->decimal('consumed_value', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['membership_id', 'membership_benefit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_usages');
    }
};