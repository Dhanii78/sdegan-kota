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
        Schema::table('dataKomoditas', function (Blueprint $table) {
            // Memeriksa apakah kolom belum ada sebelum menambahkannya untuk menghindari error
            if (!Schema::hasColumn('dataKomoditas', 'harga_hethap')) {
                $table->integer('harga_hethap')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dataKomoditas', function (Blueprint $table) {
            if (Schema::hasColumn('dataKomoditas', 'harga_hethap')) {
                $table->dropColumn('harga_hethap');
            }
        });
    }
};