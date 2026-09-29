<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">`n    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Komoditas</title>
    <link rel="stylesheet" href="{{ asset('css/data/edit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <div class="container">
        <a href="{{ route('SumberData.index') }}" class="btn-back">
            <i class="fas fa-chevron-left"></i> Kembali
        </a>
    
        <h2 class="header-text">EDIT KOMODITAS</h2>
    
        <div class="main-content">
            <!-- Gambar di kiri -->
            <img src="{{ asset('images/edit_data.png') }}" alt="Gambar" class="form-image">
    
            <!-- Form di kanan -->
            <div class="form-container">
                <form action="{{ route('SumberData.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
            
                    <div class="form-group">
                        <label>Komoditas:</label>
                        <input type="text" name="nama_komoditas" value="{{ $data->nama_komoditas }}" required>
            
                        <label>Satuan:</label>
                        <input type="text" name="satuan" value="{{ $data->satuan }}" required>
                    </div>
                    <div class="form-group">
                        <label>HET/HAP:</label>
                        <input type="text" id="hethap" name="het_hap" value="{{ $data->hethap }}" required>

                        <label>Intervensi (%):</label>
                        <input type="text" id="intervensi" name="intervensi" value="{{ $data->intervensi }}" required>
                    </div>

                    <div class="form-group">
                        <label>Waspada (%):</label>
                        <input type="text" id="waspada" name="waspada" value="{{ $data->waspada }}" required>

                        <label>Harga Intervensi:</label>
                        <input type="text" id="harga_intervensi" name="harga_intervensi" value="{{ $data->harga_intervensi }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Waspada:</label>
                        <input type="text" id="harga_waspada" name="harga_waspada" value="{{ $data->harga_waspada }}" readonly>

                        <label>CV:</label>
                        <input type="text" name="cv" value="{{ $data->cv }}" required>
                    </div>      
                    <div class="form-group">
                        <label>Batasan CV:</label>
                        <input type="text" name="batasan_cv" value="{{ $data->batasan_cv }}" required>
            
                        <label>Sumber HET/HAP:</label>
                        <input type="text" name="sumber_hethap" value="{{ $data->sumber_hethap }}" required>
                    </div>
            
                    <div class="form-group">
                        <label>Sumber Indikator:</label>
                        <input type="text" name="sumber_indikator" value="{{ $data->sumber_indikator }}" required>
                    </div>
            
                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>            
        </div>
    </div>   
<script>
    function hitungHarga() {
        let het = parseFloat(document.getElementById('hethap').value.replace(',', '.')) || 0;
        let intervensi = parseFloat(document.getElementById('intervensi').value) || 0;
        let waspada = parseFloat(document.getElementById('waspada').value) || 0;

        let hargaIntervensi = het * (1 + intervensi / 100);
        let hargaWaspada = het * (1 + waspada / 100);

        document.getElementById('harga_intervensi').value = Math.round(hargaIntervensi);
        document.getElementById('harga_waspada').value = Math.round(hargaWaspada);
    }

    // Event listener untuk semua input yang berpengaruh
    document.getElementById('hethap').addEventListener('input', hitungHarga);
    document.getElementById('intervensi').addEventListener('input', hitungHarga);
    document.getElementById('waspada').addEventListener('input', hitungHarga);

    // Jalankan sekali saat pertama load
    hitungHarga();
</script>
</body>
</html>

