<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">`n    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Pengguna</title>
    <link rel="stylesheet" href="{{ asset('css/user/create.css') }}">
    <script src="{{ asset('js/checkbox.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">

        <a href="{{ route('pengguna.index') }}" class="btn-back"><i class="fas fa-chevron-left"></i> Kembali</a>
        
        <div class="header-text">
            <p>Tambah Pengguna</p>
        </div>

        <div class="main-content">
            <img src="{{ asset('images/add_user.png') }}" alt="Gambar User" class="form-image"> <!-- Gambar di luar -->
            <div class="content">
                <div class="add-user-form">
                    <form action="{{ route('pengguna.store') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <label for="userID">ID:</label>
                            <input type="text" id="userID" name="id" required>
                        </div>
                        <div class="input-group">
                            <label for="userName">Nama:</label>
                            <input type="text" id="userName" name="nama" required>
                        </div>
                        <div class="input-group">
                            <label for="userEmail">Email:</label>
                            <input type="email" id="userEmail" name="email" required>
                        </div>
                        <div class="input-group">
                            <label for="userRole">Role:</label>
                            <select id="userRole" name="role" required>
                                <option value="" disabled selected>Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="operator">Operator</option>
                                <option value="verifikator">Verifikator</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="userPassword">Password:</label>
                            <input type="password" id="userPassword" name="password" required>
                        </div>
                        
                        <div class="checkbox-container">
                            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                            <label for="showPassword">Tampilkan Password</label>
                        </div>
                        <div class="button-container">
                            <button type="submit">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
    // Ambil elemen modal
    var modal = document.getElementById("profileModal");

    // Ambil elemen yang membuka modal
    var btn = document.getElementById("profile-link");

    // Ambil elemen <span> yang menutup modal
    var span = document.getElementsByClassName("close")[0];

    // Ketika pengguna mengklik link profil, buka modal
    btn.onclick = function() {
    modal.style.display = "block"; // Tampilkan modal
    setTimeout(function() { 
        modal.classList.add("show");
        modal.classList.remove("hide"); // Pastikan untuk menghapus kelas 'hide' jika ada
    }, 10); // Waktu untuk memastikan display:block diterapkan
    }

    // Ketika pengguna mengklik <span> (x), tutup modal
    span.onclick = function() {
    modal.classList.remove("show"); // Hapus kelas 'show' untuk memulai animasi keluar
    modal.classList.add("hide"); // Tambahkan kelas 'hide' untuk memulai animasi penutupan
    setTimeout(function() { 
        modal.style.display = "none"; // Sembunyikan modal setelah animasi selesai
    }, 300); // Durasi yang sesuai dengan animasi CSS (0.3s)
    }

    // Ketika pengguna mengklik di luar modal, tutup modal
    window.onclick = function(event) {
    if (event.target == modal) {
        modal.classList.remove("show"); // Hapus kelas 'show' untuk memulai animasi keluar
        modal.classList.add("hide"); // Tambahkan kelas 'hide' untuk memulai animasi penutupan
        setTimeout(function() { 
            modal.style.display = "none"; // Sembunyikan modal setelah animasi selesai
        }, 300); // Durasi yang sesuai dengan animasi CSS (0.3s)
    }
    }
    </script>
</body>
</html>


    </script>
</body>
</html>

