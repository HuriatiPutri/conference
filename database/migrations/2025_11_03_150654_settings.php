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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('site_name')->nullable();
                $table->string('site_logo_url')->nullable();
                $table->string('site_favicon_url')->nullable();
                $table->string('stamp_receipt_path')->nullable();
                $table->string('stamp_loa_path')->nullable();
                $table->string('loa_graphic_path')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'site_logo_url')) {
                    $table->string('site_logo_url')->nullable()->after('site_name');
                }
                if (!Schema::hasColumn('settings', 'site_favicon_url')) {
                    $table->string('site_favicon_url')->nullable()->after('site_logo_url');
                }
                if (!Schema::hasColumn('settings', 'stamp_receipt_path')) {
                    $table->string('stamp_receipt_path')->nullable()->after('site_favicon_url');
                }
                if (!Schema::hasColumn('settings', 'stamp_loa_path')) {
                    $table->string('stamp_loa_path')->nullable()->after('stamp_receipt_path');
                }
                if (!Schema::hasColumn('settings', 'loa_graphic_path')) {
                    $table->string('loa_graphic_path')->nullable()->after('stamp_loa_path');
                }
                if (!Schema::hasColumn('settings', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (Schema::hasColumn('settings', 'site_logo_url')) {
                    $table->dropColumn('site_logo_url');
                }
                if (Schema::hasColumn('settings', 'site_favicon_url')) {
                    $table->dropColumn('site_favicon_url');
                }
                if (Schema::hasColumn('settings', 'stamp_receipt_path')) {
                    $table->dropColumn('stamp_receipt_path');
                }
                if (Schema::hasColumn('settings', 'stamp_loa_path')) {
                    $table->dropColumn('stamp_loa_path');
                }
                if (Schema::hasColumn('settings', 'loa_graphic_path')) {
                    $table->dropColumn('loa_graphic_path');
                }
            });
        }
    }
};
