<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

    <title>Dashboard Verifikator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- CSS UTAMA --}}
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">


    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@latest"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>


    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    >


    {{-- Poppins --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    {{-- jsPDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    {{-- Mobile Navigation --}}
    <script src="{{ asset('js/mobile-nav.js') }}"></script>

    <style>

        .verifikator-welcome {
            margin: 10px 0 20px 0;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #198754;
        }

        .verifikator-welcome h3 {
            margin: 0 0 5px 0;
        }

        .verifikator-welcome p {
            margin: 0;
        }

        .btn-dashboard {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
        }

        .text-aman {
            color: #198754;
            font-weight: 600;
        }

        .text-waspada {
            color: #ffc107;
            font-weight: 600;
        }

        .text-intervensi {
            color: #dc3545;
            font-weight: 600;
        }

        .text-naik {
            color: #dc3545;
            margin-left: 5px;
        }

        .text-turun {
            color: #198754;
            margin-left: 5px;
        }

        .text-tetap {
            color: #6c757d;
            margin-left: 5px;
        }

        .text-muted {
            color: #6c757d;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-responsive table {
            min-width: 900px;
        }

        .profile-details {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            align-items: center;
        }

        .profile-logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        @media (max-width: 768px) {

            .profile-details {
                flex-direction: column;
                text-align: center;
            }

            .cards {
                grid-template-columns: 1fr !important;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar select,
            .filter-bar input,
            .filter-bar button {
                width: 100%;
            }

        }

    </style>

</head>


<body class="role-portal">
    @include('components.role-mobile-chrome', ['role' => 'verifikator', 'active' => 'dashboard'])


<div class="container">


    {{-- ========================================================= --}}
    {{-- NAVBAR --}}
    {{-- ========================================================= --}}

    <div class="navbar">


        <div class="logo">

            <img
                src="{{ asset('images/logo.png') }}"
                alt="Logo"
            >

            <p class="logo-text">
                S-DEGAN
            </p>

            <p class="logo-text">
                Sistem Deteksi Dini Gejolak Harga Pangan
            </p>

        </div>


        <button
            type="button"
            class="toggle-btn"
        >
            ☰
        </button>


        <div class="menu">


            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard_verifikator.index') }}">

                <i class="fas fa-home"></i>

                Dashboard

            </a>


            {{-- VERIFIKASI --}}
            <a href="{{ route('dashboard_verifikator.verify_data') }}">

                <i class="fas fa-check-circle"></i>

                Verifikasi Data

            </a>


            {{-- REKAP --}}
            <a href="{{ route('rekap.verifikator') }}">

                <i class="fas fa-chart-line"></i>

                Rekap Data

            </a>


            {{-- PROFIL --}}
            <a
                href="javascript:void(0)"
                id="profile-link"
            >

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



    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="header-text">

        <p>
            Dashboard Verifikator
        </p>

    </div>



    {{-- ========================================================= --}}
    {{-- WELCOME --}}
    {{-- ========================================================= --}}

    <div class="verifikator-welcome">

        <h3>

            Selamat Datang,

            {{ Auth::user()->nama ?? Auth::user()->name }}

        </h3>

        <p>

            Anda login sebagai

            <strong>
                Verifikator
            </strong>.

        </p>

    </div>



    {{-- ========================================================= --}}
    {{-- MENU UTAMA VERIFIKATOR --}}
    {{-- ========================================================= --}}

    <div class="content-container">


        <div class="container-kiri">


            <p class="logo-title">

                Menu Verifikator

            </p>


            <div class="cards">


                {{-- DATA PENDING --}}

                <div class="card">

                    <h3>

                        <i class="fas fa-hourglass-half"></i>

                        Data Pending

                    </h3>


                    <div class="card-body">

                        <p>

                            Data komoditas yang

                            belum diverifikasi

                            oleh verifikator.

                        </p>


                        <a
                            href="{{ route('dashboard_verifikator.verify_data') }}"
                            class="btn btn-primary btn-dashboard"
                        >

                            <i class="fas fa-search"></i>

                            Lihat Data

                        </a>

                    </div>

                </div>



                {{-- DATA REVISI --}}

                <div class="card">

                    <h3>

                        <i class="fas fa-exclamation-triangle"></i>

                        Data Revisi

                    </h3>


                    <div class="card-body">

                        <p>

                            Data yang dikembalikan

                            kepada operator karena

                            membutuhkan perbaikan.

                        </p>


                        <a
                            href="{{ route('dashboard_verifikator.verify_data') }}"
                            class="btn btn-primary btn-dashboard"
                        >

                            <i class="fas fa-edit"></i>

                            Lihat Revisi

                        </a>

                    </div>

                </div>



                {{-- DATA TERVERIFIKASI --}}

                <div class="card">

                    <h3>

                        <i class="fas fa-check-circle"></i>

                        Data Terverifikasi

                    </h3>


                    <div class="card-body">

                        <p>

                            Data yang sudah diperiksa

                            dan dinyatakan valid.

                        </p>


                        <a
                            href="{{ route('rekap.verifikator') }}"
                            class="btn btn-primary btn-dashboard"
                        >

                            <i class="fas fa-database"></i>

                            Lihat Rekap

                        </a>

                    </div>

                </div>



                {{-- PROFIL --}}

                <div class="card">

                    <h3>

                        <i class="fas fa-user"></i>

                        Profil

                    </h3>


                    <div class="card-body">

                        <p>

                            Kelola informasi akun

                            verifikator.

                        </p>


                        <a
                            href="javascript:void(0)"
                            id="profile-link-card"
                            class="btn btn-primary btn-dashboard"
                        >

                            <i class="fas fa-user-circle"></i>

                            Lihat Profil

                        </a>

                    </div>

                </div>


            </div>


        </div>


    </div>



    {{-- ========================================================= --}}
    {{-- MONITORING HARGA KOMODITAS --}}
    {{-- ========================================================= --}}

    <div class="container-besar">


        <p class="logo-title">

            Monitoring Harga Komoditas

        </p>


        <div class="filter-bar">


            <label for="commodity">

                Komoditas:

            </label>


            <select id="commodity">

                <option value="">

                    Pilih Komoditas

                </option>

            </select>



            <label for="start-date">

                Tanggal Mulai:

            </label>


            <input
                type="date"
                id="start-date"
            >



            <label for="end-date">

                Tanggal Akhir:

            </label>


            <input
                type="date"
                id="end-date"
            >



            <button
                type="button"
                id="filter-button"
            >

                <i class="fa-solid fa-filter"></i>

                Filter

            </button>


        </div>



        {{-- ===================================================== --}}
        {{-- GRAFIK --}}
        {{-- ===================================================== --}}

        <div class="chart-row">


            <div class="container-stat">

                <p class="logo-title">

                    Grafik Harga Komoditas

                </p>


                <div class="chart-wrapper">
                    <canvas id="myChart"></canvas>
                </div>


            </div>


        </div>



        {{-- ===================================================== --}}
        {{-- REKAP PERIODE --}}
        {{-- ===================================================== --}}

        <div class="content-container">


            <div class="container-kiri">


                <p class="logo-title">

                    Rekapitulasi Harga Periode Pilih

                </p>


                <div class="cards">


                    {{-- RATA-RATA --}}

                    <div class="card">

                        <h3>

                            Harga Rata-rata

                        </h3>


                        <div
                            class="card-body"
                            id="card1-body"
                        >

                            <p>

                                Belum ada data.

                            </p>

                        </div>

                    </div>



                    {{-- AKHIR PERIODE --}}

                    <div class="card">

                        <h3>

                            Harga Akhir Periode

                        </h3>


                        <div
                            class="card-body"
                            id="card2-body"
                        >

                            <p>

                                Belum ada data.

                            </p>

                        </div>

                    </div>



                    {{-- REKOR --}}

                    <div class="card">

                        <h3>

                            Rekor Harga

                        </h3>


                        <div
                            class="card-body"
                            id="card3-body"
                        >

                            <p>

                                Belum ada data.

                            </p>

                        </div>

                    </div>



                    {{-- KETERANGAN --}}

                    <div class="card">

                        <h3>

                            Keterangan

                        </h3>


                        <div
                            class="card-body"
                            id="card4-body"
                        >

                            <p>

                                Belum ada data.

                            </p>

                        </div>

                    </div>


                </div>


            </div>


        </div>


    </div>



    {{-- ========================================================= --}}
    {{-- INDIKATOR SPHP --}}
    {{-- ========================================================= --}}

    <div class="container-besar">


        <p class="logo-title">

            INDIKATOR SPHP

        </p>



        {{-- FILTER TANGGAL --}}

        <div class="filter-bar">


            <form
                method="GET"
                action="{{ route('dashboard_verifikator.index') }}"
            >


                <label for="tanggal">

                    Pilih Tanggal:

                </label>


                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                >


                <button type="submit">

                    <i class="fa-solid fa-filter"></i>

                    Filter

                </button>


            </form>


        </div>



        {{-- ===================================================== --}}
        {{-- TABEL INDIKATOR --}}
        {{-- ===================================================== --}}

        <div class="table-responsive">


            <table class="table table-bordered">


                <thead>

                <tr>


                    <th>

                        No

                    </th>


                    <th>

                        Komoditas

                    </th>


                    <th>

                        Satuan

                    </th>


                    <th>

                        HET/HAP

                    </th>


                    @php

                        $filterTanggal = \Carbon\Carbon::parse($tanggal);

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

                        {{ $filterTanggal->copy()->subDays(6)->format('d/m/Y') }}

                        -

                        {{ $filterTanggal->format('d/m/Y') }}

                    </th>


                    <th>

                        @if($filterTanggal->isToday())

                            Indikator Hari Ini

                        @else

                            Indikator {{ $filterTanggal->format('d/m/Y') }}

                        @endif

                    </th>


                    <th>

                        Indikator Rata-rata

                    </th>


                </tr>

                </thead>



                <tbody>


                @forelse($sumber_data as $data)


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

                            {{ number_format($data->hethap, 0, ',', '.') }}

                        </td>



                        {{-- HARGA TERBARU --}}

                        <td>


                            @php

                                $latest =
                                    $latestRekap->{$data->nama_komoditas} ?? null;

                                $previous =
                                    $previousRekap->{$data->nama_komoditas} ?? null;

                            @endphp


                            {{
                                is_numeric($latest)
                                ? number_format($latest, 0, ',', '.')
                                : '-'
                            }}



                            @if(
                                is_numeric($latest) &&
                                is_numeric($previous)
                            )


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



                        {{-- HARGA SEBELUMNYA --}}

                        <td>

                            {{
                                is_numeric($previous)
                                ? number_format($previous, 0, ',', '.')
                                : '-'
                            }}

                        </td>



                        {{-- RATA-RATA 7 HARI --}}

                        <td>


                            @php

                                $avg =
                                    $sevenDayAverage[$data->nama_komoditas]
                                    ?? null;

                            @endphp


                            {{
                                is_numeric($avg)
                                ? number_format($avg, 0, ',', '.')
                                : '-'
                            }}


                        </td>



                        {{-- INDIKATOR --}}

                        <td>


                            @php

                                $status =
                                    $indicators[$data->nama_komoditas]
                                    ?? null;

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



                        {{-- INDIKATOR RATA-RATA --}}

                        <td>


                            @php

                                $statusAvg =
                                    $averageIndicators[$data->nama_komoditas]
                                    ?? null;

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


                @empty


                    <tr>

                        <td
                            colspan="9"
                            style="text-align:center;"
                        >

                            Tidak ada data.

                        </td>

                    </tr>


                @endforelse


                </tbody>


            </table>


        </div>


    </div>



</div>



{{-- ========================================================= --}}
{{-- PROFILE MODAL --}}
{{-- ========================================================= --}}

<div
    id="profileModal"
    class="modal"
>


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

                    {{ Auth::user()->nama ?? Auth::user()->name }}

                </p>


                <p>

                    Role:

                    {{ Auth::user()->role }}

                </p>


                <p>

                    Email:

                    {{ Auth::user()->email }}

                </p>


                <a
                    href="#"
                    class="btn-logout"
                    onclick="handleLogout(event)"
                >

                    Logout

                </a>


            </div>



            <div class="profile-logo">


                <img
                    src="{{ asset('images/profileLogo.png') }}"
                    alt="Profile Logo"
                >
            </div>


        </div>


    </div>


</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('profileModal');
        const profileBtn = document.getElementById('profile-link');
        const profileCardBtn = document.getElementById('profile-link-card');
        const closeSpan = modal ? modal.querySelector('.close') : null;

        function openProfileModal(e) {
            if (e) e.preventDefault();
            if (!modal) return;
            modal.style.display = 'block';
            setTimeout(function () {
                modal.classList.add('show');
                modal.classList.remove('hide');
            }, 10);
        }

        function closeProfileModal() {
            if (!modal) return;
            modal.classList.remove('show');
            modal.classList.add('hide');
            setTimeout(function () {
                modal.style.display = 'none';
            }, 300);
        }

        if (profileBtn) profileBtn.addEventListener('click', openProfileModal);
        if (profileCardBtn) profileCardBtn.addEventListener('click', openProfileModal);
        if (closeSpan) closeSpan.addEventListener('click', closeProfileModal);

        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeProfileModal();
            }
        });
    });

    function handleLogout(event) {
        if (event) event.preventDefault();
        localStorage.removeItem('dashboardFilters');
        const form = document.getElementById('logout-form');
        if (form) {
            form.submit();
        } else {
            window.location.href = "{{ route('logout') }}";
        }
    }
</script>

<!-- =====================================================
     MAIN DASHBOARD JAVASCRIPT
====================================================== -->
<script>

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

        const saved = localStorage.getItem('dashboardFilters');
        if (saved) {
            try {
                const parsedData = JSON.parse(saved);
                const commodity = document.getElementById('commodity');
                const startDate = document.getElementById('start-date');
                const endDate = document.getElementById('end-date');

                if (commodity) {
                    commodity.value = parsedData.commodity || '';
                }
                if (startDate) {
                    startDate.value = parsedData.startDate || '';
                }
                if (endDate) {
                    endDate.value = parsedData.endDate || '';
                }

                if (parsedData.commodity && parsedData.startDate && parsedData.endDate) {
                    loadChartData();
                    loadStatistics();
                }
            } catch (error) {
                console.error('Filter tidak valid:', error);
            }
        }
    })
    .catch(error => {
        console.error('Gagal memuat komoditas:', error);
    });
}

/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function formatDate(dateString) {

    if (!dateString) {

        return '-';

    }


    const parts =
        dateString.split('-');


    if (parts.length !== 3) {

        return dateString;

    }


    return `${parts[2]}-${parts[1]}-${parts[0]}`;

}



/*
|--------------------------------------------------------------------------
| CHART
|--------------------------------------------------------------------------
*/

let myChart = null;


function initializeChart() {

    const canvas =
        document.getElementById(
            'myChart'
        );


    if (!canvas) {

        return;

    }


    const ctx =
        canvas.getContext('2d');


    myChart =
        new Chart(ctx, {

            type: 'line',


            data: {

                labels: [],


                datasets: [

                    {

                        label:
                            'Harga Komoditas',

                        data: [],

                        borderColor:
                            'blue',

                        borderWidth: 2,

                        tension: 0.3,

                        fill: false,

                        pointRadius: 3,

                        pointHoverRadius: 5

                    },


                    {

                        label:
                            'HET/HAP',

                        data: [],

                        borderColor:
                            'red',

                        borderDash:
                            [5, 5],

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

                            parser:
                                'yyyy-MM-dd',

                            tooltipFormat:
                                'dd/MM/yyyy',

                            unit:
                                'day',

                            displayFormats: {

                                day:
                                    'dd/MM/yyyy'

                            }

                        },


                        title: {

                            display: true,

                            text: 'Tanggal'

                        }

                    },


                    y: {

                        beginAtZero: false,


                        title: {

                            display: true,

                            text: 'Harga (Rp)'

                        },


                        ticks: {

                            callback:
                                function(value) {

                                    return Number(value)
                                        .toLocaleString('id-ID');

                                }

                        }

                    }

                }

            }

        });

}



/*
|--------------------------------------------------------------------------
| LOAD CHART DATA
|--------------------------------------------------------------------------
*/

function loadChartData() {

    const commodity =
        document.getElementById(
            'commodity'
        );


    const startDate =
        document.getElementById(
            'start-date'
        );


    const endDate =
        document.getElementById(
            'end-date'
        );


    if (
        !commodity ||
        !startDate ||
        !endDate
    ) {

        return;

    }


    const commodityValue =
        commodity.value;

    const startValue =
        startDate.value;

    const endValue =
        endDate.value;


    if (
        !commodityValue ||
        !startValue ||
        !endValue
    ) {

        return;

    }


    if (!myChart) {

        initializeChart();

    }


    fetch(
        `/api/grafik?commodity=${encodeURIComponent(commodityValue)}&start_date=${startValue}&end_date=${endValue}`
    )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data grafik'
                );

            }

            return response.json();

        })

        .then(data => {

            const harga =
                data.map(item => ({

                    x: item.date,

                    y: Number(item.price)

                }));


            const het =
                data.map(item => ({

                    x: item.date,

                    y: Number(item.hethap)

                }));


            if (!myChart) {

                return;

            }


            myChart.data.datasets[0].data =
                harga;


            myChart.data.datasets[1].data =
                het;


            myChart.update();



            /*
            |--------------------------------------------------------------------------
            | PERINGATAN INTERVENSI 7 HARI
            |--------------------------------------------------------------------------
            */

            if (data.length >= 7) {

                const last7Days =
                    data.slice(-7);


                const allIntervensi =
                    last7Days.every(
                        item =>
                            parseFloat(item.price) >=
                            parseFloat(item.harga_intervensi)
                    );


                if (allIntervensi) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            `⚠️ Komoditas ${commodityValue}`,

                        text:
                            'Harga sudah 7 hari berturut-turut berstatus INTERVENSI!',

                        timer:
                            5000,

                        timerProgressBar:
                            true,

                        showConfirmButton:
                            false

                    });

                }

            }

        })

        .catch(error => {

            console.error(
                'Gagal load chart:',
                error
            );

        });

}



/*
|--------------------------------------------------------------------------
| STATISTIK PERIODE
|--------------------------------------------------------------------------
*/

function loadStatistics() {

    const commodity =
        document.getElementById(
            'commodity'
        );


    const startDate =
        document.getElementById(
            'start-date'
        );


    const endDate =
        document.getElementById(
            'end-date'
        );


    if (
        !commodity ||
        !startDate ||
        !endDate
    ) {

        return;

    }


    const commodityValue =
        commodity.value;

    const startValue =
        startDate.value;

    const endValue =
        endDate.value;


    if (
        !commodityValue ||
        !startValue ||
        !endValue
    ) {

        return;

    }


    fetch(
        `/api/filter?commodity=${encodeURIComponent(commodityValue)}&start_date=${startValue}&end_date=${endValue}`
    )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil statistik'
                );

            }

            return response.json();

        })

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



            const card1 =
                document.getElementById(
                    'card1-body'
                );


            const card2 =
                document.getElementById(
                    'card2-body'
                );


            const card3 =
                document.getElementById(
                    'card3-body'
                );


            const card4 =
                document.getElementById(
                    'card4-body'
                );



            if (card1) {

                card1.innerHTML = `

                    <p>
                        Harga Rata-rata:
                        Rp.${data.averagePrice ?? '-'}
                    </p>

                    <p>
                        Status:

                        <span class="${statusAvgClass}">

                            ${data.statusAverage ?? '-'}

                        </span>

                    </p>

                `;

            }



            if (card2) {

                card2.innerHTML = `

                    <p>
                        Harga Akhir:
                        Rp.${data.latestPrice ?? '-'}
                    </p>

                    <p>
                        Tanggal:
                        ${formatDate(data.latestDate)}
                    </p>

                    <p>
                        Status:

                        <span class="${statusLatestClass}">

                            ${data.statusLatest ?? '-'}

                        </span>

                    </p>

                `;

            }



            if (card3) {

                card3.innerHTML = `

                    <p>
                        Harga Tertinggi:
                        Rp.${data.highestPrice ?? '-'}
                    </p>

                    <p>
                        Tanggal:
                        ${formatDate(data.highestDate)}
                    </p>

                    <p>
                        Harga Terendah:
                        Rp.${data.lowestPrice ?? '-'}
                    </p>

                    <p>
                        Tanggal:
                        ${formatDate(data.lowestDate)}
                    </p>

                `;

            }



            if (card4) {

                card4.innerHTML = `

                    <p>
                        Jumlah Data:
                        ${data.dataCount ?? '-'}
                    </p>

                    <p>
                        Status CV:
                        ${data.statusCV ?? '-'}
                    </p>

                `;

            }



            document
                .querySelectorAll('.card')
                .forEach(card => {

                    setTimeout(
                        () => {

                            card.classList.add(
                                'show'
                            );

                        },
                        100
                    );

                });

        })

        .catch(error => {

            console.error(
                'Gagal load statistik:',
                error
            );

        });

}



/*
|--------------------------------------------------------------------------
| FILTER BUTTON
|--------------------------------------------------------------------------
*/

const filterButton =
    document.getElementById(
        'filter-button'
    );


if (filterButton) {

    filterButton.addEventListener(
        'click',
        function () {

            saveFilters();

            loadChartData();

            loadStatistics();

        }
    );

}



/*
|--------------------------------------------------------------------------
| FILTER CHANGE
|--------------------------------------------------------------------------
*/

[
    'commodity',
    'start-date',
    'end-date'
].forEach(id => {


    const element =
        document.getElementById(id);


    if (!element) {

        return;

    }


    element.addEventListener(
        'change',
        function () {


            saveFilters();


            const startDate =
                document.getElementById(
                    'start-date'
                ).value;


            const endDate =
                document.getElementById(
                    'end-date'
                ).value;


            if (
                startDate &&
                endDate
            ) {

                loadChartData();

                loadStatistics();

            }

        }
    );

});



/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        initializeChart();

        loadCommodities();

    }
);

</script>


</body>

</html>

