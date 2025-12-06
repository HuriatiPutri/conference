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
            $table->unsignedBigInteger('joiv_registration_id')->nullable()->after('audience_id');
            $table->foreign('joiv_registration_id')->references('id')->on('joiv_registrations')->onDelete('cascade');
            $table->index('joiv_registration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            $table->dropForeign(['joiv_registration_id']);
            $table->dropColumn('joiv_registration_id');
        });
    }
};
