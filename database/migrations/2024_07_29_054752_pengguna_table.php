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
        Schema::create('pengguna', function(Blueprint $table){
            $table->bigInteger('id');
            $table->string('nama');
            $table->string('email');
            $table->enum('role', ['admin', 'operator', 'verifikator']); // Menambahkan kolom role
            $table->string('password');
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
