<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class sumber_data extends Model
{
    use HasFactory;
    protected $table = 'sumber_data';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama_komoditas',
        'satuan',
        'hethap',
        'intervensi',
        'waspada',
        'harga_intervensi',
        'harga_waspada',
        'cv',
        'sumber_hethap',
        'sumber_indikator',
        'batasan_cv',
    ];  
}
