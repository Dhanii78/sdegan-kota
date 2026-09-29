<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'id' => 1,
                'nama' => 'Admin Sistem',
                'email' => 'admin2@email.com',
                'role' => 'admin',
                'level' => '1',
                'password' => Hash::make('password123'),
            ],
            [
                'id' => 2,
                'nama' => 'Operator Dinas',
                'email' => 'operator2@email.com',
                'role' => 'operator',
                'level' => '2',
                'password' => Hash::make('password123'),
            ],
            [
                'id' => 3,
                'nama' => 'Verifikator Data',
                'email' => 'verifikator2@email.com',
                'role' => 'verifikator',
                'level' => '3',
                'password' => Hash::make('password123'),
            ]
        ]);
    }
}