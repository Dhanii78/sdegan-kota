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
        Schema::create('het', function(Blueprint $table){
            $table->date('tanggal');
            $table->integer('Beras Premium');
            $table->integer('Beras Medium');
            $table->integer('Gula Pasir Konsumsi');
            $table->integer('Minyak Goreng kms Sederhana');
            $table->integer('Minyak Goreng Curah');
            $table->integer('Daging Sapi Murni');
            $table->integer('Daging Ayam Ras');
            $table->integer('Telur Ayam Ras');
            $table->integer('Bawang Merah');
            $table->integer('Bawang Putih Bonggol');
            $table->integer('Cabai Merah Besar');
            $table->integer('Cabai Merah Keriting');
            $table->integer('Cabai Rawit merah');
            $table->integer('Kedelai Biji kering Impor');
            $table->integer('Jagung Pipilan Kering');
            $table->integer('Tepung Terigu');
            $table->integer('Kentang');
            $table->integer('Tomat');
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
