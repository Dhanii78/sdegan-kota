<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Dashboard untuk Umum</title>
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
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
                <a href="dashboard_umum"><i class="fas fa-home"></i> Dashboard</a>
            </div>
            <div class="datetime-umum">
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
                <canvas id="myChart"></canvas>
            </div>
        </div>                
        <div class="content-container">
            <div class="container-kiri">
                <p class="logo-title">Rekapitulasi Harga Periode Pilih</p>
                <div class="cards">
                    <div class="card">
                        <h3>Harga Rata-rata</h3>
                        <div class="card-body" id="card1-body"></div>
                    </div>
                    <div class="card">
                        <h3>Harga Akhir Periode</h3>
                        <div class="card-body" id="card2-body"></div>
                    </div>
                    <div class="card">
                        <h3>Rekor harga</h3>
                        <div class="card-body" id="card3-body"></div>
                    </div>
                    <div class="card">
                        <h3>Keterangan</h3>
                        <div class="card-body" id="card4-body"></div>
                    </div>
                </div>
            </div>
            <div class="container-kanan">
                <p class="logo-title">Rekapitulasi Harga Keseluruhan Periode</p>
                <div class="cards">
                    <div class="card">
                        <h3>Harga Rata-rata</h3>
                        <div class="card-body" id="card-right-1"></div>
                    </div>
                    <div class="card">
                        <h3>Harga Terkini</h3>
                        <div class="card-body" id="card-right-2"></div>
                    </div>
                    <div class="card">
                        <h3>Rekor harga</h3>
                        <div class="card-body" id="card-right-3"></div>
                    </div>
                    <div class="card">
                        <h3>Keterangan</h3>
                        <div class="card-body" id="card-right-4"></div>
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
            <div class="filter-bar">
                <form method="GET" action="{{ route('dashboard_umum') }}">
                    <label for="tanggal">Pilih Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                        value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                    <button type="submit">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </form>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Komoditas</th>
                        <th>Satuan</th>
                        <th>HET/HAP</th>
                        @php
                            $filterTanggal = \Carbon\Carbon::parse($tanggal);
                        @endphp
                        <th>
                            @if($filterTanggal->isToday()) Harga Hari Ini @else Harga {{ $filterTanggal->format('d/m/Y') }} @endif
                        </th>
                        <th>
                            @if($filterTanggal->isToday()) Harga Sebelumnya @else Harga {{ $filterTanggal->subDay()->format('d/m/Y') }} @endif
                        </th>
                        <th>
                            Rata-rata {{ \Carbon\Carbon::parse($tanggal)->subDays(6)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                        </th>
                        <th>
                            @if($filterTanggal->isToday()) Indikator Hari Ini @else Indikator {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }} @endif
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
                    <td>{{ number_format($data->hethap, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $latest = $latestRekap->{$data->nama_komoditas} ?? null;
                            $previous = $previousRekap->{$data->nama_komoditas} ?? null;
                        @endphp
                        {{ is_numeric($latest) ? number_format($latest, 0, ',', '.') : '-' }}
                        @if(is_numeric($latest) && is_numeric($previous))
                            @if ($latest > $previous) <span class="text-naik"><i class="fa fa-long-arrow-up"></i></span>
                            @elseif ($latest < $previous) <span class="text-turun"><i class="fa fa-long-arrow-down"></i></span>
                            @else <span class="text-tetap">−</span> @endif
                        @endif
                    </td>
                    <td>{{ is_numeric($previous) ? number_format($previous, 0, ',', '.') : '-' }}</td>
                    <td>
                        @php $avg = $sevenDayAverage[$data->nama_komoditas] ?? null; @endphp
                        {{ is_numeric($avg) ? number_format($avg, 0, ',', '.') : '-' }}
                    </td>
                    <td>
                        @php $status = $indicators[$data->nama_komoditas] ?? null; @endphp
                        @if($status == 'Aman') <span class="text-aman">Aman</span>
                        @elseif($status == 'Waspada') <span class="text-waspada">Waspada</span>
                        @elseif($status == 'Intervensi') <span class="text-intervensi">Intervensi</span>
                        @else <span class="text-muted">-</span> @endif
                    </td>
                    <td>
                        @php $statusAvg = $averageIndicators[$data->nama_komoditas] ?? null; @endphp
                        @if($statusAvg == 'Aman') <span class="text-aman">Aman</span>
                        @elseif($statusAvg == 'Waspada') <span class="text-waspada">Waspada</span>
                        @elseif($statusAvg == 'Intervensi') <span class="text-intervensi">Intervensi</span>
                        @else <span class="text-muted">-</span> @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>  
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'info',
                title: 'Hai!',
                text: 'Pilih Komoditas dan Periode tanggal dulu yuk...',
                confirmButtonColor: '#89c4af'
            });
        });

        const toggleBtn = document.querySelector('.toggle-btn');
        const navbar = document.querySelector('.navbar');
        const container = document.querySelector('.container');
        const layoutContainer = document.querySelector(".layout-container");
        const overlay = document.querySelector('.mobile-overlay');

        function toggleMobileMenu() {
            if (navbar) {
                navbar.classList.toggle('visible');
                navbar.classList.toggle('show');
                navbar.classList.toggle('hidden');
            }
            if (overlay) {
                overlay.classList.toggle('active');
            }
            document.body.classList.toggle('sidebar-open');
            container?.classList.toggle('sidebar-hidden'); 
            toggleBtn?.classList.toggle('hide'); 
            container?.classList.toggle("shifted"); 
            layoutContainer?.classList.toggle("shifted");
        }

        toggleBtn?.addEventListener('click', toggleMobileMenu);
        overlay?.addEventListener('click', toggleMobileMenu);
    </script>    
    <script>
        function downloadPDF() {
            const commodity = document.getElementById('commodity').value;
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            if (!commodity || !startDate || !endDate) {
                alert('Please fill in all the fields.');
                return;
            }
            window.location.href = `/unduh-pdf?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const downloadBtn = document.getElementById('download-pdf');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', downloadPDF);
            }
        });
    </script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
            const currentDateEl = document.getElementById('current-date');
            const currentTimeEl = document.getElementById('current-time');

            if (currentDateEl) currentDateEl.innerText = now.toLocaleDateString('id-ID', optionsDate);
            if (currentTimeEl) currentTimeEl.innerText = now.toLocaleTimeString('id-ID', optionsTime);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
    <script>
        function saveFilters() {
            const commodityEl = document.getElementById('commodity');
            const startDateEl = document.getElementById('start-date');
            const endDateEl = document.getElementById('end-date');

            if (!commodityEl || !startDateEl || !endDateEl) return;

            const filterData = {
                commodity: commodityEl.value,
                startDate: startDateEl.value,
                endDate: endDateEl.value,
            };
            localStorage.setItem('dashboardFilters', JSON.stringify(filterData));
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedFilters = JSON.parse(localStorage.getItem('dashboardFilters'));
            if (savedFilters) {
                const commodityEl = document.getElementById('commodity');
                const startDateEl = document.getElementById('start-date');
                const endDateEl = document.getElementById('end-date');

                if (commodityEl) commodityEl.value = savedFilters.commodity || '';
                if (startDateEl) startDateEl.value = savedFilters.startDate || '';
                if (endDateEl) endDateEl.value = savedFilters.endDate || '';

                loadFilteredData(savedFilters.commodity, savedFilters.startDate, savedFilters.endDate);
            }
        });

        ['commodity', 'start-date', 'end-date'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', saveFilters);
            }
        });

        function loadFilteredData(commodity, startDate, endDate) {
            if (!commodity || !startDate || !endDate) return;
            fetch(`/api/filter?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                .then(res => res.json())
                .then(data => {
                    console.log('Filtered data loaded:', data);
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

            function loadCommodities() {
                fetch('/api/komoditas')
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('commodity');
                        if (!select) return;
                        select.innerHTML = ''; 
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.nama_komoditas;
                            opt.textContent = item.nama_komoditas;
                            select.appendChild(opt);
                        });

                        const saved = JSON.parse(localStorage.getItem('dashboardFilters'));
                        if (saved) {
                            select.value = saved.commodity || '';
                            const startDateEl = document.getElementById('start-date');
                            const endDateEl = document.getElementById('end-date');
                            if (startDateEl) startDateEl.value = saved.startDate || '';
                            if (endDateEl) endDateEl.value = saved.endDate || '';

                            loadChartData();
                            loadChartDataAll();
                            loadStatistics();
                            loadTotalData();
                        }
                    });
            }

            function formatDate(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-'); 
                return `${parts[2]} - ${parts[1]} - ${parts[0]}`;
            }

            function loadStatistics() {
                const commodityEl = document.getElementById('commodity');
                const startDateEl = document.getElementById('start-date');
                const endDateEl = document.getElementById('end-date');

                if (!commodityEl || !startDateEl || !endDateEl) return;

                const commodity = commodityEl.value;
                const startDate = startDateEl.value;
                const endDate = endDateEl.value;

                if (!commodity || !startDate || !endDate) return;

                fetch(`/api/filter?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                    .then(res => res.json())
                    .then(data => {
                        const statusAvgClass = data.statusAverage === 'Intervensi' ? 'text-intervensi' :
                            data.statusAverage === 'Waspada' ? 'text-waspada' : 'text-aman';
                        const statusLatestClass = data.statusLatest === 'Intervensi' ? 'text-intervensi' :
                            data.statusLatest === 'Waspada' ? 'text-waspada' : 'text-aman';

                        const c1 = document.getElementById('card1-body');
                        const c2 = document.getElementById('card2-body');
                        const c3 = document.getElementById('card3-body');
                        const c4 = document.getElementById('card4-body');

                        if (c1) c1.innerHTML = `<p>Harga Rata-rata Periode :  Rp.${data.averagePrice}</p><p>Status Harga Rata-rata Periode :  <span class="${statusAvgClass}">${data.statusAverage}</span></p>`;
                        if (c2) c2.innerHTML = `<p>Harga Akhir Periode :  Rp.${data.latestPrice}</p><p>Tanggal Harga Akhir Periode :  ${formatDate(data.latestDate)}</p><p>Status Harga Akhir Periode :  <span class="${statusLatestClass}">${data.statusLatest}</span></p>`;
                        if (c3) c3.innerHTML = `<p>Harga Tertinggi :  Rp.${data.highestPrice}</p><p>Tanggal Harga Tertinggi :  ${formatDate(data.highestDate)}</p><p>Harga Terendah :  Rp.${data.lowestPrice}</p><p>Tanggal Harga Terendah :  ${formatDate(data.lowestDate)}</p>`;
                        if (c4) c4.innerHTML = `<p>Jumlah Data :  ${data.dataCount}</p><p>Status CV :  ${data.statusCV}</p>`;

                        document.querySelectorAll('.card').forEach(card => {
                            setTimeout(() => { card.classList.add('show'); }, 100);
                        });
                    })
                    .catch(err => console.error('Gagal load statistik:', err));
            }

            function loadTotalData() {
                const commodityEl = document.getElementById('commodity');
                if (!commodityEl) return;
                const commodity = commodityEl.value;

                fetch(`/api/loadTotalData?commodity=${commodity}`)
                    .then(res => res.json())
                    .then(data => {
                        const statusAvgClass = data.statusAverage === 'Intervensi' ? 'text-intervensi' :
                            data.statusAverage === 'Waspada' ? 'text-waspada' : 'text-aman';
                        const statusLatestClass = data.statusLatest === 'Intervensi' ? 'text-intervensi' :
                            data.statusLatest === 'Waspada' ? 'text-waspada' : 'text-aman';

                        const cr1 = document.getElementById('card-right-1');
                        const cr2 = document.getElementById('card-right-2');
                        const cr3 = document.getElementById('card-right-3');
                        const cr4 = document.getElementById('card-right-4');

                        if (cr1) cr1.innerHTML = `<p>Harga Rata-rata :  Rp.${data.totalAveragePrice}</p><p>Status Rata-rata :  <span class="${statusAvgClass}">${data.statusAverage}</span></p>`;
                        if (cr2) cr2.innerHTML = `<p>Harga Terkini :  Rp.${data.latestPrice}</p><p>Tanggal Harga Terkini :  ${formatDate(data.latestDate)}</p><p>Status Harga Terkini :  <span class="${statusLatestClass}">${data.statusLatest}</span></p>`;
                        if (cr3) cr3.innerHTML = `<p>Harga Tertinggi :  Rp.${data.totalHighestPrice}</p><p>Tanggal Harga Tertinggi :  ${formatDate(data.totalHighestDate)}</p><p>Harga Terendah :  Rp.${data.totalLowestPrice}</p><p>Tanggal Harga Terendah :  ${formatDate(data.totalLowestDate)}</p>`;
                        if (cr4) cr4.innerHTML = `<p>Jumlah Data :  ${data.totalDataCount}</p><p>Status CV :  ${data.statusCV}</p>`;

                        document.querySelectorAll('.card').forEach(card => {
                            setTimeout(() => { card.classList.add('show'); }, 100);
                        });
                    })
                    .catch(err => console.error('Gagal load total data:', err));
            }

            function initializeChart() {
                const canvas = document.getElementById('myChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
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
                                pointRadius: 0.3,
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
                                pointRadius: 0.3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } },
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    parser: 'yyyy-MM-dd',
                                    tooltipFormat: 'll',
                                    unit: 'day',
                                    displayFormats: { day: 'yyyy-MM-dd' }
                                },
                                title: { display: true, text: 'Tanggal' }
                            },
                            y: {
                                title: { display: true, text: 'Harga (Rp)' },
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) { return value.toLocaleString('id-ID'); }
                                }
                            }
                        }
                    }
                });
            }

            function loadChartData() {
                const commodityEl = document.getElementById('commodity');
                const startDateEl = document.getElementById('start-date');
                const endDateEl = document.getElementById('end-date');

                if (!commodityEl || !startDateEl || !endDateEl) return;
                const commodity = commodityEl.value;
                const startDate = startDateEl.value;
                const endDate = endDateEl.value;

                if (!commodity || !startDate || !endDate || !myChart) return;

                fetch(`/api/grafik?commodity=${commodity}&start_date=${startDate}&end_date=${endDate}`)
                    .then(res => res.json())
                    .then(data => {
                        const harga = data.map(item => ({ x: item.date, y: item.price }));
                        const het = data.map(item => ({ x: item.date, y: item.hethap }));

                        myChart.data.labels = [];
                        myChart.data.datasets[0].data = harga;
                        myChart.data.datasets[1].data = het;
                        myChart.update();

                        if (data.length >= 7) {
                            const last7Days = data.slice(-7); 
                            const allIntervensi = last7Days.every(item => parseFloat(item.price) >= parseFloat(item.harga_intervensi));

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
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                if (myChartAll) { myChartAll.destroy(); }

                myChartAll = new Chart(ctx, {
                    type: 'line',
                    data: { labels: labels, datasets: datasets },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } },
                        scales: {
                            x: { title: { display: true, text: 'Tanggal' } },
                            y: {
                                grace: '10%',
                                ticks: { callback: function(value) { return value.toLocaleString('id-ID'); } }
                            }
                        }
                    }
                });
            }

            function loadChartDataAll() {
                const startDateEl = document.getElementById('start-date');
                const endDateEl = document.getElementById('end-date');

                if (!startDateEl || !endDateEl) return;
                const startDate = startDateEl.value;
                const endDate = endDateEl.value;

                if (!startDate || !endDate) return;

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
                                pointRadius: 0.3,
                                pointHoverRadius: 5
                            });
                            i++;
                        }
                        initializeChartAll(allLabels, datasets);
                    })
                    .catch(err => console.error('Gagal load chart all:', err));
            }

            initializeChart();
            initializeChartAll();
            loadCommodities();

            const filterBtn = document.getElementById('filter-button');
            if (filterBtn) {
                filterBtn.addEventListener('click', () => {
                    saveFilters();
                    loadStatistics();
                    loadTotalData();
                    loadChartData();
                    loadChartDataAll();
                });
            }

            ['commodity', 'start-date', 'end-date'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => {
                        saveFilters();
                        const startDateEl = document.getElementById('start-date');
                        const endDateEl = document.getElementById('end-date');
                        if (!startDateEl || !endDateEl) return;
                        if (startDateEl.value && endDateEl.value) {
                            loadChartData();
                            loadChartDataAll();
                            loadStatistics();
                            loadTotalData();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>