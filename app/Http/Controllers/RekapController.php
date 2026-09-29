<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapExport;
use App\Exports\HetExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use App\Models\Data;
use App\Models\sumber_data;
use App\Models\rekap;
use App\Models\het;

class RekapController extends Controller
{
    public function index(){
        $rekap = DB::table('rekap')->orderBy('TANGGAL', 'desc')->get();
        $het = DB::table('het')->orderBy('TANGGAL', 'desc')->limit(1)->get();
    
        $rekap_columns = Schema::getColumnListing('rekap');
        $het_columns = Schema::getColumnListing('het');
    
        return view('dashboard_admin.sub_menu.rekap_data', compact('rekap', 'het', 'rekap_columns', 'het_columns'));
    }    
    public function exportRekap(){
        return Excel::download(new RekapExport, 'rekap.xlsx');
    }
    public function exportHet(){
        return Excel::download(new HetExport, 'het.xlsx');
    }

    public function deleteTanggal(Request $request)
    {
        $tanggal = $request->input('tanggal');

        if (!$tanggal) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }
                        
        // hapus semua data yang memiliki tanggal tersebut
        Data::where('tanggal', $tanggal)->delete();
        Rekap::where('tanggal', $tanggal)->delete();
        HET::where('tanggal', $tanggal)->delete();


        return redirect()->back()->with('success', 'Data yang dipilih berhasil dihapus.');
    }
}

