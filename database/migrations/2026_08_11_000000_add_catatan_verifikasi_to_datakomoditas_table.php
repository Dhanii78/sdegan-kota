<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('datakomoditas', 'catatan_verifikasi')) {
            Schema::table('datakomoditas', function (Blueprint $table) {
                $table->text('catatan_verifikasi')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('datakomoditas', 'catatan_verifikasi')) {
            Schema::table('datakomoditas', function (Blueprint $table) {
                $table->dropColumn('catatan_verifikasi');
            });
        }
    }
};
