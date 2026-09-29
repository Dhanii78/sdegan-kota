<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\sumber_data;
use App\Models\rekap;
use App\Models\het;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Log;


class DataController extends Controller{

    // public function index(Request $request){
    //     Log::info('Fetching default paginated data.');
    //     $data = Data::orderBy('created_at', 'asc')->paginate(20);
    //     Log::info('Data retrieved:', ['count' => $data->count()]);
        
    //     return view('dashboard_admin.sub_menu.data_pangan', ['dataKomoditas' => $data]);
    // }

    public function index(Request $request)
    {
        Log::info('Fetching data pangan.');

        // Set default tanggal = hari ini
        $start = $request->start_date ?? date('Y-m-d');
        $end = $request->end_date ?? date('Y-m-d');

        $query = Data::query();

        // Selalu pakai filter (default atau dari user)
        $query->whereBetween('tanggal', [$start, $end]);

        // Ambil data
        $data = $query->orderBy('created_at', 'desc')->get();

        Log::info('Data retrieved:', ['count' => $data->count()]);

        return view('dashboard_admin.sub_menu.data_pangan', [
            'dataKomoditas' => $data
        ]);
    }

    public function indexoperator(Request $request) {
        Log::info('Request riwayat operator:', $request->all());

        $query = Data::query();

        if ($request->filled('search')) {
            $query->where('nama_komoditas', 'LIKE', '%' . $request->input('search') . '%');
        }

        if ($request->filled('month') && preg_match('/^\d{4}-\d{2}$/', $request->input('month'))) {
            [$year, $month] = explode('-', $request->input('month'));
            $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        switch ($request->input('status')) {
            case 'valid':
                $query->where('status_verifikasi', 'Valid');
                break;
            case 'pending':
                $query->whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi']);
                break;
            case 'rejected':
                $query->whereIn('status_verifikasi', [
                    'Tidak_valid',
                    'Tidak Valid',
                    'Harga_Tidak_Wajar',
                    'Data_Ganda',
                    'Revisi',
                ]);
                break;
        }

        $data = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get();

        Log::info('Riwayat operator retrieved:', ['count' => $data->count()]);

        return view('dashboard_operator.data_pangan', ['dataKomoditas' => $data]);
    }
    
    public function verifyadmin(Request $request){
        // Ambil input query dari request
        $query = $request->input('query');
    
        // Jika ada query, lakukan pencarian
        if ($query) {
            $data = Data::where(function($q) use ($query) {
                $q->where('nama_komoditas', 'like', "%$query%")
                    ->orWhere('harga_kemarin', 'like', "%$query%")
                    ->orWhere('harga_hari_ini', 'like', "%$query%")
                    ->orWhereDate('created_at', $query);
            })
            ->whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi'])
            ->orderBy('created_at', 'asc')
            ->get();
        } else {
            // Jika tidak ada query, ambil semua data yang belum diverifikasi
            $data = Data::whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi'])->orderBy('created_at', 'asc')->get();
        }

        // Kembalikan data ke view
        return view('dashboard_admin.sub_menu.verify_data', ['dataKomoditas' => $data]);
    }

    public function verifyverifikator(Request $request){
        // Ambil input query dari request
        $query = $request->input('query');
    
        // Jika ada query, lakukan pencarian
        if ($query) {
            $data = Data::where(function($q) use ($query) {
                $q->where('nama_komoditas', 'like', "%$query%")
                    ->orWhere('harga_kemarin', 'like', "%$query%")
                    ->orWhere('harga_hari_ini', 'like', "%$query%")
                    ->orWhereDate('created_at', $query);
            })
            ->whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi'])
            ->orderBy('created_at', 'asc')
            ->get();
        } else {
            // Jika tidak ada query, ambil semua data yang belum diverifikasi
            $data = Data::whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi'])->orderBy('created_at', 'asc')->get();
        }

        // Kembalikan data ke view
        return view('dashboard_verifikator.verify_data', ['dataKomoditas' => $data]);
    }

    public function create(){
        return view('dashboard_admin.sub_menu.input_entry');
    }

    public function createoperator(){
        return view('dashboard_operator.input_entry');
    }

    public function insert(Request $request){
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'nama_komoditas' => 'required|array',
            'nama_komoditas.*' => 'required|string|max:255',
            'harga_het' => 'required|array',
            'harga_het.*' => 'required|numeric',
            'harga_kemarin' => 'required|array',
            'harga_kemarin.*' => 'required|numeric',
            'harga_hari_ini' => 'required|array',
            'harga_hari_ini.*' => 'required|numeric',
        ]);
    
        foreach ($request->nama_komoditas as $index => $komoditas) {
            $data = new Data();
            $data->tanggal = $request->tanggal;
            $data->nama_komoditas = $komoditas;
            $data->harga_het = $request->harga_het[$index];
            $data->harga_kemarin = $request->harga_kemarin[$index];
            $data->harga_hari_ini = $request->harga_hari_ini[$index];
            $data->status_verifikasi = 'Belum_Diverifikasi';
            $data->save();
        }
    
        return redirect()->route('dashboard_admin.sub_menu.data_pangan')->with('success', 'Semua data berhasil disimpan!');
    }
    
    public function insertoperator(Request $request){
        $validatedData = $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'nama_komoditas' => 'required|array',
            'nama_komoditas.*' => 'required|string|max:255',
            'harga_het' => 'required|array',
            'harga_het.*' => 'required|numeric',
            'harga_kemarin' => 'required|array',
            'harga_kemarin.*' => 'required|numeric',
            'harga_hari_ini' => 'required|array',
            'harga_hari_ini.*' => 'required|numeric',
        ], [
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);

        // Cek apakah data untuk tanggal tersebut sudah diinput sebelumnya
        $exists = Data::whereDate('tanggal', $request->tanggal)->exists();
        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data untuk tanggal tersebut sudah diinput sebelumnya!');
        }
    
        foreach ($request->nama_komoditas as $index => $komoditas) {
            $data = new Data();
            $data->tanggal = $request->tanggal;
            $data->nama_komoditas = $komoditas;
            $data->harga_het = $request->harga_het[$index];
            $data->harga_kemarin = $request->harga_kemarin[$index];
            $data->harga_hari_ini = $request->harga_hari_ini[$index];
            $data->status_verifikasi = 'Belum_Diverifikasi';
            $data->save();
        }
    
        return redirect()
            ->route('dashboard_operator.data_pangan')
            ->with('success', 'Data berhasil dikirim untuk diverifikasi.')
            ->with('report_count', count($request->nama_komoditas));
    }
    
    public function edit($id){
        // Ambil data yang ingin diedit berdasarkan ID
        $data = Data::find($id);
        
        // Pastikan data ada
        if (!$data) {
        return redirect()->route('dashboard_admin.sub_menu.entry_data')->with('error', 'Data tidak ditemukan!');
        }
    
        // Kembalikan view dengan data yang ingin diedit
        return view('dashboard_admin.sub_menu.edit_entry', compact('data'));
        }

    public function editoperator($id){
        // Ambil data yang ingin diedit berdasarkan ID
        $data = Data::find($id);

        // Pastikan data ada
        if (!$data) {
            return redirect()->route('dashboard_operator.data_pangan')->with('error', 'Data tidak ditemukan!');
        }

        // Cek apakah data sudah divalidasi (tidak boleh diedit)
        if ($data->status_verifikasi === 'Valid') {
            return redirect()->route('dashboard_operator.data_pangan')->with('error', 'Data yang sudah divalidasi tidak dapat diedit!');
        }
    
        // Kembalikan view dengan data yang ingin diedit
        return view('dashboard_operator.edit_entry', compact('data'));
    }

        public function update(Request $request, $id)
        {
            // Ambil data yang ingin diperbarui berdasarkan ID
            $data = Data::find($id);
        
            // Pastikan data ada
            if (!$data) {
                return redirect()->route('dashboard_admin.sub_menu.entry_data')->with('error', 'Data tidak ditemukan!');
            }
        
            // Validasi input
            $validatedData = $request->validate([
                'tanggal' => 'required|date',
                'nama_komoditas' => 'required|string|max:255',
                'harga_het' => 'required|numeric',
                'harga_kemarin' => 'required|numeric',
                'harga_hari_ini' => 'required|numeric',
            ]);
        
            // Update data yang ada
            $data->tanggal = $validatedData['tanggal'];
            $data->nama_komoditas = $validatedData['nama_komoditas'];
            $data->harga_het = $validatedData['harga_het'];
            $data->harga_kemarin = $validatedData['harga_kemarin'];
            $data->harga_hari_ini = $validatedData['harga_hari_ini'];
            $data->status_verifikasi = 'Belum_Diverifikasi';
        
            // Simpan perubahan
            if ($data->save()) {
                return redirect()->route('dashboard_admin.sub_menu.data_pangan')->with('success', 'Data berhasil diperbarui!');
            } else {
                return redirect()->route('dashboard_admin.sub_menu.data_pangan')->with('error', 'Data gagal diperbarui!');
            }
        }

    public function updateoperator(Request $request, $id){
        // Ambil data yang ingin diperbarui berdasarkan ID
        $data = Data::find($id);

        // Pastikan data ada
        if (!$data) {
            return redirect()->route('dashboard_operator.data_pangan')->with('error', 'Data tidak ditemukan!');
        }

        // Cek apakah data sudah divalidasi (tidak boleh diedit)
        if ($data->status_verifikasi === 'Valid') {
            return redirect()->route('dashboard_operator.data_pangan')->with('error', 'Data yang sudah divalidasi tidak dapat diedit!');
        }

        // Validasi input
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'nama_komoditas' => 'required|string|max:255',
            'harga_het' => 'required|numeric',
            'harga_kemarin' => 'required|numeric',
            'harga_hari_ini' => 'required|numeric',
        ]);

        // Update data yang ada
        $data->tanggal = $validatedData['tanggal'];
        $data->nama_komoditas = $validatedData['nama_komoditas'];
        $data->harga_het = $validatedData['harga_het'];
        $data->harga_kemarin = $validatedData['harga_kemarin'];
        $data->harga_hari_ini = $validatedData['harga_hari_ini'];
        $data->status_verifikasi = 'Belum_Diverifikasi';

        // Simpan perubahan
        if ($data->save()) {
            return redirect()->route('dashboard_operator.data_pangan')->with('success', 'Data berhasil diperbarui!');
        } else {
            return redirect()->route('dashboard_operator.data_pangan')->with('error', 'Data gagal diperbarui!');
        }
    }

    public function destroy($id){
        $data = Data::find($id);
        $data->delete();
        return redirect()->route('dashboard_admin.sub_menu.data_pangan')->with('delete-success', 'Data berhasil dihapus!');
    }    

    public function destroyoperator($id){
        $data = Data::find($id);
        $data->delete();
        return redirect()->route('dashboard_operator.data_pangan')->with('delete-success', 'Data berhasil dihapus!');
    } 

    public function exportpdf(){
        $data = Data::all();
        $pdf = PDF::loadview('datakomunitas-pdf', compact('data'));
        return $pdf->download('data.pdf');
    }
    
    public function showForm() {
        // Ambil semua nama komoditas beserta harga HET dari kolom hethap di tabel sumber_data
        $komoditasList = sumber_data::select('nama_komoditas', 'satuan', 'hethap')->get();

        // Ambil data REKAP terakhir
        $latestRekap = rekap::orderBy('tanggal', 'desc')->first();
        $rekapData = [];
        if ($latestRekap) {
            foreach ($latestRekap->toArray() as $key => $value) {
                if ($key !== 'tanggal') {
                    $rekapData[$key] = $value;
                }
            }
        }

        // Ambil semua tanggal yang sudah ada di rekap dan het
        $existingRekapDates = rekap::pluck('tanggal')->toArray();
        $existingHetDates = het::pluck('tanggal')->toArray();

        return view('dashboard_admin.sub_menu.input_data', [
            'komoditasList' => $komoditasList,
            'rekapData' => $rekapData,
            'existingRekapDates' => $existingRekapDates,
            'existingHetDates' => $existingHetDates
        ]);
    }
    
    public function showFormoperator(){
        // Ambil semua nama komoditas beserta harga HET dari kolom hethap di tabel sumber_data
        $komoditasList = sumber_data::select('nama_komoditas', 'satuan', 'hethap')->get();

        // Ambil data REKAP terakhir
        $latestRekap = rekap::orderBy('tanggal', 'desc')->first();
        $rekapData = [];
        if ($latestRekap) {
            foreach ($latestRekap->toArray() as $key => $value) {
                if ($key !== 'tanggal') {
                    $rekapData[$key] = $value;
                }
            }
        }

        // Ambil semua tanggal yang sudah ada di rekap dan het
        $existingRekapDates = rekap::pluck('tanggal')->toArray();
        $existingHetDates = het::pluck('tanggal')->toArray();

        return view('dashboard_operator.input_data', [
            'komoditasList' => $komoditasList,
            'rekapData' => $rekapData,
            'existingRekapDates' => $existingRekapDates,
            'existingHetDates' => $existingHetDates
        ]);
    }     
    
    public function bulkDelete(Request $request) {
        $ids = $request->input('ids');

        if (!$ids || count($ids) == 0) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        // ambil tanggal dari data yang dipilih
        $tanggal = Data::whereIn('id', $ids)
                        ->pluck('tanggal')
                        ->unique();

        // hapus semua data yang memiliki tanggal tersebut
        Data::whereIn('tanggal', $tanggal)->delete();
        Rekap::whereIn('tanggal', $tanggal)->delete();
        HET::whereIn('tanggal', $tanggal)->delete();


        return redirect()->back()->with('success', 'Data yang dipilih berhasil dihapus.');
    }

    // public function turunkanStatus($id)
    // {
    //     $data = Data::findOrFail($id);

    //     $data->status_verifikasi = 'Belum_Diverifikasi';
    //     $data->save();

    //     return redirect()->back()->with('success', 'Status berhasil diturunkan untuk diperbaiki');
    // }

    public function turunkanStatus($id)
    {
        $data = Data::findOrFail($id);

        // ubah status jadi belum diverifikasi
        $data->status_verifikasi = 'Revisi';
        $data->save();

        $komoditas = $data->nama_komoditas;

        // update kolom rekap jadi null berdasarkan tanggal
        Rekap::where('tanggal', $data->tanggal)
            ->update([
                $komoditas => null
            ]);

        return redirect()->back()->with('success', 'Status berhasil diturunkan dan rekap diperbarui.');
    }

}
