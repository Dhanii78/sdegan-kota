<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">`n    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>data</title>
    <link rel="stylesheet" href={{ asset('css/data/index.css') }}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <p class="logo-text">S-DEGAN</p>
                <p class="logo-text">Sistem Deteksi Dini Gejolak Harga Pangan</p>
            </div>
            <button class="toggle-btn">☰</button>
            <div class="menu">
                <a href="{{ route('dashboard_admin.index') }}"><i class="fas fa-home"></i> Dashboard</a>
                <a href="{{ route('SumberData.index') }}"><i class="fas fa-book"></i> Regulasi</a>
                <a href="{{ route('pengguna.index') }}"><i class="fas fa-user"></i> User</a>
                <a href="{{ route('dashboard_admin.sub_menu.data_pangan') }}"><i class="fas fa-keyboard"></i> Data Pangan</a>
                <a href="{{ route('rekap.admin') }}"><i class="fas fa-chart-line"></i> Rekap Data</a>
                <a href="javascript:void(0)" id="profile-link"><i class="fas fa-user-circle"></i> Profil</a>
            </div>
            <div class="datetime">
                <span id="current-time"></span>
                <hr>
                <span id="current-date"></span>
            </div>
        </div> 
        <div class="header-text">
            <p>Regulasi data</p>
        </div>
        <div class="main-content">
            <div class="filter-container">
                <label for="filterCommodity">Filter Komoditas: </label>
                <select id="filterCommodity" onchange="filterTable()">
                    <option value="">Semua Komoditas</option>
                    @foreach ($sumber_data->unique('nama_komoditas') as $data)
                        <option value="{{ $data->nama_komoditas }}">{{ $data->nama_komoditas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="content">
                <!-- Tombol "Tambah Komoditas" -->
                <a href="{{ route('SumberData.create') }}" class="btn-add"><i class="fas fa-plus"></i> Tambah Komoditas</a>
                <!-- Tabel untuk menampilkan data pengguna -->
                <table id="userTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Komoditas</th>
                            <th>Satuan</th>
                            <th>HET/HAP</th>
                            <th>Intervensi (%)</th>
                            <th>Waspada (%)</th>
                            <th>Harga Intervensi</th>
                            <th>Harga Waspada</th>
                            <th>CV</th>
                            <th>Sumber HET/HAP</th>
                            <th>Sumber Indikator</th>
                            <th>Batasan CV</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sumber_data as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->nama_komoditas }}</td>
                                <td>{{ $data->satuan }}</td>
                                <td>{{ number_format($data->hethap, 0, ',', '.') }}</td>
                                <td>{{ $data->intervensi }}%</td>
                                <td>{{ $data->waspada }}%</td>
                                <td>{{ number_format($data->hethap * (1 + $data->intervensi / 100), 0, ',', '.') }}</td>
                                <td>{{ number_format($data->hethap * (1 + $data->waspada / 100), 0, ',', '.') }}</td>
                                <td>{{ $data->cv }}%</td>
                                <td>{{ $data->sumber_hethap }}</td>
                                <td>{{ $data->sumber_indikator }}</td>
                                <td>{{ $data->batasan_cv }}</td>
                                <!-- Kolom Aksi -->
                                <td>
                                    <a href="{{ route('SumberData.edit', $data->id) }}" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('SumberData.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Profil User</h2>
            <div class="profile-details">
                <div class="profile-info">
                    <p>Nama: {{ Auth::user()->nama }}</p>
                    <p>Role: {{ Auth::user()->role }}</p>
                    <p>Email: {{ Auth::user()->email }}</p>
                    <a href="#" class="btn-logout" onclick="handleLogout(event)">Logout</a>
                </div>
                <div class="profile-logo">
                    <img src="{{ asset('images/profileLogo.png') }}" alt="Profile Logo" />
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ambil elemen tombol toggle dan navbar
        const toggleBtn = document.querySelector('.toggle-btn');
        const navbar = document.querySelector('.navbar');
        const container = document.querySelector('.container');
        const layoutContainer = document.querySelector(".layout-container");

        // Fungsi untuk menyembunyikan atau menampilkan sidebar
        toggleBtn.addEventListener('click', () => {
            navbar.classList.toggle('hidden');  // Toggle class untuk sidebar
            container.classList.toggle('sidebar-hidden'); // Menyesuaikan konten ketika sidebar disembunyikan
            toggleBtn.classList.toggle('hide'); // Menambahkan class untuk memutar panah
            container.classList.toggle("shifted"); // Menggeser container
            layoutContainer.classList.toggle("shifted");
        });
    </script>

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
    
    document.addEventListener('DOMContentLoaded', (event) => {
        // Seleksi elemen alert
        const alertSuccess = document.getElementById('alert-success');
        const alertError = document.getElementById('alert-error');
        
        // Fungsi untuk menghilangkan elemen
        function hideAlert(alertElement) {
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease-out";
                alertElement.style.opacity = "0";
                setTimeout(() => alertElement.remove(), 500); // Hapus elemen setelah transisi
            }
        }
        
        // Set waktu untuk menghilangkan alert
        setTimeout(() => hideAlert(alertSuccess), 2000); //2 detik
        setTimeout(() => hideAlert(alertError), 2000);
    });
    </script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
            document.getElementById('current-date').innerText = now.toLocaleDateString('id-ID', optionsDate);
            document.getElementById('current-time').innerText = now.toLocaleTimeString('id-ID', optionsTime);
        }
        
        // Update setiap detik
        setInterval(updateDateTime, 1000);
        updateDateTime(); // Panggil sekali saat halaman dimuat
    </script>
    <script>
        function filterTable() {
            const select = document.getElementById('filterCommodity');
            const filterValue = select.value.toLowerCase();
            const table = document.getElementById('userTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const commodityCell = rows[i].getElementsByTagName('td')[1]; // kolom komoditas
                if (commodityCell) {
                    const textValue = commodityCell.textContent || commodityCell.innerText;
                    rows[i].style.display = filterValue === "" || textValue.toLowerCase().includes(filterValue) ? "" : "none";
                }
            }
        }
    </script>
    <!-- FORM LOGOUT (WAJIB ADA) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <script>
        function handleLogout(event) {
            // Hentikan default link supaya tidak GET
            if (event) {
                event.preventDefault();
            }

            // Hapus filter dashboard dari localStorage
            localStorage.removeItem('dashboardFilters');

            // Ambil form logout
            const form = document.getElementById('logout-form');

            if (form) {
                form.submit(); // Kirim POST ke Laravel
            } else {
                console.error('Form logout tidak ditemukan!');
            }
        }
    </script>
</body>
</html>



                modal.style.display = "none";
            }, 300);
        }
    }

    // Ketika pengguna mengklik di luar modal, tutup modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.remove("show");
            modal.classList.add("hide");
            setTimeout(function() { 
                modal.style.display = "none";
            }, 300);
        }
    }
    
    document.addEventListener('DOMContentLoaded', (event) => {
        // Seleksi elemen alert
        const alertSuccess = document.getElementById('alert-success');
        const alertError = document.getElementById('alert-error');
        
        // Fungsi untuk menghilangkan elemen
        function hideAlert(alertElement) {
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease-out";
                alertElement.style.opacity = "0";
                setTimeout(() => alertElement.remove(), 500);
            }
        }
        
        // Set waktu untuk menghilangkan alert
        setTimeout(() => hideAlert(alertSuccess), 2000);
        setTimeout(() => hideAlert(alertError), 2000);
    });
    </script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
            const dateEl = document.getElementById('current-date');
            const timeEl = document.getElementById('current-time');
            if (dateEl) dateEl.innerText = now.toLocaleDateString('id-ID', optionsDate);
            if (timeEl) timeEl.innerText = now.toLocaleTimeString('id-ID', optionsTime);
        }
        
        // Update setiap detik
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
    <script>
        function filterTable() {
            const select = document.getElementById('filterCommodity');
            const filterValue = select.value.toLowerCase();
            const table = document.getElementById('userTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const commodityCell = rows[i].getElementsByTagName('td')[1]; // kolom komoditas
                if (commodityCell) {
                    const textValue = commodityCell.textContent || commodityCell.innerText;
                    rows[i].style.display = filterValue === "" || textValue.toLowerCase().includes(filterValue) ? "" : "none";
                }
            }
        }

        // Konfirmasi hapus di mobile
        function confirmDeleteReg(btn, nama) {
            Swal.fire({
                title: 'Hapus Regulasi?',
                text: `Anda akan menghapus data komoditas "${nama}". Tindakan ini tidak bisa dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = btn.closest('.admin-regulasi-card__actions').querySelector('form');
                    if (form) form.submit();
                }
            });
        }
    </script>
    <!-- FORM LOGOUT (WAJIB ADA) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <script>
        function handleLogout(event) {
            if (event) { event.preventDefault(); }
            localStorage.removeItem('dashboardFilters');
            const form = document.getElementById('logout-form');
            if (form) { form.submit(); }
        }
    </script>
</body>
</html>
