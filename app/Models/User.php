<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel database.
     *
     * Jika tabel user kamu memang "users", biarkan seperti ini.
     */
    protected $table = 'pengguna';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'email',
        'password',
        'role',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    /**
     * Kolom yang disembunyikan ketika data user
     * dikembalikan dalam JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}