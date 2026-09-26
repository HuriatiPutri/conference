<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Mengubah nama kolom price menjadi price_idr
            $table->renameColumn('price', 'price_idr');

            // Menambahkan kolom price_usd setelah price_idr
            // Sesuaikan tipe datanya (misal: decimal)
            $table->decimal('price_usd', 15, 2)->after('price_idr')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Mengembalikan nama kolom jika di-rollback
            $table->renameColumn('price_idr', 'price');

            // Menghapus kolom price_usd
            $table->dropColumn('price_usd');
        });
    }
};