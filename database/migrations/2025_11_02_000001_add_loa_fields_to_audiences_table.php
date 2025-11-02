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
            $table->enum('loa_status', ['pending', 'approved', 'rejected'])->default('pending')->after('payment_status');
            $table->text('loa_notes')->nullable()->after('loa_status');
            $table->timestamp('loa_approved_at')->nullable()->after('loa_notes');
            $table->text('loa_authors')->nullable()->after('loa_approved_at');
            $table->string('loa_joiv_volume')->nullable()->after('loa_authors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->dropColumn(['loa_status', 'loa_notes', 'loa_approved_at', 'loa_authors', 'loa_joiv_volume']);
        });
    }
};