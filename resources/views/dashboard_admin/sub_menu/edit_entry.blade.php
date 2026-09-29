<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Data</title>
    <link rel="stylesheet" href="{{asset('css/edit_entry.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   </head>
<body>
    <div class="container">

        <div class="header-text"><p>Edit Data</p></div>

        <a href="{{ route('dashboard_admin.sub_menu.data_pangan') }}" class="btn-back"><i class="fas fa-chevron-left"></i> Kembali</a>

        <div class="main-content">
            <img src="{{ asset('images/input_pangan.png') }}" alt="Gambar Komoditas" class="form-image"> <!-- Gambar di luar -->

            <form action="{{ route('dashboard_admin.sub_menu.data.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $data->tanggal }}" readonly>
                </div>
                <div class="form-group">
                <label for="nama_komoditas">Nama Komoditas:</label>
                <input type="text" id="nama_komoditas" name="nama_komoditas" value="{{ $data->nama_komoditas }}" readonly>
                </div>
                <div class="form-group">
                <label for="harga_het">Harga HET:</label>
                <input type="number" id="harga_het" name="harga_het" value="{{ $data->harga_het }}" step="0.01" readonly>
                </div>
                <div class="form-group">
                <label for="harga_kemarin">Harga Kemarin:</label>
                <input type="number" id="harga_kemarin" name="harga_kemarin" value="{{ $data->harga_kemarin }}" step="0.01" readonly>
                </div>
                <div class="form-group">
                <label for="harga_hari_ini">Harga Hari Ini:</label>
                <input type="number" id="harga_hari_ini" name="harga_hari_ini" value="{{ $data->harga_hari_ini }}" step="0.01" required>
                </div>
                <div class="button-container">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Update</button>
                </div>            
                </form>
        </div>
    </div>
</body>
</html>

