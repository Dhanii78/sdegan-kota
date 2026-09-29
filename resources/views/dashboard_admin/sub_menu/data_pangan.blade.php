<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Pangan</title>
    <link rel="stylesheet" href="{{ asset ('css/data_pangan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/mobile-nav.js') }}"></script>
    <style>
        @media (max-width: 767px) {
            .container > .navbar { display: none !important; }
            .desktop-admin-data { display: none !important; }
            .mobile-admin-data { display: block !important; }
        }
        @media (min-width: 768px) {
            .desktop-admin-data { display: block !important; }
            .mobile-admin-data { display: none !important; }
        }
        
        /* Mobile admin actions buttons */
        .mobile-admin-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        .btn-mob-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            font-size: 0.9rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .btn-mob-success {
            background-color: #28a745;
        }
        .btn-mob-danger {
            background-color: #dc3545;
        }
        
        /* Mobile filter card */
        .mobile-filter-card {
            background: #fff;
            border: 1px solid #becbb9;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .mobile-filter-card h3 {
            margin: 0 0 14px 0;
            font-size: 1rem;
            color: #08202b;
            font-weight: 700;
            text-align: left;
        }
        .mobile-filter-card .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
            text-align: left;
        }
        .mobile-filter-card .filter-group label {
            font-size: 0.85rem;
            color: #424d42;
            font-weight: 600;
        }
        .mobile-filter-card .filter-group input {
            padding: 10px;
            border: 1px solid #becbb9;
            border-radius: 6px;
            font-size: 0.95rem;
            background-color: #f1f9ff;
        }
        .mobile-filter-card .filter-buttons {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 8px;
            margin-top: 16px;
        }
        .btn-filter-submit {
            background-color: #056bad;
            color: white;
            border: none;
            padding: 10px;
            font-weight: bold;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .btn-filter-reset {
            background-color: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px;
            font-weight: bold;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Mobile actions inside card detail */
        .btn-action-mob {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            color: #fff !important;
            font-size: 0.78rem;
            font-weight: 700;
            border-radius: 4px;
            text-decoration: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.1s;
        }
        .btn-action-mob--edit {
            background-color: #ffc107;
            color: black !important;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
        }
        .btn-action-mob--revisi {
            background-color: #fd1488;
        }
    </style></head>
<body class="role-portal">
    @include('components.admin-mobile-chrome', ['active' => 'data_pangan']) 
    <div class="desktop-admin-data">
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

        <h2 class="center-text">Data Pangan</h2>
        
        <div class="main-content">
            <div style="width: 60%; margin: 0 auto 20px auto; display: flex; justify-content: center; gap: 20px;">
                <a href="{{ route('dashboard_admin.sub_menu.input_data') }}" class="btn-custom btn-success">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Data Pangan
                </a>
                <a href="{{ route('dashboard_admin.sub_menu.verify_data') }}" class="btn-custom btn-danger">
                    <i class="fa-solid fa-check-circle"></i> Verifikasi Data
                </a>
            </div>      

            <div class="filter-container">
                <form method="GET" action="">
                    <label>Dari Tanggal:</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">

                    <label>Sampai:</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">

                    <button type="submit" class="btn-filter">
                        <i class="fa fa-search"></i> Filter
                    </button>

                    <a href="{{ url()->current() }}" class="btn-edit">
                        Reset
                    </a>
                </form>
            </div>

            <div class="table-container">
                <table id="entryDataTable" class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Komoditas</th>
                            <th>Harga HET</th>
                            <th>Harga Kemarin</th>
                            <th>Harga Hari Ini</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataKomoditas as $data)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-checkbox" name="ids[]" value="{{ $data->id }}">
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->tanggal ?? '-' }}</td>
                                <td>{{ $data->nama_komoditas ?? '-' }}</td>
                                <td>Rp.{{ number_format($data->harga_het ?? 0, 0, ',', '.') }}</td>
                                <td>Rp.{{ number_format($data->harga_kemarin ?? 0, 0, ',', '.') }}</td>
                                <td>Rp.{{ number_format($data->harga_hari_ini ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if($data->status_verifikasi == 'Valid')
                                        ✅ Valid
                                    @elseif($data->status_verifikasi == 'Tidak_valid' || $data->status_verifikasi == 'Tidak Valid')
                                        ❌ Tidak Valid Harga Salah
                                    @elseif($data->status_verifikasi == 'Harga_Tidak_Wajar')
                                        ⚠️ Tidak Valid: Harga Tidak Wajar
                                    @elseif($data->status_verifikasi == 'Data_Ganda')
                                        ⚠️ Tidak Valid: Data Ganda
                                    @elseif($data->status_verifikasi == 'Revisi')
                                        🔄 Revisi
                                    @else
                                        Belum Diverifikasi
                                    @endif
                                </td>                                                                                  
                                <td class="text-center">

                                    @if($data->status_verifikasi == 'Valid')

                                    <a href="javascript:void(0)"
                                    onclick="confirmTurunkan({{ $data->id }})"
                                    class="btn btn-revisi btn-sm" >
                                        <i class="fa-solid fa-arrow-down"></i> Turunkan
                                    </a>

                                    <form id="turunkan-form-{{ $data->id }}"
                                        action="{{ route('dashboard_admin.sub_menu.data.turunkan', $data->id) }}"
                                        method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    @else

                                    <a href="{{ route('dashboard_admin.sub_menu.data.edit', $data->id) }}" class="btn btn-edit btn-sm">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>

                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                                    <em>Belum ada data pangan hari ini.</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>                    
                </table>
            </div>  
        </div>

        <form id="bulkDeleteForm" action="{{ route('dashboard_admin.sub_menu.data.bulkDelete') }}" method="POST">
            @csrf
        </form>
        <div class="bulk-action-container">
            <button type="button" id="btnBulkDelete" class="btn-bulk-delete">
                <i class="fas fa-trash"></i> Hapus Data
            </button>
        </div>
    </div></div>

    <div class="mobile-admin-data">
        <main class="mobile-history-view">
            <h1>Data Pangan</h1>
            <p>Kelola dan pantau seluruh data komoditas pangan.</p>

            <div class="mobile-admin-actions">
                <a href="{{ route('dashboard_admin.sub_menu.input_data') }}" class="btn-mob-action btn-mob-success">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Data
                </a>
                <a href="{{ route('dashboard_admin.sub_menu.verify_data') }}" class="btn-mob-action btn-mob-danger">
                    <i class="fa-solid fa-check-circle"></i> Verifikasi Data
                </a>
            </div>

            <div class="mobile-filter-card">
                <h3><i class="fa fa-sliders"></i> Filter Tanggal</h3>
                <form method="GET" action="">
                    <div class="filter-group">
                        <label>Dari Tanggal:</label>
                        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">
                    </div>
                    <div class="filter-group">
                        <label>Sampai Tanggal:</label>
                        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                    </div>
                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter-submit"><i class="fa fa-search"></i> Terapkan</button>
                        <a href="{{ url()->current() }}" class="btn-filter-reset">Reset</a>
                    </div>
                </form>
            </div>

            <section class="history-list" aria-label="Daftar laporan pangan">
                @php
                    $reportGroups = $dataKomoditas->groupBy('tanggal')->sortKeysDesc();
                @endphp
                @forelse($reportGroups as $tanggal => $reports)
                    @php
                        $statuses = $reports->pluck('status_verifikasi');
                        if ($statuses->every(fn ($status) => $status === 'Valid')) {
                            $statusLabel = 'Tervalidasi';
                            $statusClass = 'valid';
                        } elseif ($statuses->contains(fn ($status) => in_array($status, ['Belum_Diverifikasi', 'Belum Diverifikasi'], true))) {
                            $statusLabel = 'Menunggu';
                            $statusClass = 'pending';
                        } else {
                            $statusLabel = 'Perlu Revisi';
                            $statusClass = 'rejected';
                        }
                    @endphp
                    <div class="history-card-wrapper">
                        <article class="history-card" onclick="toggleHistoryCard(this)">
                            <div class="history-card__date">
                                <i class="fas fa-calendar-day" aria-hidden="true"></i>
                                {{ \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}
                            </div>
                            <span class="history-status history-status--{{ $statusClass }}">{{ $statusLabel }}</span>
                            <h2 class="history-card__title">Laporan harga pangan</h2>
                            <p class="history-card__meta">{{ $reports->count() }} komoditas</p>
                            <i class="fas fa-chevron-right history-card__arrow" aria-hidden="true"></i>
                        </article>
                        <div class="history-card__details" style="display: none;">
                            <div class="details-divider"></div>
                            <div class="details-list">
                                @foreach($reports as $report)
                                    <div class="details-item">
                                        <div class="details-item__info">
                                            <div class="details-item__name">{{ $report->nama_komoditas }}</div>
                                            <div class="details-item__prices">
                                                <span class="price-lbl">Kemarin:</span> <span class="price-val">Rp{{ number_format($report->harga_kemarin, 0, ',', '.') }}</span> |
                                                <span class="price-lbl">Hari Ini:</span> <span class="price-val price-val--today">Rp{{ number_format($report->harga_hari_ini, 0, ',', '.') }}</span>
                                            </div>
                                            @if($report->status_verifikasi !== 'Valid' && !in_array($report->status_verifikasi, ['Belum_Diverifikasi', 'Belum Diverifikasi'], true))
                                                <div class="details-item__reason text-danger">
                                                    <i class="fa-solid fa-circle-info"></i> {{ str_replace('_', ' ', $report->status_verifikasi) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="details-item__action">
                                            @if($report->status_verifikasi === 'Valid')
                                                <a href="javascript:void(0)"
                                                   onclick="confirmTurunkan({{ $report->id }})"
                                                   class="btn-action-mob btn-action-mob--revisi"
                                                   title="Turunkan Status">
                                                    <i class="fa-solid fa-arrow-down"></i> Turunkan
                                                </a>
                                                <form id="turunkan-form-{{ $report->id }}"
                                                      action="{{ route('dashboard_admin.sub_menu.data.turunkan', $report->id) }}"
                                                      method="POST"
                                                      style="display:none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                            @else
                                                <a href="{{ route('dashboard_admin.sub_menu.data.edit', $report->id) }}" class="btn-action-mob btn-action-mob--edit">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="history-empty">Belum ada laporan yang sesuai dengan filter.</p>
                @endforelse
            </section>
        </main>
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
                        <a href="#" class="btn-logout" onclick="handleLogout(event)">Logout</a>
                    </div>
                    <div class="profile-logo">
                        <img src="{{ asset('images/profileLogo.png') }}" alt="Profile Logo" />
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>
        @endif

        @if (session('error'))
        <script>
            Swal.fire({
                title: "Gagal!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        </script>
        @endif

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
        // Ambil elemen modal profil
        var profileModal = document.getElementById("profileModal");
    
        // Ambil elemen yang membuka modal profil
        var profileBtn = document.getElementById("profile-link");
    
        // Ambil elemen <span> yang menutup modal profil
        var profileSpan = document.getElementsByClassName("close")[0];
    
        // Ketika pengguna mengklik link profil, buka modal
        profileBtn.onclick = function() {
            profileModal.style.display = "block"; // Tampilkan modal
            setTimeout(function() { 
                profileModal.classList.add("show");
                profileModal.classList.remove("hide"); // Pastikan untuk menghapus kelas 'hide' jika ada
            }, 10); // Waktu untuk memastikan display:block diterapkan
        }
    
        // Ketika pengguna mengklik <span> (x), tutup modal
        profileSpan.onclick = function() {
            profileModal.classList.remove("show"); // Hapus kelas 'show' untuk memulai animasi keluar
            profileModal.classList.add("hide"); // Tambahkan kelas 'hide' untuk memulai animasi penutupan
            setTimeout(function() { 
                profileModal.style.display = "none"; // Sembunyikan modal setelah animasi selesai
            }, 300); // Durasi yang sesuai dengan animasi CSS (0.3s)
        }
    
        // Ketika pengguna mengklik di luar modal, tutup modal
        window.onclick = function(event) {
            if (event.target == profileModal) {
                profileModal.classList.remove("show"); // Hapus kelas 'show' untuk memulai animasi keluar
                profileModal.classList.add("hide"); // Tambahkan kelas 'hide' untuk memulai animasi penutupan
                setTimeout(function() { 
                    profileModal.style.display = "none"; // Sembunyikan modal setelah animasi selesai
                }, 300); // Durasi yang sesuai dengan animasi CSS (0.3s)
            }
        }
    
        // Ambil elemen modal import
        var importDataModal = document.getElementById("importDataModal");
    
        // Ambil elemen yang membuka modal import
        var importDataBtn = document.getElementById("importDataBtn");
    
        // Ambil elemen <span> yang menutup modal import
        var importDataSpan = document.getElementsByClassName("close")[0];
    
        // Ketika pengguna mengklik tombol import, buka modal
        importDataBtn.onclick = function() {
            importDataModal.style.display = "block"; // Set display to block first
            setTimeout(function() {
                importDataModal.classList.add("show");
            }, 10); // Slight delay to ensure the transition runs
        }
    
        // Ketika pengguna mengklik <span> (x), tutup modal
        importDataSpan.onclick = function() {
            importDataModal.classList.remove("show");
            setTimeout(function() {
                importDataModal.style.display = "none";
            }, 300); // Delay to match the transition duration
        }
    
        // Ketika pengguna mengklik di luar modal, tutup modal
        window.onclick = function(event) {
            if (event.target == importDataModal) {
                importDataModal.classList.remove("show");
                setTimeout(function() {
                    importDataModal.style.display = "none";
                }, 300); // Delay to match the transition duration
            }
        }
    </script>
    <script>
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
        // Update status dari localStorage ke kolom Status di Data Pangan
        document.querySelectorAll('tr').forEach(function(row, index) {
            let status = localStorage.getItem('status_' + index);
            let statusCell = row.querySelector('#status-' + index);

            if (status) {
                if (status === 'valid') {
                    statusCell.innerHTML = '✅ Valid';
                } else if (status === 'tidak_valid') {
                    statusCell.innerHTML = '❌ Tidak Valid';
                }
            }
        });
    </script>
    <script>
        function toggleSelectAll(source) {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = source.checked);
        }
    </script>
    <script>
        document.getElementById('btnBulkDelete').addEventListener('click', function() {
            const checked = document.querySelectorAll('input[name="ids[]"]:checked');
            
            if(checked.length === 0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih data',
                    text: 'Silakan pilih data yang ingin dihapus.',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Anda akan menghapus ${checked.length} data ??`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit form POST ke route bulkDelete
                    document.getElementById('bulkDeleteForm').submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
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
        function confirmTurunkan(id) {

            Swal.fire({
                title: 'Turunkan Status?',
                text: "Data akan dikembalikan untuk direvisi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, turunkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('turunkan-form-' + id).submit();
                }
            });

        }
    </script>
    <script>
        function toggleHistoryCard(cardElement) {
            cardElement.classList.toggle('is-expanded');
            const details = cardElement.nextElementSibling;
            if (details) {
                if (details.style.display === 'none' || !details.style.display) {
                    details.style.display = 'block';
                } else {
                    details.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>




