<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"
    >

    <title>Validasi Data Pasar - S-DEGAN</title>

    {{-- CSS --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/verify_data.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/role_mobile.css') }}"
    >

    {{-- FONT AWESOME --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    >

    {{-- SWEET ALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        /* =========================================================
           CATATAN DESKTOP
        ========================================================= */

        .desktop-note-wrapper {
            display: none;
            margin-top: 8px;
            width: 100%;
        }

        .desktop-note-wrapper label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .desktop-validation-note {
            width: 100%;
            min-width: 180px;
            min-height: 65px;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
            font-family: inherit;
            font-size: 13px;
            line-height: 1.4;
            box-sizing: border-box;
        }

        .desktop-validation-note:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .desktop-validation-note.is-required {
            border-color: #dc2626;
        }

        .note-required-message {
            display: block;
            margin-top: 4px;
            color: #dc2626;
            font-size: 11px;
        }

        .desktop-validation-note.note-error {
            border-color: #dc2626;
            background-color: #fff5f5;
        }

        /* =========================================================
           STATUS SELECT
        ========================================================= */

        .status-valid {
            color: #15803d;
            font-weight: 600;
        }

        .status-invalid {
            color: #dc2626;
            font-weight: 600;
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        .validation-choice-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .validation-choice-group button {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-weight: 600;
        }

        .validation-choice-group button[data-status-choice="Valid"].is-selected {
            background: #16a34a;
            color: #fff;
            border-color: #16a34a;
        }

        .validation-choice-group button[data-status-choice="Tidak Valid"].is-selected {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }

        .validation-note.note-error {
            border-color: #dc2626 !important;
            background: #fff5f5;
        }

        /* =========================================================
           SUCCESS
        ========================================================= */

        .validation-success-view {
            max-width: 650px;
            margin: 60px auto;
            padding: 40px 25px;
            text-align: center;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .validation-success-view > i {
            font-size: 70px;
            color: #16a34a;
            margin-bottom: 20px;
        }

        .validation-success-view h1 {
            margin-bottom: 10px;
        }

        .validation-summary {
            margin: 25px 0;
            text-align: left;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .validation-summary h2 {
            margin: 0;
            padding: 15px;
            background: #f5f5f5;
            font-size: 18px;
        }

        .validation-summary > div {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            border-top: 1px solid #eee;
        }

        .validation-summary .is-rejected {
            color: #dc2626;
        }

        .validation-success-view a {
            display: inline-block;
            margin: 5px;
            padding: 10px 18px;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
        }

        @media (max-width: 767px) {

            .desktop-validation-view {
                display: none !important;
            }

            .mobile-validation-view {
                display: block !important;
            }

        }

        @media (min-width: 768px) {

            .mobile-validation-view {
                display: none !important;
            }

            .desktop-validation-view {
                display: block !important;
            }

        }

    </style>

</head>


<body class="role-portal">


    {{-- =========================================================
         MOBILE CHROME
    ========================================================== --}}

    @include('components.role-mobile-chrome', [
        'role' => 'verifikator',
        'active' => 'primary'
    ])


    {{-- =========================================================
         FORM UTAMA
    ========================================================== --}}

    <form
        id="saveForm"
        action="{{ route('dashboard_verifikator.save_data') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="action"
            id="action"
            value="save"
        >


        {{-- =====================================================
             SUCCESS
        ====================================================== --}}

        @if(session('success'))

            <section class="validation-success-view">

                <i
                    class="fas fa-circle-check"
                    aria-hidden="true"
                ></i>

                <h1>
                    Keputusan berhasil dikirim
                </h1>

                <p>
                    {{ session('success') }}
                </p>


                <section
                    class="validation-summary"
                    aria-label="Ringkasan keputusan verifikasi"
                >

                    <h2>
                        Ringkasan verifikasi
                    </h2>

                    <div>
                        <span>
                            Total komoditas
                        </span>

                        <strong>
                            {{ session('verification_total', 0) }}
                            item
                        </strong>
                    </div>

                    <div>
                        <span>
                            Disetujui
                        </span>

                        <strong>
                            {{ session('verification_valid', 0) }}
                            item
                        </strong>
                    </div>

                    <div>
                        <span>
                            Ditolak
                        </span>

                        <strong class="is-rejected">
                            {{ session('verification_rejected', 0) }}
                            item
                        </strong>
                    </div>

                </section>


                <a
                    href="{{ route('dashboard_verifikator.verify_data') }}"
                >
                    Kembali ke validasi
                </a>


                <a
                    href="{{ route('rekap.verifikator') }}"
                >
                    Lihat rekap data
                </a>

            </section>

        @else


            {{-- =================================================
                 MOBILE VIEW
            ================================================== --}}

            <main class="mobile-validation-view">

                <h1>
                    Validasi Data Pasar
                </h1>

                <p>
                    Periksa dan konfirmasi input data harga
                    komoditas hari ini.
                </p>


                {{-- BULK ACTION --}}

                <div class="validation-bulk-actions">

                    <button
                        type="button"
                        class="validate-all"
                        data-set-all="Valid"
                    >

                        <i
                            class="fas fa-check-double"
                            aria-hidden="true"
                        ></i>

                        Validasi semua

                    </button>


                    <button
                        type="button"
                        class="reject-all"
                        data-set-all="Tidak Valid"
                    >

                        <i
                            class="fas fa-xmark"
                            aria-hidden="true"
                        ></i>

                        Tolak semua

                    </button>

                </div>


                {{-- DATA --}}

                @forelse($dataKomoditas as $index => $data)

                    <article class="validation-card-list">

                        <div
                            class="validation-card"
                            data-validation-card
                        >

                            <h2>
                                {{ $data->nama_komoditas }}
                            </h2>

                            <p>
                                Laporan harga
                                {{
                                    \Illuminate\Support\Carbon::parse(
                                        $data->tanggal
                                    )
                                    ->locale('id')
                                    ->translatedFormat('d F Y')
                                }}
                            </p>


                            {{-- HARGA --}}

                            <div class="validation-prices">

                                <div>

                                    <span>
                                        HET
                                    </span>

                                    <strong>

                                        Rp
                                        {{
                                            number_format(
                                                $data->harga_het ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Kemarin
                                    </span>

                                    <strong>

                                        Rp
                                        {{
                                            number_format(
                                                $data->harga_kemarin ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Hari ini
                                    </span>

                                    <strong>

                                        Rp
                                        {{
                                            number_format(
                                                $data->harga_hari_ini ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </strong>

                                </div>

                            </div>


                            {{-- DATA TERSEMBUNYI --}}

                            <input
                                data-mobile-control
                                type="hidden"
                                name="data[{{ $index }}][tanggal]"
                                value="{{ $data->tanggal }}"
                            >


                            <input
                                data-mobile-control
                                type="hidden"
                                name="data[{{ $index }}][nama_komoditas]"
                                value="{{ $data->nama_komoditas }}"
                            >


                            <input
                                data-mobile-control
                                type="hidden"
                                name="data[{{ $index }}][harga_het]"
                                value="{{ $data->harga_het }}"
                            >


                            <input
                                data-mobile-control
                                type="hidden"
                                name="data[{{ $index }}][harga_kemarin]"
                                value="{{ $data->harga_kemarin }}"
                            >


                            <input
                                data-mobile-control
                                type="hidden"
                                name="data[{{ $index }}][harga_hari_ini]"
                                value="{{ $data->harga_hari_ini }}"
                            >


                            {{-- STATUS --}}

                            <label
                                class="visually-hidden"
                                for="mobile-status-{{ $index }}"
                            >
                                Status
                                {{ $data->nama_komoditas }}
                            </label>


                            <select
                                data-mobile-control
                                class="visually-hidden"
                                id="mobile-status-{{ $index }}"
                                name="data[{{ $index }}][status_verifikasi]"
                                required
                            >

                                <option value="">
                                    Pilih status
                                </option>

                                <option value="Valid">
                                    Valid
                                </option>

                                <option value="Tidak Valid">
                                    Tidak Valid
                                </option>

                            </select>


                            {{-- BUTTON STATUS --}}

                            <div
                                class="validation-choice-group"
                                aria-label="Keputusan {{ $data->nama_komoditas }}"
                            >

                                <button
                                    type="button"
                                    data-status-choice="Valid"
                                    data-status-target="mobile-status-{{ $index }}"
                                >
                                    <i class="fas fa-check"></i>
                                    Valid
                                </button>


                                <button
                                    type="button"
                                    data-status-choice="Tidak Valid"
                                    data-status-target="mobile-status-{{ $index }}"
                                >
                                    <i class="fas fa-xmark"></i>
                                    Tolak
                                </button>

                            </div>


                            {{-- CATATAN --}}

                            <label
                                class="visually-hidden"
                                for="mobile-note-{{ $index }}"
                            >
                                Catatan
                                {{ $data->nama_komoditas }}
                            </label>


                            <input
                                data-mobile-control
                                class="validation-note"
                                id="mobile-note-{{ $index }}"
                                type="text"
                                name="data[{{ $index }}][catatan_verifikasi]"
                                value="{{ old(
                                    'data.' . $index . '.catatan_verifikasi',
                                    $data->catatan_verifikasi
                                ) }}"
                                placeholder="Catatan jika data tidak valid..."
                            >

                        </div>

                    </article>

                @empty

                    <p class="validation-empty">
                        Tidak ada data harga yang menunggu validasi.
                    </p>

                @endforelse


                {{-- SUBMIT --}}

                @if($dataKomoditas->isNotEmpty())

                    <div class="validation-submit-area">

                        <button
                            type="button"
                            class="validation-save-trigger"
                        >

                            Kirim keputusan akhir

                            <i
                                class="fas fa-paper-plane"
                                aria-hidden="true"
                            ></i>

                        </button>

                    </div>

                @endif

            </main>


        @endif


        {{-- =========================================================
             DESKTOP VIEW
        ========================================================== --}}

        <section class="desktop-validation-view">

            <div class="container">


                {{-- NAVBAR --}}

                <div class="navbar">

                    <div class="logo">

                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="Logo S-DEGAN"
                        >

                        <p class="logo-text">
                            S-DEGAN
                        </p>

                        <p class="logo-text">
                            Sistem Deteksi Dini Gejolak Harga Pangan
                        </p>

                    </div>


                    <div class="menu">

                        <a
                            href="{{ route('dashboard_verifikator.index') }}"
                        >

                            <i class="fas fa-home"></i>

                            Dashboard

                        </a>


                        <a
                            href="{{ route('dashboard_verifikator.verify_data') }}"
                        >

                            <i class="fas fa-check-circle"></i>

                            Verifikasi

                        </a>


                        <a
                            href="{{ route('rekap.verifikator') }}"
                        >

                            <i class="fas fa-chart-line"></i>

                            Rekap Data

                        </a>

                    </div>

                </div>


                {{-- MAIN --}}

                <main class="main-content">

                    <h2 class="center-text">
                        Verifikasi Data
                    </h2>


                    <div class="table-container">


                        {{-- BULK STATUS --}}

                        <div class="filter-container">

                            <label for="bulkStatus">
                                Verifikasi instan
                            </label>


                            <select id="bulkStatus">

                                <option value="">
                                    Pilih semua
                                </option>

                                <option value="Valid">
                                    Valid
                                </option>

                                <option value="Tidak Valid">
                                    Tidak Valid
                                </option>

                            </select>

                        </div>


                        {{-- TABLE --}}

                        <div class="table-wrapper">

                            <table
                                id="verifyDataTable"
                                class="table table-bordered table-striped"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Tanggal
                                        </th>

                                        <th>
                                            Nama Komoditas
                                        </th>

                                        <th>
                                            Harga HET
                                        </th>

                                        <th>
                                            Harga Kemarin
                                        </th>

                                        <th>
                                            Harga Hari Ini
                                        </th>

                                        <th>
                                            Status & Catatan
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($dataKomoditas as $index => $data)

                                        <tr>


                                            {{-- TANGGAL --}}

                                            <td>

                                                {{ $data->tanggal }}


                                                <input
                                                    data-desktop-control
                                                    type="hidden"
                                                    name="data[{{ $index }}][tanggal]"
                                                    value="{{ $data->tanggal }}"
                                                >

                                            </td>


                                            {{-- KOMODITAS --}}

                                            <td>

                                                {{ $data->nama_komoditas }}


                                                <input
                                                    data-desktop-control
                                                    type="hidden"
                                                    name="data[{{ $index }}][nama_komoditas]"
                                                    value="{{ $data->nama_komoditas }}"
                                                >

                                            </td>


                                            {{-- HET --}}

                                            <td>

                                                Rp.
                                                {{
                                                    number_format(
                                                        $data->harga_het ?? 0,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}


                                                <input
                                                    data-desktop-control
                                                    type="hidden"
                                                    name="data[{{ $index }}][harga_het]"
                                                    value="{{ $data->harga_het }}"
                                                >

                                            </td>


                                            {{-- KEMARIN --}}

                                            <td>

                                                Rp.
                                                {{
                                                    number_format(
                                                        $data->harga_kemarin ?? 0,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}


                                                <input
                                                    data-desktop-control
                                                    type="hidden"
                                                    name="data[{{ $index }}][harga_kemarin]"
                                                    value="{{ $data->harga_kemarin }}"
                                                >

                                            </td>


                                            {{-- HARI INI --}}

                                            <td>

                                                Rp.
                                                {{
                                                    number_format(
                                                        $data->harga_hari_ini ?? 0,
                                                        0,
                                                        ',',
                                                        '.'
                                                    )
                                                }}


                                                <input
                                                    data-desktop-control
                                                    type="hidden"
                                                    name="data[{{ $index }}][harga_hari_ini]"
                                                    value="{{ $data->harga_hari_ini }}"
                                                >

                                            </td>


                                            {{-- STATUS --}}

                                            <td>

                                                <label
                                                    class="visually-hidden"
                                                    for="desktop-status-{{ $index }}"
                                                >

                                                    Status
                                                    {{ $data->nama_komoditas }}

                                                </label>


                                                <select
                                                    data-desktop-control
                                                    id="desktop-status-{{ $index }}"
                                                    name="data[{{ $index }}][status_verifikasi]"
                                                    required
                                                >

                                                    <option value="">
                                                        Pilih status
                                                    </option>

                                                    <option value="Valid">
                                                        Valid
                                                    </option>

                                                    <option value="Tidak Valid">
                                                        Tidak Valid
                                                    </option>

                                                </select>


                                                {{-- CATATAN --}}

                                                <div
                                                    class="desktop-note-wrapper"
                                                    id="desktop-note-wrapper-{{ $index }}"
                                                >

                                                    <label
                                                        for="desktop-note-{{ $index }}"
                                                    >
                                                        Catatan
                                                    </label>


                                                    <textarea
                                                        data-desktop-control
                                                        id="desktop-note-{{ $index }}"
                                                        name="data[{{ $index }}][catatan_verifikasi]"
                                                        class="validation-note desktop-validation-note"
                                                        rows="3"
                                                        placeholder="Isi alasan jika data tidak valid..."
                                                    >{{ old(
                                                        'data.' . $index . '.catatan_verifikasi',
                                                        $data->catatan_verifikasi
                                                    ) }}</textarea>


                                                    <small class="note-required-message">
                                                        Catatan wajib diisi jika data tidak valid.
                                                    </small>

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>


                        {{-- BUTTON SIMPAN --}}

                        @if($dataKomoditas->isNotEmpty())

                            <div class="button-container">

                                <button
                                    type="button"
                                    class="btn btn-add validation-save-trigger"
                                >

                                    <i class="fas fa-save"></i>

                                    Simpan

                                </button>

                            </div>

                        @endif

                    </div>

                </main>

            </div>

        </section>


    </form>


    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const form =
                document.getElementById('saveForm');


            const mobileQuery =
                window.matchMedia('(max-width: 767px)');


            /* =====================================================
               AKTIFKAN KONTROL BERDASARKAN DEVICE
            ===================================================== */

            function syncValidationControls() {

                document
                    .querySelectorAll('[data-mobile-control]')
                    .forEach(function (control) {

                        control.disabled =
                            !mobileQuery.matches;

                    });


                document
                    .querySelectorAll('[data-desktop-control]')
                    .forEach(function (control) {

                        control.disabled =
                            mobileQuery.matches;

                    });

            }


            /* =====================================================
               SELECT STATUS AKTIF
            ===================================================== */

            function activeStatusSelects() {

                return Array.from(
                    document.querySelectorAll(
                        'select[name*="[status_verifikasi]"]'
                    )
                ).filter(function (select) {

                    return !select.disabled;

                });

            }


            /* =====================================================
               UPDATE CATATAN DESKTOP
            ===================================================== */

            function updateDesktopNote(select) {

                if (!select) {
                    return;
                }


                const match =
                    select.name.match(
                        /data\[(\d+)\]/
                    );


                if (!match) {
                    return;
                }


                const index =
                    match[1];


                const wrapper =
                    document.getElementById(
                        'desktop-note-wrapper-' + index
                    );


                const note =
                    document.getElementById(
                        'desktop-note-' + index
                    );


                if (!wrapper || !note) {
                    return;
                }


                if (select.value === 'Tidak Valid') {

                    wrapper.style.display = 'block';

                    note.required = true;

                    note.classList.add(
                        'is-required'
                    );

                } else {

                    wrapper.style.display = 'none';

                    note.required = false;

                    note.classList.remove(
                        'is-required'
                    );

                    note.classList.remove(
                        'note-error'
                    );

                }

            }


            /* =====================================================
               UPDATE SEMUA CATATAN
            ===================================================== */

            function updateAllDesktopNotes() {

                document
                    .querySelectorAll(
                        'select[name*="[status_verifikasi]"]'
                    )
                    .forEach(function (select) {

                        if (!select.disabled) {

                            updateDesktopNote(
                                select
                            );

                        }

                    });

            }


            /* =====================================================
               STATUS MOBILE
            ===================================================== */

            function syncChoiceState(select) {

                const card =
                    select.closest(
                        '[data-validation-card]'
                    );


                if (!card) {
                    return;
                }


                card
                    .querySelectorAll(
                        '[data-status-choice]'
                    )
                    .forEach(function (button) {

                        button.classList.toggle(
                            'is-selected',
                            button.dataset.statusChoice ===
                            select.value
                        );

                    });

            }


            /* =====================================================
               SET SEMUA STATUS
            ===================================================== */

            function setAllStatus(status) {

                activeStatusSelects()
                    .forEach(function (select) {

                        select.value =
                            status;


                        syncChoiceState(
                            select
                        );


                        updateDesktopNote(
                            select
                        );

                    });

            }


            /* =====================================================
               VALIDASI CATATAN
            ===================================================== */

            function validateNotes() {

                const selects =
                    activeStatusSelects();


                for (const select of selects) {

                    if (
                        select.value !==
                        'Tidak Valid'
                    ) {

                        continue;

                    }


                    const match =
                        select.name.match(
                            /data\[(\d+)\]/
                        );


                    if (!match) {
                        continue;
                    }


                    const index =
                        match[1];


                    const mobileNote =
                        document.querySelector(
                            'input[name="data[' +
                            index +
                            '][catatan_verifikasi]"]'
                        );


                    const desktopNote =
                        document.querySelector(
                            'textarea[name="data[' +
                            index +
                            '][catatan_verifikasi]"]'
                        );


                    let note = null;


                    if (
                        mobileQuery.matches &&
                        mobileNote
                    ) {

                        note =
                            mobileNote;

                    } else if (
                        !mobileQuery.matches &&
                        desktopNote
                    ) {

                        note =
                            desktopNote;

                    }


                    if (
                        !note ||
                        note.value.trim() === ''
                    ) {

                        if (note) {

                            note.classList.add(
                                'note-error'
                            );

                            note.focus();

                        }


                        Swal.fire({

                            icon: 'warning',

                            title:
                                'Catatan wajib diisi',

                            text:
                                'Data yang tidak valid harus diberikan catatan atau alasan.'

                        });


                        return false;

                    }

                }


                return true;

            }


            /* =====================================================
               VALIDASI STATUS
            ===================================================== */

            function validateStatuses() {

                const selects =
                    activeStatusSelects();


                if (selects.length === 0) {

                    Swal.fire({

                        icon: 'warning',

                        title:
                            'Tidak ada data',

                        text:
                            'Tidak ada data yang dapat diverifikasi.'

                    });

                    return false;

                }


                const allFilled =
                    selects.every(
                        function (select) {

                            return (
                                select.value !== ''
                            );

                        }
                    );


                if (!allFilled) {

                    Swal.fire({

                        icon: 'warning',

                        title:
                            'Status belum lengkap',

                        text:
                            'Pilih keputusan untuk setiap komoditas sebelum mengirim.'

                    });


                    return false;

                }


                return true;

            }


            /* =====================================================
               INITIAL
            ===================================================== */

            syncValidationControls();

            updateAllDesktopNotes();


            /* =====================================================
               RESPONSIVE CHANGE
            ===================================================== */

            if (
                typeof mobileQuery.addEventListener ===
                'function'
            ) {

                mobileQuery.addEventListener(
                    'change',
                    function () {

                        syncValidationControls();

                        updateAllDesktopNotes();

                    }
                );

            } else {

                mobileQuery.addListener(
                    function () {

                        syncValidationControls();

                        updateAllDesktopNotes();

                    }
                );

            }


            /* =====================================================
               VALIDASI SEMUA / TOLAK SEMUA
            ===================================================== */

            document
                .querySelectorAll('[data-set-all]')
                .forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            setAllStatus(
                                button.dataset.setAll
                            );

                        }
                    );

                });


            /* =====================================================
               BUTTON STATUS MOBILE
            ===================================================== */

            document
                .querySelectorAll(
                    '[data-status-choice]'
                )
                .forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const select =
                                document.getElementById(
                                    button.dataset.statusTarget
                                );


                            if (!select) {
                                return;
                            }


                            select.value =
                                button.dataset.statusChoice;


                            syncChoiceState(
                                select
                            );


                            updateDesktopNote(
                                select
                            );

                        }
                    );

                });


            /* =====================================================
               BULK STATUS DESKTOP
            ===================================================== */

            const bulkStatus =
                document.getElementById(
                    'bulkStatus'
                );


            if (bulkStatus) {

                bulkStatus.addEventListener(
                    'change',
                    function () {

                        if (
                            bulkStatus.value
                        ) {

                            setAllStatus(
                                bulkStatus.value
                            );

                        }

                    }
                );

            }


            /* =====================================================
               STATUS DESKTOP BERUBAH
            ===================================================== */

            document
                .querySelectorAll(
                    'select[data-desktop-control][name*="[status_verifikasi]"]'
                )
                .forEach(function (select) {

                    select.addEventListener(
                        'change',
                        function () {

                            updateDesktopNote(
                                select
                            );

                        }
                    );

                });


            /* =====================================================
               HAPUS ERROR CATATAN
            ===================================================== */

            document
                .querySelectorAll(
                    '.validation-note'
                )
                .forEach(function (note) {

                    note.addEventListener(
                        'input',
                        function () {

                            if (
                                note.value.trim() !== ''
                            ) {

                                note.classList.remove(
                                    'note-error'
                                );

                            }

                        }
                    );

                });


            /* =====================================================
               SIMPAN / KIRIM KEPUTUSAN
            ===================================================== */

            document
                .querySelectorAll(
                    '.validation-save-trigger'
                )
                .forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {


                            /* VALIDASI STATUS */

                            if (
                                !validateStatuses()
                            ) {

                                return;

                            }


                            /* VALIDASI CATATAN */

                            if (
                                !validateNotes()
                            ) {

                                return;

                            }


                            const selects =
                                activeStatusSelects();


                            const validCount =
                                selects.filter(
                                    function (select) {

                                        return (
                                            select.value ===
                                            'Valid'
                                        );

                                    }
                                ).length;


                            const rejectedCount =
                                selects.length -
                                validCount;


                            /* KONFIRMASI */

                            Swal.fire({
                                title:
                                    'Kirim keputusan verifikasi?',
                                html:
                                    '<p>' +
                                    validCount +
                                    ' komoditas disetujui</p>' +
                                    '<p>' +
                                    rejectedCount +
                                    ' komoditas ditolak</p>',
                                icon:
                                    'question',
                                showCancelButton:
                                    true,
                                confirmButtonText:
                                    'Kirim keputusan',
                                cancelButtonText:
                                    'Periksa kembali',
                                reverseButtons:
                                    true
                            }).then(
                                function (result) {
                                    if (
                                        result.isConfirmed
                                    ) {
                                        Swal.fire({
                                            title: 'Mengirim Keputusan...',
                                            text: 'Mohon tunggu, keputusan verifikasi sedang diproses.',
                                            icon: 'info',
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            showConfirmButton: false,
                                            didOpen: () => {
                                                Swal.showLoading();
                                            }
                                        });
                                        form.submit();
                                    }
                                }
                            );

                        }
                    );

                });

        });

    </script>

</body>

</html>