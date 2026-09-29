<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">`n    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="{{ asset('css/user/create.css') }}">
    <script src="{{ asset('js/checkbox.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">

        <a href="{{ route('pengguna.index') }}" class="btn-back"><i class="fas fa-chevron-left"></i> Kembali</a>

        <div class="header-text">
            <p>Edit Pengguna</p>
        </div>

        <div class="main-content">
            <img src="{{ asset('images/edit_user.png') }}" alt="Gambar User" class="form-image"> <!-- Gambar di luar -->
            <div class="content">
                <div class="edit-user-form">
                    <form action="{{ route('pengguna.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="input-group">
                            <label for="userID">ID:</label>
                            <input type="text" id="userID" name="id" value="{{ $data->id }}" required>
                        </div>
                        <div class="input-group">
                            <label for="userName">Nama:</label>
                            <input type="text" id="userName" name="nama" value="{{ $data->nama }}" required>
                        </div>
                        <div class="input-group">
                            <label for="userEmail">Email:</label>
                            <input type="email" id="userEmail" name="email" value="{{ $data->email }}" required>
                        </div>
                        <div class="input-group">
                            <label for="userRole">Role:</label>
                            <input type="role" id="userRole" name="role" value="{{ $data->role }}" required>
                        </div>
                        <div class="input-group">
                            <label for="userPassword">Password:</label>
                            <input type="password" id="userPassword" name="password" value="{{ $data->password }}" required>
                            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> Tampilkan Password
                        </div>
                        <div class="button-container">
                            <button type="submit">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal -->
        <div id="profileModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Profil User</h2>
                <div class="profile-details">
                    <div class="profile-info">
                        <p>Nama: {{ Auth::user()->nama }}</p>
                        <p>Role: {{ Auth::user()->role }}</p>
                        <p>Email: {{ Auth::user()->email }}</p>
                        <a href="/logout" class="btn-logout">Logout</a>
                    </div>
                    <div class="profile-logo">
                        <img src="{{ asset('images/profileLogo.png') }}" alt="Profile Logo" />
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



</body>
</html>

