<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Data Pangan - S-DEGAN</title>
    <link rel="stylesheet" href="{{ asset('css/input_entry.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="role-portal">
    @include('components.role-mobile-chrome', ['role' => 'operator', 'active' => 'primary'])

    <main class="operator-entry-page">
        <section class="portal-intro">
            <h1>Sistem Deteksi Dini Gejolak Harga Pangan</h1>
            <p>Bidang Ketahanan Pangan - DKPP Kota Kediri</p>
            <span class="portal-user-badge">
                <i class="fas fa-user" aria-hidden="true"></i>
                Operator: {{ Auth::user()->nama ?? 'Pengguna' }}
            </span>
        </section>

        <h2 class="operator-form-heading">Input Data Pangan</h2>

        @if ($errors->has('tanggal'))
            <div class="alert alert-danger mx-4" role="alert">
                {{ $errors->first('tanggal') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mx-4" role="alert">
                <i class="fas fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('dashboard_operator.data.insert') }}" method="POST" id="formPangan">
            @csrf

            <div class="date-field">
                <label for="tanggal">Pilih Tanggal Input</label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                    max="{{ now()->format('Y-m-d') }}"
                    required
                >
            </div>

            <div class="table-responsive">
                <div class="scrollable-table">
                    <table class="table" aria-label="Form harga komoditas pangan">
                        <thead>
                            <tr>
                                <th>Komoditas</th>
                                <th>HET</th>
                                <th>Kemarin</th>
                                <th>Hari Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($komoditasList as $komoditas)
                                @php
                                    $nama = $komoditas->nama_komoditas;
                                    $hargaHet = $komoditas->hethap ?? 0;
                                    $hargaKemarin = $rekapData[$nama] ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        {{ $nama }}
                                        @if(!empty($komoditas->satuan))
                                            <span class="commodity-unit">/ {{ $komoditas->satuan }}</span>
                                        @endif
                                        <input type="hidden" name="nama_komoditas[]" value="{{ $nama }}">
                                    </td>
                                    <td>
                                        {{ number_format($hargaHet, 0, ',', '.') }}
                                        <input type="hidden" name="harga_het[]" value="{{ $hargaHet }}">
                                    </td>
                                    <td>
                                        {{ number_format($hargaKemarin, 0, ',', '.') }}
                                        <input type="hidden" name="harga_kemarin[]" value="{{ $hargaKemarin }}">
                                    </td>
                                    <td>
                                        <label class="visually-hidden" for="harga-hari-ini-{{ $loop->index }}">Harga {{ $nama }} hari ini</label>
                                        <div class="price-input">
                                            <span>Rp</span>
                                            <input
                                                type="number"
                                                id="harga-hari-ini-{{ $loop->index }}"
                                                name="harga_hari_ini[]"
                                                value="{{ old('harga_hari_ini.' . $loop->index) }}"
                                                min="0"
                                                inputmode="decimal"
                                                placeholder="0"
                                                required
                                            >
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="submit-area">
                <a href="{{ route('dashboard_operator.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Kembali ke Dashboard
                </a>
                <button type="submit" id="submitBtn">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    Kirim Data
                </button>
            </div>
        </form>
    </main>

    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1080">
        <div id="tanggalAlert" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Data untuk tanggal ini sudah ada. Silakan pilih tanggal lain.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        let isSubmitting = false;

        document.getElementById('formPangan').addEventListener('submit', function (event) {
            event.preventDefault();

            if (isSubmitting) return;

            const form = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Kirim data harga?',
                text: 'Data harga yang sudah diisi akan dikirim untuk diverifikasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim data',
                cancelButtonText: 'Periksa kembali',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                isSubmitting = true;

                Swal.fire({
                    title: 'Mengirim Data...',
                    text: 'Mohon tunggu, data sedang dikirim ke verifikator.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            });
        });

        document.getElementById('tanggal').addEventListener('change', function () {
            const tanggal = this.value;
            if (!tanggal) return;

            fetch("{{ route('check.tanggal') }}?tanggal=" + encodeURIComponent(tanggal))
                .then(response => response.json())
                .then(data => {
                    if (!data.exists) return;

                    new bootstrap.Toast(document.getElementById('tanggalAlert')).show();
                    this.value = '';
                });
        });
    </script>
</body>
</html>
