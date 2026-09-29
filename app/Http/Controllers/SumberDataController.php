<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sumber_data;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SumberDataController extends Controller
{
    public function index() {
        $data = sumber_data::all();
        return view('dashboard_admin.data.index', ['sumber_data' => $data]);
    }

    public function create() {
        return view('dashboard_admin.data.create');
    }

    public function store(Request $request) {
        $request->validate([
            'komoditas' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'hethap' => 'required|string|max:255',
            'intervensi' => 'required|string|max:255',
            'waspada' => 'required|string|max:255',
            'harga_intervensi' => 'required|string|max:255',
            'harga_waspada' => 'required|string|max:255',
            'cv' => 'required|string|max:255',
            'sumber_hethap' => 'required|string|max:255',
            'sumber_indikator' => 'required|string|max:255',
            'batasan_cv' => 'required|string|max:255',
        ]);

        $sumber = sumber_data::create([
            'nama_komoditas' => $request->komoditas,
            'satuan' => $request->satuan,
            'hethap' => $request->hethap,
            'intervensi' => $request->intervensi,
            'waspada' => $request->waspada,
            'harga_intervensi' => $request->harga_intervensi,
            'harga_waspada' => $request->harga_waspada,
            'cv' => $request->cv,
            'sumber_hethap' => $request->sumber_hethap,
            'sumber_indikator' => $request->sumber_indikator,
            'batasan_cv' => $request->batasan_cv
        ]);

        $nama_kolom = str_replace(' ', '_', $request->komoditas);

        if (!Schema::hasColumn('rekap', $nama_kolom)) {
            Schema::table('rekap', function (Blueprint $table) use ($nama_kolom) {
                $table->integer($nama_kolom)->nullable()->after('TANGGAL');
            });
        }

        if (!Schema::hasColumn('het', $nama_kolom)) {
            Schema::table('het', function (Blueprint $table) use ($nama_kolom) {
                $table->integer($nama_kolom)->nullable()->after('TANGGAL');
            });
        }

        return redirect()->route('SumberData.index')->with('success', 'Data komoditas berhasil ditambahkan dan kolom rekap/het diperbarui!');
    }

    public function edit($id) {
        $data = sumber_data::findOrFail($id);
        return view('dashboard_admin.data.edit', compact('data'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'nama_komoditas'     => 'required|string|max:255',
            'satuan'             => 'required|string|max:255',
            'het_hap'            => 'required|string|max:255',
            'intervensi'         => 'required|string|max:255',
            'waspada'            => 'required|string|max:255',
            'harga_intervensi'   => 'required|string|max:255',
            'harga_waspada'      => 'required|string|max:255',
            'cv'                 => 'required|string|max:255',
            'batasan_cv'         => 'required|string|max:255',
            'sumber_hethap'      => 'required|string|max:255',
            'sumber_indikator'   => 'required|string|max:255',
        ]);
    
        $data = sumber_data::findOrFail($id);
    
        $old_column = str_replace(' ', '_', $data->nama_komoditas);
        $new_column = str_replace(' ', '_', $request->nama_komoditas);
    
        // Rename kolom jika nama komoditas berubah
        if ($old_column !== $new_column) {
            if (Schema::hasColumn('rekap', $old_column)) {
                Schema::table('rekap', function (Blueprint $table) use ($old_column, $new_column) {
                    $table->renameColumn($old_column, $new_column);
                });
            }
    
            if (Schema::hasColumn('het', $old_column)) {
                Schema::table('het', function (Blueprint $table) use ($old_column, $new_column) {
                    $table->renameColumn($old_column, $new_column);
                });
            }
        }
    
        // Update isi tabel sumber_data
        $data->update([
            'nama_komoditas'     => $request->nama_komoditas,
            'satuan'             => $request->satuan,
            'hethap'             => $request->het_hap,
            'intervensi'         => $request->intervensi,
            'waspada'            => $request->waspada,
            'harga_intervensi'   => $request->harga_intervensi,
            'harga_waspada'      => $request->harga_waspada,
            'cv'                 => $request->cv,
            'batasan_cv'         => $request->batasan_cv,
            'sumber_hethap'      => $request->sumber_hethap,
            'sumber_indikator'   => $request->sumber_indikator,
        ]);
    
        return redirect()->route('SumberData.index')->with('success', 'Data komoditas berhasil diperbarui!');
    }    

    public function destroy(string $id){
            $data = sumber_data::findOrFail($id);
            $nama_kolom = $data->nama_komoditas;

            try {
                if (Schema::hasColumn('rekap', $nama_kolom)) {
                    DB::statement("ALTER TABLE `rekap` DROP COLUMN `$nama_kolom`");
                }
            } catch (\Throwable $e) {
                Log::error("Gagal menghapus kolom $nama_kolom dari rekap: " . $e->getMessage());
                return back()->with('error', "Gagal menghapus kolom $nama_kolom dari rekap: " . $e->getMessage());
            }

            try {
                if (Schema::hasColumn('het', $nama_kolom)) {
                    DB::statement("ALTER TABLE `het` DROP COLUMN `$nama_kolom`");
                }
            } catch (\Throwable $e) {
                Log::error("Gagal menghapus kolom $nama_kolom dari het: " . $e->getMessage());
                return back()->with('error', "Gagal menghapus kolom $nama_kolom dari het: " . $e->getMessage());
            }

            $data->delete();

            return redirect()->route('SumberData.index')
                ->with('success', "Data '$nama_kolom' dan kolom terkait berhasil dihapus!");
    }
}
