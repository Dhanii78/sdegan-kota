<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;
    protected $table = 'datakomoditas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'nama_komoditas',
        'tanggal',
        'harga_het',
        'harga_kemarin',
        'harga_hari_ini',
        'status_verifikasi',
        'catatan_verifikasi',
    ];
    protected $dates = ['created_at'];

    //public $timestamps = false;
}
