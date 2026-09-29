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
        $columns = [
            'Beras Premium', 'Beras Medium', 'Gula Pasir', 'Minyakita', 'Minyak Goreng Curah',
            'Daging Sapi', 'Daging Ayam Ras', 'Telur Ayam Ras', 'Bawang Merah', 'Bawang Putih Bonggol',
            'Cabai Merah Besar', 'Cabai Merah Keriting', 'Cabai Rawit Merah', 'Kedelai Biji Kering Impor',
            'Jagung Pipilan Kering', 'Tepung Terigu', 'Kentang', 'Tomat'
        ];

        Schema::table('rekap', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('rekap', $column)) {
                    $table->integer($column)->nullable()->change();
                }
            }
        });

        Schema::table('het', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('het', $column)) {
                    $table->integer($column)->nullable()->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
