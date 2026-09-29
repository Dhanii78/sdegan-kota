<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Riwayat Kiriman - S-DEGAN</title>
    <link rel="stylesheet" href="{{ asset('css/data_pangan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="role-portal">

    @include('components.role-mobile-chrome', ['role' => 'operator', 'active' => 'history'])

    @php
        $activeStatus = request('status', 'all');
        $reportGroups = $dataKomoditas->groupBy('tanggal')->sortKeysDesc();
    @endphp

    {{-- ================================================================
         MOBILE VIEW — hanya tampil di layar ≤ 767 px (via role_mobile.css)
         ================================================================ --}}
    <main class="mobile-history-view">
        <h1>Riwayat Kiriman</h1>
        <p>Daftar laporan pemantauan harga komoditas pangan.</p>

        {{-- Banner sukses --}}
        @if(session('success'))
            <div class="operator-success-banner" id="mob-alert-success" role="status">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
                <div>
                    <strong>Berhasil</strong>
                    <span>{{ session('success') }}</span>
                    @if(session('report_count'))
                        <br><span>{{ session('report_count') }} komoditas menunggu verifikasi.</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Banner error --}}
        @if(session('error'))
            <div id="mob-alert-error" role="alert"
                 style="display:flex;align-items:flex-start;gap:10px;margin:0 0 18px;padding:12px;
                        border:1px solid #ffc9c9;border-radius:7px;background:#fde8e8;color:#c6181d;">
                <i class="fas fa-circle-exclamation" aria-hidden="true"
                   style="margin-top:2px;font-size:1.25rem;flex-shrink:0;"></i>
                <div>
                    <strong style="display:block;">Kesalahan</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Form filter --}}
        <form method="GET" action="{{ route('dashboard_operator.data_pangan') }}" class="history-filter-form">
            <label class="history-filter-field">
                <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                <span class="visually-hidden">Cari komoditas</span>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari pasar atau komoditas...">
            </label>
            <label class="history-filter-field">
                <i class="fas fa-calendar-days" aria-hidden="true"></i>
                <span class="visually-hidden">Bulan laporan</span>
                <input type="month" name="month" value="{{ request('month') }}"
                       onchange="this.form.submit()">
            </label>
            <div class="history-status-tabs" aria-label="Filter status laporan">
                <button type="submit" name="status" value="all"
                        class="{{ $activeStatus === 'all'      ? 'is-active' : '' }}">Semua</button>
                <button type="submit" name="status" value="valid"
                        class="{{ $activeStatus === 'valid'    ? 'is-active' : '' }}">Tervalidasi</button>
                <button type="submit" name="status" value="pending"
                        class="{{ $activeStatus === 'pending'  ? 'is-active' : '' }}">Menunggu</button>
                <button type="submit" name="status" value="rejected"
                        class="{{ $activeStatus === 'rejected' ? 'is-active' : '' }}">Ditolak</button>
            </div>
        </form>

        {{-- Daftar riwayat (accordion per tanggal) --}}
        <section class="history-list" aria-label="Daftar riwayat laporan">
            @forelse($reportGroups as $tanggal => $reports)
                @php
                    $statuses = $reports->pluck('status_verifikasi');
                    if ($statuses->every(fn ($s) => $s === 'Valid')) {
                        $statusLabel = 'Tervalidasi'; $statusClass = 'valid';
                    } elseif ($statuses->contains(fn ($s) => in_array($s, ['Belum_Diverifikasi', 'Belum Diverifikasi']))) {
                        $statusLabel = 'Menunggu Verifikasi'; $statusClass = 'pending';
                    } else {
                        $statusLabel = 'Perlu Revisi'; $statusClass = 'rejected';
                    }
                @endphp

                <div class="history-card-wrapper">
                    {{-- Header kartu (klik untuk buka/tutup) --}}
                    <article class="history-card" onclick="toggleCard(this)"
                             role="button" tabindex="0"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleCard(this)}"
                             aria-expanded="false">
                        <div class="history-card__date">
                            <i class="fas fa-calendar-day" aria-hidden="true"></i>
                            {{ \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}
                        </div>
                        <span class="history-status history-status--{{ $statusClass }}">{{ $statusLabel }}</span>
                        <h2 class="history-card__title">Laporan harga pangan</h2>
                        <p class="history-card__meta">{{ $reports->count() }} komoditas diperbarui</p>
                        <i class="fas fa-chevron-right history-card__arrow" aria-hidden="true"></i>
                    </article>

                    {{-- Detail komoditas (tersembunyi awalnya) --}}
                    <div class="history-card__details" style="display:none;">
                        <div class="details-divider"></div>
                        <div class="details-list">
                            @foreach($reports as $report)
                                <div class="details-item">
                                    <div class="details-item__info">
                                        <div class="details-item__name">{{ $report->nama_komoditas }}</div>
                                        <div class="details-item__prices">
                                            <span class="price-lbl">Kemarin:</span>
                                            <span class="price-val">Rp{{ number_format($report->harga_kemarin, 0, ',', '.') }}</span>
                                            &nbsp;&middot;&nbsp;
                                            <span class="price-lbl">Hari Ini:</span>
                                            <span class="price-val price-val--today">Rp{{ number_format($report->harga_hari_ini, 0, ',', '.') }}</span>
                                        </div>
                                        {{-- Label alasan penolakan --}}
                                        @if(!in_array($report->status_verifikasi, ['Valid', 'Belum_Diverifikasi', 'Belum Diverifikasi']))
                                            <div class="details-item__reason">
                                                <i class="fa-solid fa-circle-info"></i>
                                                {{ str_replace('_', ' ', $report->status_verifikasi) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="details-item__action">
                                        {{-- Tombol Edit untuk semua status kecuali Valid --}}
                                        @if($report->status_verifikasi !== 'Valid')
                                            <a href="{{ route('dashboard_operator.data.edit', $report->id) }}"
                                               class="btn-edit-mob">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                        @else
                                            <span class="badge-locked" title="Tidak dapat diedit">
                                                <i class="fa-solid fa-lock"></i> Terkunci
                                            </span>
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

    {{-- ================================================================
         DESKTOP VIEW — hanya tampil di layar > 767 px (disembunyikan via CSS)
         ================================================================ --}}
    <div class="desktop-operator-data">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <p class="logo-text">S-DEGAN</p>
                    <p class="logo-text">Sistem Deteksi Dini Gejolak Harga Pangan</p>
                </div>
                <button class="toggle-btn">☰</button>
                <div class="menu">
                    <a href="{{ route('dashboard_operator.index') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('dashboard_operator.input_data') }}">
                        <i class="fas fa-keyboard"></i> Input Data
                    </a>
                    <a href="{{ route('dashboard_operator.data_pangan') }}">
                        <i class="fas fa-clock-rotate-left"></i> Riwayat
                    </a>
                    <a href="{{ route('rekap.operator') }}">
                        <i class="fas fa-chart-line"></i> Rekap Data
                    </a>
                </div>
                <div class="datetime-op">
                    <span id="current-time"></span>
                    <hr>
                    <span id="current-date"></span>
                </div>
            </div>

            <h2 class="center-text">Riwayat Data Pangan</h2>

            <div style="width:60%;margin:0 auto 16px;display:flex;justify-content:center;gap:20px;">
                <a href="{{ route('dashboard_operator.input_data') }}" class="btn-custom btn-success">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Data Pangan
                </a>
            </div>

            <div class="main-content">
                <form id="bulkDeleteForm"
                      action="{{ route('dashboard_operator.data.bulkDelete') }}"
                      method="POST">
                    @csrf
                    <div class="table-responsive">
                        <div class="table-container">
                            <table id="entryDataTable" class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll"
                                                   onclick="toggleSelectAll(this)">
                                        </th>
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
                                                <input type="checkbox" class="row-checkbox"
                                                       name="ids[]" value="{{ $data->id }}"
                                                       data-tanggal="{{ $data->tanggal }}">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->tanggal ?? '-' }}</td>
                                            <td>{{ $data->nama_komoditas ?? '-' }}</td>
                                            <td>Rp.{{ number_format($data->harga_het ?? 0, 0, ',', '.') }}</td>
                                            <td>Rp.{{ number_format($data->harga_kemarin ?? 0, 0, ',', '.') }}</td>
                                            <td>Rp.{{ number_format($data->harga_hari_ini ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                @if($data->status_verifikasi == 'Valid')         ✅ Valid
                                                @elseif($data->status_verifikasi == 'Tidak_valid' || $data->status_verifikasi == 'Tidak Valid') ❌ Tidak Valid
                                                @elseif($data->status_verifikasi == 'Harga_Tidak_Wajar') ⚠️ Harga Tidak Wajar
                                                @elseif($data->status_verifikasi == 'Data_Ganda') ⚠️ Data Ganda
                                                @elseif($data->status_verifikasi == 'Revisi')    🔄 Revisi
                                                @else Belum Diverifikasi
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data->status_verifikasi !== 'Valid')
                                                    <a href="{{ route('dashboard_operator.data.edit', $data->id) }}"
                                                       class="btn btn-edit btn-sm">
                                                        <i class="fa-solid fa-pen"></i> Edit
                                                    </a>
                                                @else
                                                    <span class="text-muted"
                                                          title="Sudah diverifikasi — tidak dapat diedit">
                                                        <i class="fa-solid fa-lock"></i> Terkunci
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted"
                                                style="padding:20px;">
                                                <em>Belum ada data pangan.</em>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="bulk-action-container">
                    <button type="button" id="btnBulkDelete" class="btn-bulk-delete">
                        <i class="fas fa-trash"></i> Hapus Data Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         SCRIPTS
         ================================================================ --}}
    <script>
        /* ---- Auto-hide mobile alert banners setelah 3 detik ---- */
        document.addEventListener('DOMContentLoaded', function () {
            ['mob-alert-success', 'mob-alert-error'].forEach(function (id) {
                var el = document.getElementById(id);
                if (!el) return;
                setTimeout(function () {
                    el.style.transition = 'opacity 0.5s ease-out';
                    el.style.opacity = '0';
                    setTimeout(function () { el.remove(); }, 500);
                }, 3000);
            });
        });

        /* ---- Accordion toggle (mobile) ---- */
        function toggleCard(cardEl) {
            var expanded = cardEl.getAttribute('aria-expanded') === 'true';
            cardEl.setAttribute('aria-expanded', !expanded);
            cardEl.classList.toggle('is-expanded');
            var details = cardEl.nextElementSibling;
            if (!details) return;
            details.style.display = details.style.display === 'block' ? 'none' : 'block';
        }

        /* ---- Desktop: jam & tanggal real-time ---- */
        function updateDateTime() {
            var dEl = document.getElementById('current-date');
            var tEl = document.getElementById('current-time');
            if (!dEl || !tEl) return;
            var now = new Date();
            dEl.innerText = now.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            tEl.innerText = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        /* ---- Desktop: select all checkbox ---- */
        function toggleSelectAll(source) {
            document.querySelectorAll('.row-checkbox').forEach(function (cb) {
                cb.checked = source.checked;
            });
        }

        document.addEventListener('DOMContentLoaded', function () {

            /* ---- Desktop: toggle sidebar ---- */
            var toggleBtn = document.querySelector('.desktop-operator-data .toggle-btn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    var navbar    = document.querySelector('.desktop-operator-data .navbar');
                    var container = document.querySelector('.desktop-operator-data .container');
                    if (navbar)    navbar.classList.toggle('hidden');
                    if (container) {
                        container.classList.toggle('sidebar-hidden');
                        container.classList.toggle('shifted');
                    }
                    this.classList.toggle('hide');
                });
            }

            /* ---- Desktop: bulk delete ---- */
            var btnBulkDelete = document.getElementById('btnBulkDelete');
            if (btnBulkDelete) {
                btnBulkDelete.addEventListener('click', function () {
                    var checked = document.querySelectorAll('input[name="ids[]"]:checked');
                    if (checked.length === 0) {
                        Swal.fire({
                            icon: 'warning', title: 'Pilih data',
                            text: 'Silakan pilih data yang ingin dihapus.',
                            toast: true, position: 'top-end',
                            timer: 2000, showConfirmButton: false
                        });
                        return;
                    }
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Anda akan menghapus ' + checked.length + ' data?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            document.getElementById('bulkDeleteForm').submit();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire({
                                icon: 'info', title: 'Dibatalkan',
                                text: 'Data tidak dihapus.',
                                toast: true, position: 'top-end',
                                timer: 2000, showConfirmButton: false
                            });
                        }
                    });
                });
            }
        });
    </script>

</body>
</html>
