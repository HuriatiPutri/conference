<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            // 1. Tambah kolom polymorphic
            $table->unsignedBigInteger('reference_id')->nullable()->after('public_id');
            $table->string('reference_type')->nullable()->after('reference_id');

            $table->index(['reference_id', 'reference_type'], 'invoice_history_reference_index');
        });

        /*
         |------------------------------------------------------------
         | 2. Migrasi data lama → polymorphic
         |------------------------------------------------------------
         | audience_id → App\Models\Audience
         | joiv_registration_id → App\Models\JoivRegistration
         |
         | Membership belum ada sebelumnya → skip
         */

        DB::statement("
            UPDATE invoice_history
            SET reference_id = audience_id,
                reference_type = 'App\\\\Models\\\\Audience'
            WHERE audience_id IS NOT NULL
        ");

        DB::statement("
            UPDATE invoice_history
            SET reference_id = joiv_registration_id,
                reference_type = 'App\\\\Models\\\\JoivRegistration'
            WHERE joiv_registration_id IS NOT NULL
        ");

        /*
         |------------------------------------------------------------
         | 3. Hapus kolom lama
         |------------------------------------------------------------
         */
        Schema::table('invoice_history', function (Blueprint $table) {
            $table->dropColumn([
                'audience_id',
                'joiv_registration_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('invoice_history', function (Blueprint $table) {
            // restore kolom lama
            $table->unsignedBigInteger('audience_id')->nullable();
            $table->unsignedBigInteger('joiv_registration_id')->nullable();

            $table->dropIndex('invoice_history_reference_index');
            $table->dropColumn(['reference_id', 'reference_type']);
        });
    }
};
