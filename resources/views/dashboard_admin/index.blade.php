<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="{{ asset('js/mobile-nav.js') }}"></script>
    </head>
<body>
        <div class="mobile-overlay"></div>
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
        <div class="filter-bar">
            <label for="commodity">Komoditas:</label>
            <select id="commodity"></select>

            <label for="start-date">Tanggal Mulai:</label>
            <input type="date" id="start-date">

            <label for="end-date">Tanggal Akhir:</label>
            <input type="date" id="end-date">

            <button id="filter-button">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </div>
        <div class="chart-row">
            <div class="container-stat">
                <p class="logo-title">Grafik harga komoditas dan periode pilih</p>
                <div class="chart-wrapper">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
            <!-- <div class="container-statall">
                <p class="logo-title">Grafik Harga Periode Pilih Semua Komoditas</p>
                <canvas id="chartCanvas"></canvas>
            </div> -->
        </div>                  
        <div class="content-container">
            <div class="container-kiri">
                <p class="logo-title">Rekapitulasi Harga Periode Pilih</p>
                <div class="cards">
                    <!-- Card 1 -->
                    <div class="card">
                        <h3>Harga Rata-rata</h3>
                        <div class="card-body" id="card1-body">
                            <!-- JS akan isi di sini -->
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="card">
                        <h3>Harga Akhir Periode</h3>
                        <div class="card-body" id="card2-body">
                            <!-- JS akan isi di sini -->
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="card">
                        <h3>Rekor harga</h3>
                        <div class="card-body" id="card3-body">
                            <!-- JS akan isi di sini -->
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="card">
                        <h3>Keterangan</h3>
                        <div class="card-body" id="card4-body">
                            <!-- JS akan isi di sini -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-kanan">
                <p class="logo-title">Rekapitulasi Harga Keseluruhan Periode</p>
                <div class="cards">
                    <!-- Card 1 → Rata-rata -->
                    <div class="card">
                        <h3>Harga Rata-rata</h3>
                        <div class="card-body" id="card-right-1">
                            <!-- Data akan diisi JS -->
                        </div>
                    </div>

                    <!-- Card 2 → Harga Terkini -->
                    <div class="card">
                        <h3>Harga Terkini</h3>
                        <div class="card-body" id="card-right-2">
                            <!-- Data akan diisi JS -->
                        </div>
                    </div>

                    <!-- Card 3 → Harga Ekstrem -->
                    <div class="card">
                        <h3>Rekor harga</h3>
                        <div class="card-body" id="card-right-3">
                            <!-- Data akan diisi JS -->
                        </div>
                    </div>

                    <!-- Card 4 → Info Data -->
                    <div class="card">
                        <h3>Keterangan</h3>
                        <div class="card-body" id="card-right-4">
                            <!-- Data akan diisi JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="download-container">
                <button id="download-pdf" onclick="downloadPDF()">
                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF
                </button>
            </div>   
        </div>
        <div class="container-besar">
            <p class="logo-title">INDIKATOR SPHP</p>
            <!-- Additional Date Filter for Indikator SPHP -->
            <div class="filter-bar">
                <form method="GET" action="{{ route('dashboard_operator.index') }}">
                    <label for="tanggal">Pilih Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                        value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}">

                    <button type="submit">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </form>
            </div>
            {{-- TABEL --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Komoditas</th>
                        <th>Satuan</th>
                        <th>HET/HAP</th>
                        @php
                            $filterTanggal = \Carbon\Carbon::parse($tanggal);
                            $today = \Carbon\Carbon::today();
                        @endphp

                        <th>
                            @if($filterTanggal->isToday())
                                Harga Hari Ini
                            @else
                                Harga {{ $filterTanggal->format('d/m/Y') }}
                            @endif
                        </th>

                        <th>
                            @if($filterTanggal->isToday())
                                Harga Sebelumnya
                            @else
                                Harga {{ $filterTanggal->subDay()->format('d/m/Y') }}
                            @endif
                        </th>
                        <th>
                            Rata-rata 
                            {{ \Carbon\Carbon::parse($tanggal)->subDays(6)->format('d/m/Y') }}
                            -
                            {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                        </th>
                        <th>
                            @if($filterTanggal->isToday())
                                Indikator Hari Ini
                            @else
                                Indikator {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                            @endif
                        </th>
                        <th>Indikator Rata-rata</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($sumber_data as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $data->nama_komoditas }}</td>
                    <td>{{ $data->satuan }}</td>
                    <td>
                        @php
                            $hetValue = isset($hetData) ? ($hetData->{$data->nama_komoditas} ?? null) : null;
                            $displayHet = is_numeric($hetValue) ? $hetValue : $data->hethap;
                        @endphp
                        {{ number_format($displayHet, 0, ',', '.') }}
                    </td>

                    {{-- ✅ Harga Hari Ini + Panah --}}
                    <td>
                        @php
                            $latest = $latestRekap->{$data->nama_komoditas} ?? null;
                            $previous = $previousRekap->{$data->nama_komoditas} ?? null;
                        @endphp

                        {{ is_numeric($latest) ? number_format($latest, 0, ',', '.') : '-' }}

                        @if(is_numeric($latest) && is_numeric($previous))
                            @if ($latest > $previous)
                                <span class="text-naik">
                                    <i class="fa fa-long-arrow-up"></i>
                                </span>
                            @elseif ($latest < $previous)
                                <span class="text-turun">
                                    <i class="fa fa-long-arrow-down"></i>
                                </span>
                            @else
                                <span class="text-tetap">−</span>
                            @endif
                        @endif
                    </td>

                    {{-- ✅ Harga Kemarin --}}
                    <td>
                        {{ is_numeric($previous) ? number_format($previous, 0, ',', '.') : '-' }}
                    </td>

                    {{-- ✅ Rata-rata 7 Hari --}}
                    <td>
                        @php
                            $avg = $sevenDayAverage[$data->nama_komoditas] ?? null;
                        @endphp

                        {{ is_numeric($avg) ? number_format($avg, 0, ',', '.') : '-' }}
                    </td>

                    {{-- ✅ Indikator Hari Ini --}}
                    <td>
                        @php
                            $status = $indicators[$data->nama_komoditas] ?? null;
                        @endphp

                        @if($status == 'Aman')
                            <span class="text-aman">Aman</span>
                        @elseif($status == 'Waspada')
                            <span class="text-waspada">Waspada</span>
                        @elseif($status == 'Intervensi')
                            <span class="text-intervensi">Intervensi</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- ✅ Indikator Rata-rata --}}
                    <td>
                        @php
                            $statusAvg = $averageIndicators[$data->nama_komoditas] ?? null;
                        @endphp

                        @if($statusAvg == 'Aman')
                            <span class="text-aman">Aman</span>
                        @elseif($statusAvg == 'Waspada')
                            <span class="text-waspada">Waspada</span>
                        @elseif($statusAvg == 'Intervensi')
                            <span class="text-intervensi">Intervensi</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>
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
        function downloadPDF() {
            const commodity = document.getElementById('commodity').value;
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            // Validate the dates
            if (!commodity || !startDate || !endDate) {
                alert('Please fill in all the fields.');
                return;
            }

            // Redirect to the route that generates the PDF
            window.location.href = `/unduh-pdf?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Other existing JavaScript code

            // Event click button download PDF
            document.getElementById('download-pdf').addEventListener('click', downloadPDF);
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
        // === SIMPAN FILTER KE LOCAL STORAGE ===
        function saveFilters() {
            const filterData = {
                commodity: document.getElementById('commodity').value,
                startDate: document.getElementById('start-date').value,
                endDate: document.getElementById('end-date').value,
            };
            localStorage.setItem('dashboardFilters', JSON.stringify(filterData));
        }

        // === LOAD FILTER DARI LOCAL STORAGE SAAT PAGE DIBUKA ===
        window.addEventListener('DOMContentLoaded', () => {
            const savedFilters = JSON.parse(localStorage.getItem('dashboardFilters'));
            if (savedFilters) {
                document.getElementById('commodity').value = savedFilters.commodity || '';
                document.getElementById('start-date').value = savedFilters.startDate || '';
                document.getElementById('end-date').value = savedFilters.endDate || '';

                // Langsung panggil AJAX untuk load data berdasarkan filter
                loadFilteredData(savedFilters.commodity, savedFilters.startDate, savedFilters.endDate);
            }
        });

        // === KETIKA USER GANTI FILTER, LANGSUNG SIMPAN KE LOCAL STORAGE ===
        document.getElementById('commodity').addEventListener('change', saveFilters);
        document.getElementById('start-date').addEventListener('change', saveFilters);
        document.getElementById('end-date').addEventListener('change', saveFilters);

        // === GANTI DENGAN FUNGSI AJAX YANG KAMU SUDAH PUNYA ===
        function loadFilteredData(commodity, startDate, endDate) {
            // Contoh AJAX untuk grafik utama
            fetch(`/api/filter?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                .then(res => res.json())
                .then(data => {
                    // Panggil fungsi update grafik & tabel kamu di sini
                    console.log('Filtered data loaded:', data);

                    // Misalnya kamu punya fungsi:
                    // updateChart(data.chart)
                    // updateTable(data.table)
                });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let myChart = null;
            let myChartAll = null;

            const warnaDasar = [
                '#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231',
                '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe',
                '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000',
                '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080'
            ];

            // Muat daftar komoditas ke <select>
            function loadCommodities() {
                fetch('/api/komoditas')
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('commodity');
                        select.innerHTML = ''; // kosongkan sebelum isi
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.nama_komoditas;
                            opt.textContent = item.nama_komoditas;
                            select.appendChild(opt);
                        });

                        // Set dari localStorage jika ada
                        const saved = JSON.parse(localStorage.getItem('dashboardFilters'));
                        if (saved) {
                            select.value = saved.commodity || '';
                            document.getElementById('start-date').value = saved.startDate || '';
                            document.getElementById('end-date').value = saved.endDate || '';

                            loadChartData();
                            loadChartDataAll();
                            loadStatistics();
                            loadTotalData();
                        }
                    });
            }
            // Simpan filter ke localStorage
            function saveFilters() {
                const filterData = {
                    commodity: document.getElementById('commodity').value,
                    startDate: document.getElementById('start-date').value,
                    endDate: document.getElementById('end-date').value,
                };
                localStorage.setItem('dashboardFilters', JSON.stringify(filterData));
            }
            function formatDate(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-'); // ["2025", "10", "09"]
                return `${parts[2]} - ${parts[1]} - ${parts[0]}`;
            }
            // Statistik Periode
            function loadStatistics() {
                const commodity = document.getElementById('commodity').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                fetch(`/api/filter?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                    .then(res => res.json())
                    .then(data => {
                        const statusAvgClass = data.statusAverage === 'Intervensi' ? 'text-intervensi' :
                            data.statusAverage === 'Waspada' ? 'text-waspada' : 'text-aman';
                        const statusLatestClass = data.statusLatest === 'Intervensi' ? 'text-intervensi' :
                            data.statusLatest === 'Waspada' ? 'text-waspada' : 'text-aman';

                        // Card 1 → Rata-rata Periode
                        document.getElementById('card1-body').innerHTML = `
                            <p>Harga Rata-rata Periode :  Rp.${data.averagePrice}</p>
                            <p>Status Harga Rata-rata Periode :  <span class="${statusAvgClass}">${data.statusAverage}</span></p>
                        `;

                        // Card 2 → Akhir Periode
                        document.getElementById('card2-body').innerHTML = `
                            <p>Harga Akhir Periode :  Rp.${data.latestPrice}</p>
                            <p>Tanggal Harga Akhir Periode :  ${formatDate(data.latestDate)}</p>
                            <p>Status Harga Akhir Periode :  <span class="${statusLatestClass}">${data.statusLatest}</span></p>
                        `;

                        // Card 3 → Harga Ekstrem
                        document.getElementById('card3-body').innerHTML = `
                            <p>Harga Tertinggi :  Rp.${data.highestPrice}</p>
                            <p>Tanggal Harga Tertinggi :  ${formatDate(data.highestDate)}</p>
                            <p>Harga Terendah :  Rp.${data.lowestPrice}</p>
                            <p>Tanggal Harga Terendah :  ${formatDate(data.lowestDate)}</p>
                        `;

                        // Card 4 → Info Data
                        document.getElementById('card4-body').innerHTML = `
                            <p>Jumlah Data :  ${data.dataCount}</p>
                            <p>Status CV :  ${data.statusCV}</p>
                        `;
                        // ✅ jalankan animasi setelah card diisi
                        document.querySelectorAll('.card').forEach(card => {
                            setTimeout(() => {
                                card.classList.add('show');
                            }, 100);
                        });
                    })
                    .catch(err => console.error('Gagal load statistik:', err));
            }
            // Statistik Total
            function loadTotalData() {
                const commodity = document.getElementById('commodity').value;

                fetch(`/api/loadTotalData?commodity=${commodity}`)
                    .then(res => res.json())
                    .then(data => {
                        const statusAvgClass = data.statusAverage === 'Intervensi' ? 'text-intervensi' :
                            data.statusAverage === 'Waspada' ? 'text-waspada' : 'text-aman';
                        const statusLatestClass = data.statusLatest === 'Intervensi' ? 'text-intervensi' :
                            data.statusLatest === 'Waspada' ? 'text-waspada' : 'text-aman';

                        // Card 1 → Rata-rata
                        document.getElementById('card-right-1').innerHTML = `
                            <p>Harga Rata-rata :  Rp.${data.totalAveragePrice}</p>
                            <p>Status Rata-rata :  <span class="${statusAvgClass}">${data.statusAverage}</span></p>
                        `;

                        // Card 2 → Harga Terkini
                        document.getElementById('card-right-2').innerHTML = `
                            <p>Harga Terkini :  Rp.${data.latestPrice}</p>
                            <p>Tanggal Harga Terkini :  ${formatDate(data.latestDate)}</p>
                            <p>Status Harga Terkini :  <span class="${statusLatestClass}">${data.statusLatest}</span></p>
                        `;

                        // Card 3 → Harga Ekstrem
                        document.getElementById('card-right-3').innerHTML = `
                            <p>Harga Tertinggi :  Rp.${data.totalHighestPrice}</p>
                            <p>Tanggal Harga Tertinggi :  ${formatDate(data.totalHighestDate)}</p>
                            <p>Harga Terendah :  Rp.${data.totalLowestPrice}</p>
                            <p>Tanggal Harga Terendah :  ${formatDate(data.totalLowestDate)}</p>
                        `;

                        // Card 4 → Info Data
                        document.getElementById('card-right-4').innerHTML = `
                            <p>Jumlah Data :  ${data.totalDataCount}</p>
                            <p>Status CV :  ${data.statusCV}</p>
                        `;
                        // ✅ jalankan animasi setelah card diisi
                        document.querySelectorAll('.card').forEach(card => {
                            setTimeout(() => {
                                card.classList.add('show');
                            }, 100);
                        });
                    })
                    .catch(err => console.error('Gagal load statistik:', err));
            }
            function initializeChart() {
                const ctx = document.getElementById('myChart').getContext('2d');
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'Harga Komoditas',
                                data: [],
                                borderColor: 'blue',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: false,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'HET',
                                data: [],
                                borderColor: 'red',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0.3,
                                fill: false,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    parser: 'yyyy-MM-dd',
                                    tooltipFormat: 'll',
                                    unit: 'day',
                                    displayFormats: {
                                        day: 'yyyy-MM-dd'
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Harga (Rp)'
                                },
                                beginAtZero: false, // biarkan tidak selalu mulai dari 0
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
            function loadChartData() {
                const commodity = document.getElementById('commodity').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                fetch(`/api/grafik?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                    .then(res => res.json())
                    .then(data => {
                        const harga = data.map(item => ({
                            x: item.date,
                            y: item.price
                        }));

                        const het = data.map(item => ({
                            x: item.date,
                            y: item.hethap
                        }));

                        myChart.data.labels = [];
                        myChart.data.datasets[0].data = harga;
                        myChart.data.datasets[1].data = het;
                        myChart.update();

                        // 🔥 Cek apakah 7 hari terakhir semuanya status intervensi
                        if (data.length >= 7) {
                            const last7Days = data.slice(-7); // ambil 7 data terakhir
                            const allIntervensi = last7Days.every(item => 
                                parseFloat(item.price) >= parseFloat(item.harga_intervensi)
                            );

                            if (allIntervensi) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: `⚠️ Komoditas ${commodity}`,
                                    text: 'Harga sudah 7 hari berturut-turut berstatus INTERVENSI!',
                                    timer: 5000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        }
                    })
                    .catch(err => console.error('Gagal load chart:', err));
            }
            function initializeChartAll(labels = [], datasets = []) {

                const canvas = document.getElementById('chartCanvas');

                // Jika canvas tidak ada di halaman, hentikan fungsi
                if (!canvas) {
                    return;
                }

                const ctx = canvas.getContext('2d');

                if (myChartAll) {
                    myChartAll.destroy(); // Hancurkan chart sebelumnya
                }

                myChartAll = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            },
                            y: {
                                grace: '10%',
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
            function loadChartDataAll() {
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                if (!startDate || !endDate) return alert("Isi tanggal dulu!");

                fetch(`/api/grafikAll?start_date=${startDate}&end_date=${endDate}`)
                    .then(res => res.json())
                    .then(data => {
                        const allLabels = [];
                        const datasets = [];
                        let i = 0;

                        for (const [komoditas, values] of Object.entries(data)) {
                            const labels = values.map(v => v.date);
                            const harga = values.map(v => v.price);
                            if (allLabels.length === 0) allLabels.push(...labels);

                            datasets.push({
                                label: komoditas,
                                data: harga,
                                borderColor: warnaDasar[i % warnaDasar.length],
                                backgroundColor: warnaDasar[i % warnaDasar.length],
                                borderWidth: 2,
                                tension: 0.3,
                                fill: false,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            });
                            i++;
                        }

                        initializeChartAll(allLabels, datasets);
                    })
                    .catch(err => console.error('Gagal load chart all:', err));
            }
             
            // Inisialisasi awal
            initializeChart();
            initializeChartAll();
            loadCommodities();

            // Inisialisasi awal
            initializeChart();
            initializeChartAll();
            loadCommodities();

            // Event filter button
            document.getElementById('filter-button').addEventListener('click', () => {
                saveFilters();
                loadStatistics();
                loadTotalData();
                loadChartData();
                loadChartDataAll();
            });

            // Event simpan filter realtime
            ['commodity', 'start-date', 'end-date'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    saveFilters();

                    const commodity = document.getElementById('commodity').value;
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;

                    // Hanya jalankan semua fungsi kalau startDate dan endDate sudah diisi
                    if (startDate && endDate) {
                        loadChartData();
                        loadChartDataAll();
                        loadStatistics();
                        loadTotalData();
                    }
                });
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
    @include('components.notification-handler')
</body>
</html>


