<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable() // karena awalnya audience belum jadi member
                ->after('id')
                ->constrained('users')
                ->nullOnDelete(); // kalau member dihapus → tidak error
        });
    }

    public function down(): void
    {
        Schema::table('joiv_registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
