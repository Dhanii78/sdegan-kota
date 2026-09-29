<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

<title>Dashboard Operator</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('js/mobile-nav.js') }}"></script>

<style>

    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        overflow-x: hidden;
        font-family: 'Poppins', sans-serif;
    }

    /* =====================================================
       MOBILE RESPONSIVE TAMBAHAN
    ===================================================== */

    .responsive-wrapper {
        width: 100%;
        max-width: 100%;
        padding: 20px;
    }

    .mobile-card-grid {
        width: 100%;
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        flex: 1;
        min-height: 0;
    }

    .chart-wrapper canvas {
        width: 100% !important;
        max-width: 100%;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
    }

    .table-responsive table {
        min-width: 1000px;
        width: 100%;
        border-collapse: collapse;
    }

    .table-responsive th,
    .table-responsive td {
        white-space: nowrap;
    }


    /* =====================================================
       FILTER
    ===================================================== */

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .filter-bar label {
        font-weight: 500;
    }

    .filter-bar select,
    .filter-bar input {
        min-height: 42px;
    }


    /* =====================================================
       DOWNLOAD
    ===================================================== */

    .download-container {
        width: 100%;
    }

    #download-pdf {
        min-height: 44px;
    }


    /* =====================================================
       MODAL
    ===================================================== */

    .modal {
        padding: 15px;
    }

    .modal-content {
        width: min(500px, 100%);
        max-width: 100%;
    }


    /* =====================================================
       MOBILE
    ===================================================== */

    @media (max-width: 768px) {

        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .navbar {
            width: 100%;
            min-height: 70px;
            padding: 10px 15px;
        }

        .logo {
            max-width: calc(100% - 50px);
            overflow: hidden;
        }

        .logo img {
            max-width: 42px;
            height: auto;
        }

        .logo-text {
            font-size: 11px;
            line-height: 1.3;
        }

        .toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            min-height: 42px;
        }

        .menu {
            width: 100%;
        }

        .menu a {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            padding: 10px 15px;
        }

        .datetime-op {
            width: 100%;
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
        }


        /* FILTER */

        .filter-bar {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 15px;
        }

        .filter-bar label {
            margin-top: 5px;
        }

        .filter-bar select,
        .filter-bar input,
        .filter-bar button {
            width: 100%;
            min-height: 44px;
        }


        /* CHART */

        .chart-row {
            width: 100%;
            padding: 10px;
        }

        .container-stat,
        .container-statall {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .chart-wrapper {
            min-height: 160px;
        }


        /* CONTENT */

        .content-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 10px;
        }

        .container-kiri,
        .container-kanan {
            width: 100% !important;
            max-width: 100% !important;
        }


        /* CARDS */

        .cards {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr !important;
            gap: 12px;
        }

        .card {
            width: 100%;
            min-width: 0;
        }

        .card h3 {
            font-size: 15px;
        }

        .card-body {
            font-size: 13px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }


        /* DOWNLOAD */

        .download-container {
            width: 100%;
            padding: 0 10px 10px;
        }

        #download-pdf {
            width: 100%;
            min-height: 46px;
        }


        /* SPHP */

        .container-besar {
            width: 100% !important;
            max-width: 100% !important;
            padding: 10px;
            overflow: hidden;
        }

        .container-besar > .filter-bar {
            padding: 10px 0;
        }

        .container-besar > .filter-bar form {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .container-besar > .filter-bar form label,
        .container-besar > .filter-bar form input,
        .container-besar > .filter-bar form button {
            width: 100%;
        }


        /* TABLE */

        .table-responsive {
            margin-top: 10px;
            width: 100%;
            overflow-x: auto;
        }

        .table-responsive table {
            min-width: 1050px;
        }

        .table-responsive th,
        .table-responsive td {
            padding: 8px 10px;
            font-size: 12px;
        }


        /* MODAL */

        .modal-content {
            margin: 15% auto;
            width: 100%;
            max-width: 95%;
            padding: 20px;
        }

        .profile-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .profile-info,
        .profile-logo {
            width: 100%;
        }

        .profile-logo img {
            max-width: 120px;
        }

    }


    /* =====================================================
       EXTRA SMALL MOBILE
    ===================================================== */

    @media (max-width: 480px) {

        .navbar {
            padding: 8px 10px;
        }

        .logo img {
            max-width: 36px;
        }

        .logo-text {
            font-size: 9px;
        }

        .filter-bar {
            padding: 10px;
        }

        .chart-row {
            padding: 5px;
        }

        .content-container {
            padding: 5px;
        }

        .container-besar {
            padding: 5px;
        }

        .logo-title {
            font-size: 14px !important;
        }

        .card {
            padding: 12px !important;
        }

        .card h3 {
            font-size: 14px;
        }

        .card-body {
            font-size: 12px;
        }

        .modal-content {
            padding: 15px;
        }

    }

</style>
```

</head>

<body class="role-portal">
    @include('components.role-mobile-chrome', ['role' => 'operator', 'active' => 'dashboard'])

<div class="container">

```
<!-- =====================================================
     NAVBAR
====================================================== -->

<div class="navbar">

    <div class="logo">

        <img src="{{ asset('images/logo.png') }}"
             alt="Logo">

        <div>
            <p class="logo-text">
                S-DEGAN
            </p>

            <p class="logo-text">
                Sistem Deteksi Dini Gejolak Harga Pangan
            </p>
        </div>

    </div>


    <button class="toggle-btn"
            type="button"
            aria-label="Menu">

        ☰

    </button>


    <div class="menu">

        <a href="{{ route('dashboard_operator.index') }}">
            <i class="fas fa-home"></i>
            Dashboard
        </a>

        <a href="{{ route('dashboard_operator.data_pangan') }}">
            <i class="fas fa-keyboard"></i>
            Data Pangan
        </a>

        <a href="{{ route('rekap.operator') }}">
            <i class="fas fa-chart-line"></i>
            Rekap Data
        </a>

        <a href="javascript:void(0)"
           id="profile-link">

            <i class="fas fa-user-circle"></i>
            Profil

        </a>

    </div>


    <div class="datetime-op">

        <span id="current-time"></span>

        <hr>

        <span id="current-date"></span>

    </div>

</div>



<!-- =====================================================
     FILTER GRAFIK
====================================================== -->

<div class="filter-bar">

    <label for="commodity">
        Komoditas:
    </label>

    <select id="commodity">
    </select>


    <label for="start-date">
        Tanggal Mulai:
    </label>

    <input type="date"
           id="start-date">


    <label for="end-date">
        Tanggal Akhir:
    </label>

    <input type="date"
           id="end-date">


    <button id="filter-button"
            type="button">

        <i class="fa-solid fa-filter"></i>
        Filter

    </button>

</div>



<!-- =====================================================
     CHART
====================================================== -->

<div class="chart-row">

    <div class="container-stat">

        <p class="logo-title">
            Grafik harga komoditas dan periode pilih
        </p>

        <div class="chart-wrapper">

            <canvas id="myChart"></canvas>

        </div>

    </div>

</div>



<!-- =====================================================
     REKAP
====================================================== -->

<div class="content-container">


    <!-- PERIODE PILIH -->

    <div class="container-kiri">

        <p class="logo-title">
            Rekapitulasi Harga Periode Pilih
        </p>


        <div class="cards">


            <div class="card">

                <h3>
                    Harga Rata-rata
                </h3>

                <div class="card-body"
                     id="card1-body">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Harga Akhir Periode
                </h3>

                <div class="card-body"
                     id="card2-body">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Rekor Harga
                </h3>

                <div class="card-body"
                     id="card3-body">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Keterangan
                </h3>

                <div class="card-body"
                     id="card4-body">

                    Data akan dimuat...

                </div>

            </div>

        </div>

    </div>



    <!-- KESELURUHAN PERIODE -->

    <div class="container-kanan">

        <p class="logo-title">
            Rekapitulasi Harga Keseluruhan Periode
        </p>


        <div class="cards">


            <div class="card">

                <h3>
                    Harga Rata-rata
                </h3>

                <div class="card-body"
                     id="card-right-1">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Harga Terkini
                </h3>

                <div class="card-body"
                     id="card-right-2">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Rekor Harga
                </h3>

                <div class="card-body"
                     id="card-right-3">

                    Data akan dimuat...

                </div>

            </div>


            <div class="card">

                <h3>
                    Keterangan
                </h3>

                <div class="card-body"
                     id="card-right-4">

                    Data akan dimuat...

                </div>

            </div>

        </div>

    </div>


    <!-- DOWNLOAD PDF -->

    <div class="download-container">

        <button id="download-pdf"
                type="button"
                onclick="downloadPDF()">

            <i class="fa-solid fa-file-pdf"></i>

            Unduh PDF

        </button>

    </div>

</div>



<!-- =====================================================
     INDIKATOR SPHP
====================================================== -->

<div class="container-besar">

    <p class="logo-title">
        INDIKATOR SPHP
    </p>


    <!-- FILTER TANGGAL -->

    <div class="filter-bar">

        <form method="GET"
              action="{{ route('dashboard_operator.index') }}">

            <label for="tanggal">
                Pilih Tanggal:
            </label>

            <input type="date"
                   id="tanggal"
                   name="tanggal"
                   value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}">


            <button type="submit">

                <i class="fa-solid fa-filter"></i>

                Filter

            </button>

        </form>

    </div>



    <!-- TABLE RESPONSIVE -->

    <div class="table-responsive">

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

                        Harga {{ $filterTanggal->copy()->subDay()->format('d/m/Y') }}

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


                <th>
                    Indikator Rata-rata
                </th>

            </tr>

            </thead>


            <tbody>

            @foreach($sumber_data as $data)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>


                    <td class="text-left">
                        {{ $data->nama_komoditas }}
                    </td>


                    <td>
                        {{ $data->satuan }}
                    </td>


                    <td>
                        @php
                            $hetValue = isset($hetData) ? ($hetData->{$data->nama_komoditas} ?? null) : null;
                            $displayHet = is_numeric($hetValue) ? $hetValue : $data->hethap;
                        @endphp
                        {{ number_format($displayHet, 0, ',', '.') }}
                    </td>


                    <!-- HARGA HARI INI -->

                    <td>

                        @php

                            $latest = $latestRekap->{$data->nama_komoditas} ?? null;

                            $previous = $previousRekap->{$data->nama_komoditas} ?? null;

                        @endphp


                        {{ is_numeric($latest) ? number_format($latest, 0, ',', '.') : '-' }}


                        @if(is_numeric($latest) && is_numeric($previous))

                            @if($latest > $previous)

                                <span class="text-naik">

                                    <i class="fa fa-long-arrow-up"></i>

                                </span>

                            @elseif($latest < $previous)

                                <span class="text-turun">

                                    <i class="fa fa-long-arrow-down"></i>

                                </span>

                            @else

                                <span class="text-tetap">
                                    −
                                </span>

                            @endif

                        @endif

                    </td>


                    <!-- HARGA KEMARIN -->

                    <td>

                        {{ is_numeric($previous) ? number_format($previous, 0, ',', '.') : '-' }}

                    </td>


                    <!-- RATA-RATA 7 HARI -->

                    <td>

                        @php

                            $avg = $sevenDayAverage[$data->nama_komoditas] ?? null;

                        @endphp


                        {{ is_numeric($avg) ? number_format($avg, 0, ',', '.') : '-' }}

                    </td>


                    <!-- INDIKATOR HARI INI -->

                    <td>

                        @php

                            $status = $indicators[$data->nama_komoditas] ?? null;

                        @endphp


                        @if($status == 'Aman')

                            <span class="text-aman">
                                Aman
                            </span>

                        @elseif($status == 'Waspada')

                            <span class="text-waspada">
                                Waspada
                            </span>

                        @elseif($status == 'Intervensi')

                            <span class="text-intervensi">
                                Intervensi
                            </span>

                        @else

                            <span class="text-muted">
                                -
                            </span>

                        @endif

                    </td>


                    <!-- INDIKATOR RATA-RATA -->

                    <td>

                        @php

                            $statusAvg = $averageIndicators[$data->nama_komoditas] ?? null;

                        @endphp


                        @if($statusAvg == 'Aman')

                            <span class="text-aman">
                                Aman
                            </span>

                        @elseif($statusAvg == 'Waspada')

                            <span class="text-waspada">
                                Waspada
                            </span>

                        @elseif($statusAvg == 'Intervensi')

                            <span class="text-intervensi">
                                Intervensi
                            </span>

                        @else

                            <span class="text-muted">
                                -
                            </span>

                        @endif

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>
```

</div>

<!-- =====================================================
     PROFILE MODAL
====================================================== -->

<div id="profileModal"
     class="modal">

<div class="modal-content">

    <span class="close">
        &times;
    </span>


    <h2>
        Profil User
    </h2>


    <div class="profile-details">

        <div class="profile-info">

            <p>
                Nama:
                {{ Auth::user()->nama }}
            </p>

            <p>
                Role:
                {{ Auth::user()->role }}
            </p>

            <p>
                Email:
                {{ Auth::user()->email }}
            </p>


            <a href="#"
               class="btn-logout"
               onclick="handleLogout(event)">

                Logout

            </a>

        </div>


        <div class="profile-logo">

            <img src="{{ asset('images/profileLogo.png') }}"
                 alt="Profile Logo">

        </div>

    </div>

</div>

</div>

<!-- =====================================================
     SIDEBAR TOGGLE
====================================================== -->

<script>

    const toggleBtn = document.querySelector('.toggle-btn');

    const navbar = document.querySelector('.navbar');

    const container = document.querySelector('.container');

    const layoutContainer = document.querySelector(".layout-container");


    if (toggleBtn) {

        toggleBtn.addEventListener('click', () => {

            navbar.classList.toggle('hidden');

            container.classList.toggle('sidebar-hidden');

            toggleBtn.classList.toggle('hide');

            container.classList.toggle("shifted");

            if (layoutContainer) {

                layoutContainer.classList.toggle("shifted");

            }

        });

    }

</script>

<!-- =====================================================
     PROFILE MODAL SCRIPT
====================================================== -->

<script>

    const modal = document.getElementById("profileModal");

    const btn = document.getElementById("profile-link");

    const span = document.querySelector(".close");


    if (btn) {

        btn.onclick = function () {

            modal.style.display = "block";

            setTimeout(function () {

                modal.classList.add("show");

                modal.classList.remove("hide");

            }, 10);

        };

    }


    if (span) {

        span.onclick = function () {

            modal.classList.remove("show");

            modal.classList.add("hide");

            setTimeout(function () {

                modal.style.display = "none";

            }, 300);

        };

    }


    window.onclick = function (event) {

        if (event.target === modal) {

            modal.classList.remove("show");

            modal.classList.add("hide");

            setTimeout(function () {

                modal.style.display = "none";

            }, 300);

        }

    };

</script>

<!-- =====================================================
     DOWNLOAD PDF
====================================================== -->

<script>

    function downloadPDF() {

        const commodity =
            document.getElementById('commodity').value;

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        if (!commodity || !startDate || !endDate) {

            alert('Silakan isi komoditas, tanggal mulai, dan tanggal akhir.');

            return;

        }


        window.location.href =
            `/operator/unduh-pdf?commodity=${encodeURIComponent(commodity)}&start_date=${startDate}&end_date=${endDate}`;

    }


    document.addEventListener('DOMContentLoaded', () => {

        const button =
            document.getElementById('download-pdf');

        if (button) {

            button.addEventListener('click', downloadPDF);

        }

    });

</script>

<!-- =====================================================
     DATE & TIME
====================================================== -->

<script>

    function updateDateTime() {

        const now = new Date();


        const optionsDate = {

            weekday: 'long',

            year: 'numeric',

            month: 'long',

            day: 'numeric'

        };


        const optionsTime = {

            hour: '2-digit',

            minute: '2-digit',

            second: '2-digit'

        };


        const dateElement =
            document.getElementById('current-date');

        const timeElement =
            document.getElementById('current-time');


        if (dateElement) {

            dateElement.innerText =
                now.toLocaleDateString('id-ID', optionsDate);

        }


        if (timeElement) {

            timeElement.innerText =
                now.toLocaleTimeString('id-ID', optionsTime);

        }

    }


    setInterval(updateDateTime, 1000);

    updateDateTime();

</script>

<!-- =====================================================
     FILTER LOCAL STORAGE
====================================================== -->

<script>

    function saveFilters() {

        const commodity =
            document.getElementById('commodity').value;

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        const filterData = {

            commodity: commodity,

            startDate: startDate,

            endDate: endDate

        };


        localStorage.setItem(
            'dashboardFilters',
            JSON.stringify(filterData)
        );

    }


    function loadFilteredData(
        commodity,
        startDate,
        endDate
    ) {

        if (!commodity || !startDate || !endDate) {

            return;

        }


        fetch(
            `/api/filter?commodity=${encodeURIComponent(commodity)}&start_date=${startDate}&end_date=${endDate}`
        )

        .then(res => res.json())

        .then(data => {

            console.log(
                'Filtered data loaded:',
                data
            );

        })

        .catch(err => {

            console.error(
                'Gagal load filtered data:',
                err
            );

        });

    }

</script>

<!-- =====================================================
     MAIN DASHBOARD JAVASCRIPT
====================================================== -->

<script>

document.addEventListener('DOMContentLoaded', () => {

    let myChart = null;

    let myChartAll = null;


    const warnaDasar = [

        '#e6194b',
        '#3cb44b',
        '#ffe119',
        '#4363d8',
        '#f58231',
        '#911eb4',
        '#46f0f0',
        '#f032e6',
        '#bcf60c',
        '#fabebe',
        '#008080',
        '#e6beff',
        '#9a6324',
        '#fffac8',
        '#800000',
        '#aaffc3',
        '#808000',
        '#ffd8b1',
        '#000075',
        '#808080'

    ];


    /* =====================================================
       LOAD COMMODITIES
    ====================================================== */

    function loadCommodities() {

        fetch('/api/komoditas')

        .then(response => response.json())

        .then(data => {

            const select =
                document.getElementById('commodity');


            if (!select) return;


            select.innerHTML = '';


            data.forEach(item => {

                const opt =
                    document.createElement('option');


                opt.value =
                    item.nama_komoditas;


                opt.textContent =
                    item.nama_komoditas;


                select.appendChild(opt);

            });


            const saved =
                JSON.parse(
                    localStorage.getItem('dashboardFilters')
                );


            if (saved) {

                select.value =
                    saved.commodity || '';


                document.getElementById('start-date').value =
                    saved.startDate || '';


                document.getElementById('end-date').value =
                    saved.endDate || '';


                if (
                    saved.startDate &&
                    saved.endDate
                ) {

                    loadChartData();

                    loadChartDataAll();

                    loadStatistics();

                    loadTotalData();

                }

            }

        })

        .catch(error => {

            console.error(
                'Gagal memuat komoditas:',
                error
            );

        });

    }



    /* =====================================================
       SAVE FILTER
    ====================================================== */

    function saveDashboardFilters() {

        const data = {

            commodity:
                document.getElementById('commodity').value,

            startDate:
                document.getElementById('start-date').value,

            endDate:
                document.getElementById('end-date').value

        };


        localStorage.setItem(
            'dashboardFilters',
            JSON.stringify(data)
        );

    }



    /* =====================================================
       FORMAT DATE
    ====================================================== */

    function formatDate(dateStr) {

        if (!dateStr) return '-';


        const parts =
            dateStr.split('-');


        if (parts.length !== 3) {

            return dateStr;

        }


        return `${parts[2]} - ${parts[1]} - ${parts[0]}`;

    }



    /* =====================================================
       STATISTICS
    ====================================================== */

    function loadStatistics() {

        const commodity =
            document.getElementById('commodity').value;

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        if (!commodity || !startDate || !endDate) {

            return;

        }


        fetch(
            `/api/filter?commodity=${encodeURIComponent(commodity)}&start_date=${startDate}&end_date=${endDate}`
        )

        .then(res => res.json())

        .then(data => {


            const statusAvgClass =
                data.statusAverage === 'Intervensi'
                    ? 'text-intervensi'
                    : data.statusAverage === 'Waspada'
                        ? 'text-waspada'
                        : 'text-aman';


            const statusLatestClass =
                data.statusLatest === 'Intervensi'
                    ? 'text-intervensi'
                    : data.statusLatest === 'Waspada'
                        ? 'text-waspada'
                        : 'text-aman';


            document.getElementById('card1-body').innerHTML = `

                <p>
                    Harga Rata-rata Periode:
                    Rp.${data.averagePrice}
                </p>

                <p>
                    Status Harga Rata-rata:
                    <span class="${statusAvgClass}">
                        ${data.statusAverage}
                    </span>
                </p>

            `;


            document.getElementById('card2-body').innerHTML = `

                <p>
                    Harga Akhir Periode:
                    Rp.${data.latestPrice}
                </p>

                <p>
                    Tanggal Harga Akhir:
                    ${formatDate(data.latestDate)}
                </p>

                <p>
                    Status:
                    <span class="${statusLatestClass}">
                        ${data.statusLatest}
                    </span>
                </p>

            `;


            document.getElementById('card3-body').innerHTML = `

                <p>
                    Harga Tertinggi:
                    Rp.${data.highestPrice}
                </p>

                <p>
                    Tanggal Harga Tertinggi:
                    ${formatDate(data.highestDate)}
                </p>

                <p>
                    Harga Terendah:
                    Rp.${data.lowestPrice}
                </p>

                <p>
                    Tanggal Harga Terendah:
                    ${formatDate(data.lowestDate)}
                </p>

            `;


            document.getElementById('card4-body').innerHTML = `

                <p>
                    Jumlah Data:
                    ${data.dataCount}
                </p>

                <p>
                    Status CV:
                    ${data.statusCV}
                </p>

            `;


            document.querySelectorAll('.card')
                .forEach(card => {

                    setTimeout(() => {

                        card.classList.add('show');

                    }, 100);

                });

        })

        .catch(err => {

            console.error(
                'Gagal load statistik:',
                err
            );

        });

    }



    /* =====================================================
       TOTAL DATA
    ====================================================== */

    function loadTotalData() {

        const commodity =
            document.getElementById('commodity').value;


        if (!commodity) return;


        fetch(
            `/api/loadTotalData?commodity=${encodeURIComponent(commodity)}`
        )

        .then(res => res.json())

        .then(data => {


            const statusAvgClass =
                data.statusAverage === 'Intervensi'
                    ? 'text-intervensi'
                    : data.statusAverage === 'Waspada'
                        ? 'text-waspada'
                        : 'text-aman';


            const statusLatestClass =
                data.statusLatest === 'Intervensi'
                    ? 'text-intervensi'
                    : data.statusLatest === 'Waspada'
                        ? 'text-waspada'
                        : 'text-aman';


            document.getElementById('card-right-1').innerHTML = `

                <p>
                    Harga Rata-rata:
                    Rp.${data.totalAveragePrice}
                </p>

                <p>
                    Status Rata-rata:
                    <span class="${statusAvgClass}">
                        ${data.statusAverage}
                    </span>
                </p>

            `;


            document.getElementById('card-right-2').innerHTML = `

                <p>
                    Harga Terkini:
                    Rp.${data.latestPrice}
                </p>

                <p>
                    Tanggal Harga Terkini:
                    ${formatDate(data.latestDate)}
                </p>

                <p>
                    Status Harga Terkini:
                    <span class="${statusLatestClass}">
                        ${data.statusLatest}
                    </span>
                </p>

            `;


            document.getElementById('card-right-3').innerHTML = `

                <p>
                    Harga Tertinggi:
                    Rp.${data.totalHighestPrice}
                </p>

                <p>
                    Tanggal Harga Tertinggi:
                    ${formatDate(data.totalHighestDate)}
                </p>

                <p>
                    Harga Terendah:
                    Rp.${data.totalLowestPrice}
                </p>

                <p>
                    Tanggal Harga Terendah:
                    ${formatDate(data.totalLowestDate)}
                </p>

            `;


            document.getElementById('card-right-4').innerHTML = `

                <p>
                    Jumlah Data:
                    ${data.totalDataCount}
                </p>

                <p>
                    Status CV:
                    ${data.statusCV}
                </p>

            `;


            document.querySelectorAll('.card')
                .forEach(card => {

                    setTimeout(() => {

                        card.classList.add('show');

                    }, 100);

                });

        })

        .catch(err => {

            console.error(
                'Gagal load statistik total:',
                err
            );

        });

    }



    /* =====================================================
       INITIALIZE CHART
    ====================================================== */

    function initializeChart() {

        const canvas =
            document.getElementById('myChart');


        if (!canvas) return;


        const ctx =
            canvas.getContext('2d');


        myChart =
            new Chart(ctx, {

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

                    maintainAspectRatio: false,


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


                            beginAtZero: false,


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



    /* =====================================================
       LOAD CHART DATA
    ====================================================== */

    function loadChartData() {

        const commodity =
            document.getElementById('commodity').value;

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        if (
            !commodity ||
            !startDate ||
            !endDate ||
            !myChart
        ) {

            return;

        }


        fetch(
            `/api/grafik?commodity=${encodeURIComponent(commodity)}&start_date=${startDate}&end_date=${endDate}`
        )

        .then(res => res.json())

        .then(data => {


            const harga =
                data.map(item => ({

                    x: item.date,

                    y: item.price

                }));


            const het =
                data.map(item => ({

                    x: item.date,

                    y: item.hethap

                }));


            myChart.data.labels = [];

            myChart.data.datasets[0].data =
                harga;

            myChart.data.datasets[1].data =
                het;


            myChart.update();


            if (data.length >= 7) {

                const last7Days =
                    data.slice(-7);


                const allIntervensi =
                    last7Days.every(item =>

                        parseFloat(item.price) >=
                        parseFloat(item.harga_intervensi)

                    );


                if (allIntervensi) {

                    Swal.fire({

                        icon: 'warning',

                        title: `⚠️ Komoditas ${commodity}`,

                        text:
                            'Harga sudah 7 hari berturut-turut berstatus INTERVENSI!',

                        timer: 5000,

                        timerProgressBar: true,

                        showConfirmButton: false

                    });

                }

            }

        })

        .catch(err => {

            console.error(
                'Gagal load chart:',
                err
            );

        });

    }



    /* =====================================================
       INITIALIZE ALL CHART
    ====================================================== */

    function initializeChartAll(
        labels = [],
        datasets = []
    ) {

        const canvas =
            document.getElementById('chartCanvas');


        if (!canvas) {

            return;

        }


        const ctx =
            canvas.getContext('2d');


        if (myChartAll) {

            myChartAll.destroy();

        }


        myChartAll =
            new Chart(ctx, {

                type: 'line',


                data: {

                    labels: labels,

                    datasets: datasets

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,


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



    /* =====================================================
       LOAD ALL CHART
    ====================================================== */

    function loadChartDataAll() {

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        if (!startDate || !endDate) {

            return;

        }


        fetch(
            `/api/grafikAll?start_date=${startDate}&end_date=${endDate}`
        )

        .then(res => res.json())

        .then(data => {


            const allLabels = [];

            const datasets = [];

            let i = 0;


            for (
                const [komoditas, values]
                of Object.entries(data)
            ) {


                const labels =
                    values.map(v => v.date);


                const harga =
                    values.map(v => v.price);


                if (allLabels.length === 0) {

                    allLabels.push(...labels);

                }


                datasets.push({

                    label: komoditas,

                    data: harga,

                    borderColor:
                        warnaDasar[
                            i % warnaDasar.length
                        ],

                    backgroundColor:
                        warnaDasar[
                            i % warnaDasar.length
                        ],

                    borderWidth: 2,

                    tension: 0.3,

                    fill: false,

                    pointRadius: 3,

                    pointHoverRadius: 5

                });


                i++;

            }


            initializeChartAll(
                allLabels,
                datasets
            );

        })

        .catch(err => {

            console.error(
                'Gagal load chart all:',
                err
            );

        });

    }



    /* =====================================================
       INITIALIZATION
    ====================================================== */

    initializeChart();

    initializeChartAll();

    loadCommodities();



    /* =====================================================
       FILTER BUTTON
    ====================================================== */

    const filterButton =
        document.getElementById('filter-button');


    if (filterButton) {

        filterButton.addEventListener(
            'click',
            () => {

                saveDashboardFilters();

                loadStatistics();

                loadTotalData();

                loadChartData();

                loadChartDataAll();

            }
        );

    }



    /* =====================================================
       REALTIME FILTER
    ====================================================== */

    [
        'commodity',
        'start-date',
        'end-date'
    ].forEach(id => {


        const element =
            document.getElementById(id);


        if (!element) return;


        element.addEventListener(
            'change',
            () => {


                saveDashboardFilters();


                const startDate =
                    document.getElementById('start-date').value;


                const endDate =
                    document.getElementById('end-date').value;


                if (
                    startDate &&
                    endDate
                ) {

                    loadChartData();
    /* =====================================================
       LOAD ALL CHART
    ====================================================== */

    function loadChartDataAll() {

        const startDate =
            document.getElementById('start-date').value;

        const endDate =
            document.getElementById('end-date').value;


        if (!startDate || !endDate) {

            return;

        }


        fetch(
            `/api/grafikAll?start_date=${startDate}&end_date=${endDate}`
        )

        .then(res => res.json())

        .then(data => {


            const allLabels = [];

            const datasets = [];

            let i = 0;


            for (
                const [komoditas, values]
                of Object.entries(data)
            ) {


                const labels =
                    values.map(v => v.date);


                const harga =
                    values.map(v => v.price);


                if (allLabels.length === 0) {

                    allLabels.push(...labels);

                }


                datasets.push({

                    label: komoditas,

                    data: harga,

                    borderColor:
                        warnaDasar[
                            i % warnaDasar.length
                        ],

                    backgroundColor:
                        warnaDasar[
                            i % warnaDasar.length
                        ],

                    borderWidth: 2,

                    tension: 0.3,

                    fill: false,

                    pointRadius: 3,

                    pointHoverRadius: 5

                });


                i++;

            }


            initializeChartAll(
                allLabels,
                datasets
            );

        })

        .catch(err => {

            console.error(
                'Gagal load chart all:',
                err
            );

        });

    }



    /* =====================================================
       INITIALIZATION
    ====================================================== */

    initializeChart();

    initializeChartAll();

    loadCommodities();



    /* =====================================================
       FILTER BUTTON
    ====================================================== */

    const filterButton =
        document.getElementById('filter-button');


    if (filterButton) {

        filterButton.addEventListener(
            'click',
            () => {

                saveDashboardFilters();

                loadStatistics();

                loadTotalData();

                loadChartData();

                loadChartDataAll();

            }
        );

    }



    /* =====================================================
       REALTIME FILTER
    ====================================================== */

    [
        'commodity',
        'start-date',
        'end-date'
    ].forEach(id => {


        const element =
            document.getElementById(id);


        if (!element) return;


        element.addEventListener(
            'change',
            () => {


                saveDashboardFilters();


                const startDate =
                    document.getElementById('start-date').value;


                const endDate =
                    document.getElementById('end-date').value;


                if (
                    startDate &&
                    endDate
                ) {

                    loadChartData();

                    loadChartDataAll();

                    loadStatistics();

                    loadTotalData();

                }

            }
        );

    });

});

</script>

<!-- =====================================================
     LOGOUT FORM
====================================================== -->

<form id="logout-form"
      action="{{ route('logout') }}"
      method="POST"
      style="display: none;">
    @csrf
</form>

<script>
    function handleLogout(event) {
        if (event) {
            event.preventDefault();
        }
        localStorage.removeItem('dashboardFilters');
        const form = document.getElementById('logout-form');
        if (form) {
            form.submit();
        } else {
            window.location.href = "{{ route('logout') }}";
        }
    }
</script>

</body>

</html>
