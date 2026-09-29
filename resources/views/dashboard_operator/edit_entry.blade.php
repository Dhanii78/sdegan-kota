<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Data Pangan - S-DEGAN</title>
    <link rel="stylesheet" href="{{ asset('css/input_entry.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .edit-container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        .edit-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }
        .form-control-readonly {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            cursor: not-allowed;
        }
        .btn-update {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-update:hover {
            background-color: #1d4ed8;
        }
        .btn-cancel {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
        .edit-img-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-img {
            max-height: 150px;
            object-fit: contain;
        }
    </style>
</head>
<body class="role-portal">
    @include('components.role-mobile-chrome', ['role' => 'operator', 'active' => 'history'])

    <main class="operator-entry-page px-3">
        <section class="portal-intro">
            <h1>Sistem Deteksi Dini Gejolak Harga Pangan</h1>
            <p>Bidang Ketahanan Pangan - DKPP Kota Kediri</p>
            <span class="portal-user-badge">
                <i class="fas fa-user" aria-hidden="true"></i>
                Operator: {{ Auth::user()->nama ?? 'Pengguna' }}
            </span>
        </section>

        <div class="edit-container">
            <h2 class="edit-title">
                <i class="fas fa-edit me-2 text-primary"></i>Edit Data Komoditas
            </h2>

            <div class="edit-img-container">
                <img src="{{ asset('images/input_pangan.png') }}" alt="Edit Data" class="edit-img">
            </div>

            <form action="{{ route('dashboard_operator.data.update', $data->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <!-- Tanggal -->
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ $data->tanggal }}" readonly class="form-control form-control-readonly">
                </div>

                <!-- Nama Komoditas -->
                <div class="mb-3">
                    <label for="nama_komoditas" class="form-label">Nama Komoditas</label>
                    <input type="text" id="nama_komoditas" name="nama_komoditas" value="{{ $data->nama_komoditas }}" readonly class="form-control form-control-readonly">
                </div>

                <!-- Harga HET -->
                <div class="mb-3">
                    <label for="harga_het" class="form-label">Harga HET</label>
                    <input type="number" id="harga_het" name="harga_het" value="{{ $data->harga_het }}" readonly class="form-control form-control-readonly">
                </div>

                <!-- Harga Kemarin -->
                <div class="mb-3">
                    <label for="harga_kemarin" class="form-label">Harga Kemarin</label>
                    <input type="number" id="harga_kemarin" name="harga_kemarin" value="{{ $data->harga_kemarin }}" readonly class="form-control form-control-readonly">
                </div>

                <!-- Harga Hari Ini -->
                <div class="mb-3">
                    <label for="harga_hari_ini" class="form-label">Harga Hari Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="harga_hari_ini" name="harga_hari_ini" value="{{ $data->harga_hari_ini }}" min="0" required class="form-control" placeholder="Masukkan harga hari ini">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn-update">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard_operator.data_pangan') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Batal / Kembali
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        let isSubmitting = false;

        document.getElementById('editForm').addEventListener('submit', function (event) {
            event.preventDefault();

            if (isSubmitting) return;

            const form = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Simpan perubahan?',
                text: 'Data harga komoditas akan diperbarui dan dikirim kembali untuk diverifikasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Periksa kembali',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                isSubmitting = true;

                Swal.fire({
                    title: 'Menyimpan Perubahan...',
                    text: 'Mohon tunggu, data sedang disimpan.',
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
    </script>
</body>
</html>
