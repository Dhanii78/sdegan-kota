<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Exports\RekapExport;
use App\Exports\HetExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class RekapVeriController extends Controller
{
    // Ambil semua data dari tabel rekap
    public function index(){
        $rekap = DB::table('rekap')->orderBy('TANGGAL', 'desc')->get();
        $het = DB::table('het')->orderBy('TANGGAL', 'desc')->limit(1)->get();
    
        $rekap_columns = Schema::getColumnListing('rekap');
        $het_columns = Schema::getColumnListing('het');
    
        return view('dashboard_verifikator.rekap_data', compact('rekap', 'het', 'rekap_columns', 'het_columns'));
    }

    public function exportRekap(){
        return Excel::download(new RekapExport, 'rekap.xlsx');
    }

    public function exportHet(){
        return Excel::download(new HetExport, 'het.xlsx');
    }
}
