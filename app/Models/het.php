<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class het extends Model
{
    use HasFactory;
    protected $table = 'het';

    public $timestamps = false;

    protected $primaryKey = 'tanggal';

    protected $fillable = [
        'tanggal',
        'Beras Premium',
        'Beras Medium',	
        'Gula Pasir',
        'Minyakita',
        'Minyak Goreng Curah',	
        'Daging Sapi',	
        'Daging Ayam Ras',	
        'Telur Ayam Ras',	
        'Bawang Merah',	
        'Bawang Putih Bonggol',	
        'Cabai Merah Besar',	
        'Cabai Merah Keriting',	
        'Cabai Rawit Merah',	
        'Kedelai Biji Kering Impor',	
        'Jagung Pipilan Kering',	
    ];
}
