<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekap Data</title>
    <link rel="stylesheet" href="{{ asset('css/rekap_data.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/mobile-nav.js') }}"></script>
    <style>
        @media (max-width: 767px) {
            .container > .navbar { display: none !important; }
        }
    </style></head>
<body>`n        <div class="container">
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
            <p>Rekap Data Pangan</p>
        </div>

        <div class="main-content">
            <a href="{{ route('export.rekap') }}" class="btn btn-excel">
                <i class="fas fa-file-excel"></i> Export Harga
            </a>
            <a href="{{ route('export.het') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export HET
            </a>
            <div class="rekap-container">
                <div class="table-container">
                    <h2>Data Harga</h2>
                    <div class="table-wrapper desktop-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach ($rekap_columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekap as $data)
                                    <tr>
                                        @foreach ($rekap_columns as $column)
                                            <td>
                                                @if ($column === 'tanggal')
                                                    {{ $data->$column }}
                                                @else
                                                    {{ number_format($data->$column, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn-delete" data-tanggal="{{ $data->tanggal }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-wrapper mobile-table" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Komoditas</th>
                                    @foreach ($rekap as $data)
                                        <th>{{ $data->tanggal }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekap_columns as $column)
                                    @if (!in_array($column, ['tanggal', 'created_at', 'updated_at', 'id']))
                                        <tr>
                                            <td style="text-align: left;"><strong>{{ ucwords(str_replace('_', ' ', $column)) }}</strong></td>
                                            @foreach ($rekap as $data)
                                                <td>{{ $data->$column ? number_format($data->$column, 0, ',', '.') : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td style="text-align: left;"><strong>Aksi</strong></td>
                                    @foreach ($rekap as $data)
                                        <td>
                                            <button type="button" class="btn-delete" data-tanggal="{{ $data->tanggal }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-container">
                    <h2>Data HET</h2>
                    <div class="table-wrapper desktop-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach ($het_columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($het as $data)
                                    <tr>
                                        @foreach ($het_columns as $column)
                                            <td>
                                                @if ($column === 'tanggal')
                                                    {{ $data->$column }}
                                                @else
                                                    {{ number_format($data->$column, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-wrapper mobile-table" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Komoditas</th>
                                    @foreach ($het as $data)
                                        <th>{{ $data->tanggal }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($het_columns as $column)
                                    @if (!in_array($column, ['tanggal', 'created_at', 'updated_at', 'id']))
                                        <tr>
                                            <td style="text-align: left;"><strong>{{ ucwords(str_replace('_', ' ', $column)) }}</strong></td>
                                            @foreach ($het as $data)
                                                <td>{{ $data->$column ? number_format($data->$column, 0, ',', '.') : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <style>
                        @media (max-width: 768px) {
                            .desktop-table { display: none !important; }
                            .mobile-table { display: block !important; }
                        }
                    </style>
                </div>
            </div>            
        </div>
    </div>

    <form id="deleteForm" method="POST" action="{{ route('rekap.deleteTanggal') }}">
    @csrf
        <input type="hidden" name="tanggal" id="deleteTanggal">
    </form>

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
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {

                const tanggal = this.dataset.tanggal;

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Hapus semua data tanggal ${tanggal}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        document.getElementById('deleteTanggal').value = tanggal;
                        document.getElementById('deleteForm').submit();

                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Dibatalkan',
                            text: 'Data tidak dihapus.',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                });

            });
        });
    </script>
</body>
</html>





                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                });

            });
        });
    </script>
</body>
</html>

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
            <p>Rekap Data Pangan</p>
        </div>

        <div class="main-content">
            <a href="{{ route('export.rekap') }}" class="btn btn-excel">
                <i class="fas fa-file-excel"></i> Export Harga
            </a>
            <a href="{{ route('export.het') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export HET
            </a>
            <div class="rekap-container">
                <div class="table-container">
                    <h2>Data Harga</h2>
                    <div class="table-wrapper desktop-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach ($rekap_columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekap as $data)
                                    <tr>
                                        @foreach ($rekap_columns as $column)
                                            <td>
                                                @if ($column === 'tanggal')
                                                    {{ $data->$column }}
                                                @else
                                                    {{ number_format($data->$column, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn-delete" data-tanggal="{{ $data->tanggal }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-wrapper mobile-table" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Komoditas</th>
                                    @foreach ($rekap as $data)
                                        <th>{{ $data->tanggal }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekap_columns as $column)
                                    @if (!in_array($column, ['tanggal', 'created_at', 'updated_at', 'id']))
                                        <tr>
                                            <td style="text-align: left;"><strong>{{ ucwords(str_replace('_', ' ', $column)) }}</strong></td>
                                            @foreach ($rekap as $data)
                                                <td>{{ $data->$column ? number_format($data->$column, 0, ',', '.') : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td style="text-align: left;"><strong>Aksi</strong></td>
                                    @foreach ($rekap as $data)
                                        <td>
                                            <button type="button" class="btn-delete" data-tanggal="{{ $data->tanggal }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-container">
                    <h2>Data HET</h2>
                    <div class="table-wrapper desktop-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach ($het_columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($het as $data)
                                    <tr>
                                        @foreach ($het_columns as $column)
                                            <td>
                                                @if ($column === 'tanggal')
                                                    {{ $data->$column }}
                                                @else
                                                    {{ number_format($data->$column, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-wrapper mobile-table" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Komoditas</th>
                                    @foreach ($het as $data)
                                        <th>{{ $data->tanggal }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($het_columns as $column)
                                    @if (!in_array($column, ['tanggal', 'created_at', 'updated_at', 'id']))
                                        <tr>
                                            <td style="text-align: left;"><strong>{{ ucwords(str_replace('_', ' ', $column)) }}</strong></td>
                                            @foreach ($het as $data)
                                                <td>{{ $data->$column ? number_format($data->$column, 0, ',', '.') : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <style>
                        @media (max-width: 768px) {
                            .desktop-table { display: none !important; }
                            .mobile-table { display: block !important; }
                        }
                    </style>
                </div>
            </div>            
        </div>
    </div>

    <form id="deleteForm" method="POST" action="{{ route('rekap.deleteTanggal') }}">
    @csrf
        <input type="hidden" name="tanggal" id="deleteTanggal">
    </form>

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
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {

                const tanggal = this.dataset.tanggal;

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Hapus semua data tanggal ${tanggal}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        document.getElementById('deleteTanggal').value = tanggal;
                        document.getElementById('deleteForm').submit();

                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Dibatalkan',
                            text: 'Data tidak dihapus.',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                });

            });
        });
    </script>
</body>
</html>




