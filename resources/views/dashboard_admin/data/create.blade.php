<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">`n    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Komoditas</title>
    <link rel="stylesheet" href={{asset('css/data/create.css') }}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">

        <a href="{{ route('SumberData.index') }}" class="btn-back"><i class="fas fa-chevron-left"></i> Kembali</a>

        <div class="header-text">
            <p>Tambah Komoditas</p>
        </div>

        <div class="main-content">
            <img src="{{ asset('images/tambah_komoditas.png') }}" alt="Gambar Komoditas" class="form-image"> <!-- Gambar di luar -->
            <div class="form-container">
                <div class="add-commodity-form">
                <form action="{{ route('SumberData.store') }}" method="POST">
                    @csrf

                    <div class="input-group">
                        <label for="komoditas">Komoditas:</label>
                        <input type="text" id="komoditas" name="komoditas" required>
                    </div>

                    <div class="input-group">
                        <label for="satuan">Satuan:</label>
                        <input type="text" id="satuan" name="satuan" required>
                    </div>

                    <div class="input-group">
                        <label for="hethap">HET/HAP:</label>
                        <input type="text" id="hethap" name="hethap" required>
                    </div>

                    <div class="input-group">
                        <label for="intervensi">Intervensi (%):</label>
                        <input type="text" id="intervensi" name="intervensi" required>
                    </div>

                    <div class="input-group">
                        <label for="waspada">Waspada (%):</label>
                        <input type="text" id="waspada" name="waspada" required>
                    </div>

                    <div class="input-group">
                        <label for="harga_intervensi">Harga Intervensi:</label>
                        <input type="text" id="harga_intervensi" name="harga_intervensi" readonly>
                    </div>

                    <div class="input-group">
                        <label for="harga_waspada">Harga Waspada:</label>
                        <input type="text" id="harga_waspada" name="harga_waspada" readonly>
                    </div>

                    <div class="input-group">
                        <label for="cv">CV:</label>
                        <input type="text" id="cv" name="cv" required>
                    </div>

                    <div class="input-group">
                        <label for="sumber_hethap">Sumber HET/HAP:</label>
                        <input type="text" id="sumber_hethap" name="sumber_hethap" required>
                    </div>

                    <div class="input-group">
                        <label for="sumber_indikator">Sumber Indikator:</label>
                        <input type="text" id="sumber_indikator" name="sumber_indikator" required>
                    </div>

                    <div class="input-group">
                        <label for="batasan_cv">Batasan CV:</label>
                        <input type="text" id="batasan_cv" name="batasan_cv" required>
                    </div>

                    <button type="submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </form>
                </div>
            </div>
        </div>               
    </div>
<script>
    function hitungHarga() {
        let het = parseFloat(document.getElementById('hethap').value.replace(',', '.')) || 0;
        let intervensi = parseFloat(document.getElementById('intervensi').value.replace(',', '.')) || 0;
        let waspada = parseFloat(document.getElementById('waspada').value.replace(',', '.')) || 0;

        let hargaIntervensi = het * (1 + intervensi / 100);
        let hargaWaspada = het * (1 + waspada / 100);

        document.getElementById('harga_intervensi').value = Math.round(hargaIntervensi);
        document.getElementById('harga_waspada').value = Math.round(hargaWaspada);
    }

    document.getElementById('hethap').addEventListener('input', hitungHarga);
    document.getElementById('intervensi').addEventListener('input', hitungHarga);
    document.getElementById('waspada').addEventListener('input', hitungHarga);

    hitungHarga(); // Panggil saat pertama kali halaman dimuat
</script>
</body>
</html>
