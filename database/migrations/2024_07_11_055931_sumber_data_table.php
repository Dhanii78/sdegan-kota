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
        Schema::create('sumber_data', function(Blueprint $table){
            $table->id();
            $table->string('nama_komoditas');
            $table->string('satuan');
            $table->integer('het/hap');
            $table->integer('intervensi');
            $table->integer('waspada');
            $table->integer('harga_intervensi');
            $table->integer('harga_waspada');
            $table->integer('cv');
            $table->string('sumber_het/hap');
            $table->string('sumber_indikator');
            $table->string('batasan_cv');
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
