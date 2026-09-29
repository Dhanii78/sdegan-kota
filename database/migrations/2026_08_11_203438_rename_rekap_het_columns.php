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
        Schema::table('rekap', function (Blueprint $table) {
            $table->renameColumn('Gula Pasir Konsumsi', 'Gula Pasir');
            $table->renameColumn('Minyak Goreng kms Sederhana', 'Minyakita');
            $table->renameColumn('Daging Sapi Murni', 'Daging Sapi');
            $table->renameColumn('Cabai Rawit merah', 'Cabai Rawit Merah');
            $table->renameColumn('Kedelai Biji kering Impor', 'Kedelai Biji Kering Impor');
        });

        Schema::table('het', function (Blueprint $table) {
            $table->renameColumn('Gula Pasir Konsumsi', 'Gula Pasir');
            $table->renameColumn('Minyak Goreng kms Sederhana', 'Minyakita');
            $table->renameColumn('Daging Sapi Murni', 'Daging Sapi');
            $table->renameColumn('Cabai Rawit merah', 'Cabai Rawit Merah');
            $table->renameColumn('Kedelai Biji kering Impor', 'Kedelai Biji Kering Impor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekap', function (Blueprint $table) {
            $table->renameColumn('Gula Pasir', 'Gula Pasir Konsumsi');
            $table->renameColumn('Minyakita', 'Minyak Goreng kms Sederhana');
            $table->renameColumn('Daging Sapi', 'Daging Sapi Murni');
            $table->renameColumn('Cabai Rawit Merah', 'Cabai Rawit merah');
            $table->renameColumn('Kedelai Biji Kering Impor', 'Kedelai Biji kering Impor');
        });

        Schema::table('het', function (Blueprint $table) {
            $table->renameColumn('Gula Pasir', 'Gula Pasir Konsumsi');
            $table->renameColumn('Minyakita', 'Minyak Goreng kms Sederhana');
            $table->renameColumn('Daging Sapi', 'Daging Sapi Murni');
            $table->renameColumn('Cabai Rawit Merah', 'Cabai Rawit merah');
            $table->renameColumn('Kedelai Biji Kering Impor', 'Kedelai Biji kering Impor');
        });
    }
};
