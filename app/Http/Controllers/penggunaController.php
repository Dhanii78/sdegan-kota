<?php

namespace App\Http\Controllers;

use App\Models\pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class penggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = pengguna::all();
        return view('dashboard_admin.user.index', ['pengguna' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('dashboard_admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // Validasi data yang diterima
        $request->validate([
            'id' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);
    
        // Simpan data ke dalam database
        pengguna::create([
            'id' => $request->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password) // Hash password
        ]);
    
        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {
        // Ambil data berdasarkan ID
        $data = pengguna::findOrFail($id);

        // Tampilkan view untuk edit dengan data yang dipilih
        return view('dashboard_admin.user.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        // Validasi data yang diterima
        $request->validate([
            'id' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Cari data berdasarkan ID
        $data = pengguna::findOrFail($id);

        // Update data
        $data->update([
            'id' => $request->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password) // Hash password
        ]);

        // Redirect ke halaman indeks dengan pesan sukses
        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        // Cari data berdasarkan ID
        $data = pengguna::findOrFail($id);

        // Hapus data
        $data->delete();

        // Redirect ke halaman indeks dengan pesan sukses
        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil dihapus!');
    }
}
