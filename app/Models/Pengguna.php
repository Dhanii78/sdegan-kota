<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasFactory;
    use HasApiTokens, Notifiable;
    
    protected $table = 'pengguna';
    public $incrementing = false; // Tidak menggunakan auto-increment

    protected $fillable = [
        'id', 'nama', 'email', 'role', 'password', 'password_expires_at'
    ];

    // Jika tidak ingin menggunakan timestamp created_at dan updated_at
    public $timestamps = false;

    // Menambahkan kolom yang tidak boleh diakses secara massal
    protected $hidden = [
        'password',
    ];

    // Jika Anda menggunakan cast pada atribut, Anda dapat menambahkannya di sini
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}

