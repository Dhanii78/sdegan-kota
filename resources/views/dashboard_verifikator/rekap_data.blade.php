<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Rekap Data - S-DEGAN</title>
    <link rel="stylesheet" href="{{ asset('css/rekap_data.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role_mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="{{ asset('js/mobile-nav.js') }}"></script>
</head>
<body class="role-portal">
    @include('components.role-mobile-chrome', ['role' => 'verifikator', 'active' => 'history'])
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <p class="logo-text">S-DEGAN</p>
                <p class="logo-text">Sistem Deteksi Dini Gejolak Harga Pangan</p>
            </div>
            <button class="toggle-btn">☰</button>
            <div class="menu">
                @if(Auth::user()->role === 'operator')
                    <a href="{{ route('dashboard_operator.index') }}"><i class="fas fa-home"></i> Dashboard</a>
                @elseif(Auth::user()->role === 'verifikator')
                    <a href="{{ route('dashboard_verifikator.index') }}"><i class="fas fa-home"></i> Dashboard</a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('dashboard_admin.index') }}"><i class="fas fa-home"></i> Dashboard</a>
                @endif

                @if(Auth::user()->role === 'operator')
                    <a href="{{ route('dashboard_operator.data_pangan') }}"><i class="fas fa-keyboard"></i> Data Pangan</a>
                @endif

                @if(Auth::user()->role === 'verifikator')
                    <a href="{{ route('dashboard_verifikator.verify_data') }}"><i class="fas fa-check-circle"></i> Verifikasi</a>
                @endif

                @if(Auth::user()->role === 'operator')
                    <a href="{{ route('rekap.operator') }}"><i class="fas fa-chart-line"></i> Rekap Data</a>
                @elseif(Auth::user()->role === 'verifikator')
                    <a href="{{ route('rekap.verifikator') }}"><i class="fas fa-chart-line"></i> Rekap Data</a>
                @else
                    <a href="{{ route('rekap.admin') }}"><i class="fas fa-chart-line"></i> Rekap Data</a>
                @endif

                <a href="javascript:void(0)" id="profile-link"><i class="fas fa-user-circle"></i> Profil</a>
            </div>
            <div class="datetime-op">
                <span id="current-time"></span>
                <hr>
                <span id="current-date"></span>
            </div>
        </div>

        <div class="header-text">
            <p>Rekap Data Pangan</p>
        </div>

        <div class="main-content">
            @if(Auth::user()->role === 'operator')
                <a href="{{ route('export.rekap_op') }}" class="btn btn-excel"><i class="fas fa-file-excel"></i> Export Harga</a>
                <a href="{{ route('export.het_op') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export HET</a>
            @elseif(Auth::user()->role === 'verifikator')
                <a href="{{ route('export.rekap_veri') }}" class="btn btn-excel"><i class="fas fa-file-excel"></i> Export Harga</a>
                <a href="{{ route('export.het_veri') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export HET</a>
            @else
                <a href="{{ route('export.rekap') }}" class="btn btn-excel"><i class="fas fa-file-excel"></i> Export Harga</a>
                <a href="{{ route('export.het') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export HET</a>
            @endif

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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekap as $data)
                                    <tr>
                                        @foreach ($rekap_columns as $column)
                                            <td>{{ $column === 'tanggal' ? $data->$column : number_format($data->$column, 0, ',', '.') }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ count($rekap_columns) }}" style="text-align: center;">Tidak ada data harga.</td></tr>
                                @endforelse
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
                                @forelse ($het as $data)
                                    <tr>
                                        @foreach ($het_columns as $column)
                                            <td>{{ $column === 'tanggal' ? $data->$column : number_format($data->$column, 0, ',', '.') }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ count($het_columns) }}" style="text-align: center;">Tidak ada data HET.</td></tr>
                                @endforelse
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

    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Profil User</h2>
            <div class="profile-details">
                <div class="profile-info">
                    <p>Nama: {{ Auth::user()->nama ?? Auth::user()->name }}</p>
                    <p>Role: {{ Auth::user()->role }}</p>
                    <p>Email: {{ Auth::user()->email }}</p>
                    <a href="#" class="btn-logout" onclick="handleLogout(event)">Logout</a>
                </div>
                <div class="profile-logo"><img src="{{ asset('images/profileLogo.png') }}" alt="Profile Logo"></div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <script>
        // Copy paste skrip JS Anda dari file sebelumnya ke sini
        function updateDateTime() {
            const now = new Date();
            document.getElementById('current-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('current-time').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        const modal = document.getElementById('profileModal');
        const profileBtn = document.getElementById('profile-link');
        const closeBtn = document.querySelector('#profileModal .close');

        function openModal(e) {
            if (e) e.preventDefault();
            if (!modal) return;
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
                modal.classList.remove('hide');
            }, 10);
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.remove('show');
            modal.classList.add('hide');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        if (profileBtn) profileBtn.onclick = openModal;
        if (closeBtn) closeBtn.onclick = closeModal;
        window.onclick = (e) => { if (e.target === modal) closeModal(); };
        
        function handleLogout(e) { if(e) e.preventDefault(); const f = document.getElementById('logout-form'); if(f) f.submit(); else window.location.href = "{{ route('logout') }}"; }
    </script>
</body>
</html>
