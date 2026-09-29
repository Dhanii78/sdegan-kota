@php
    $isOperator = $role === 'operator';
    $primaryRoute = $isOperator
        ? route('dashboard_operator.input_data')
        : route('dashboard_verifikator.verify_data');
    $historyRoute = $isOperator
        ? route('dashboard_operator.data_pangan')
        : route('rekap.verifikator');
    $dashboardRoute = $isOperator
        ? route('dashboard_operator.index')
        : route('dashboard_verifikator.index');
    $rekapRoute = $isOperator
        ? route('rekap.operator')
        : route('rekap.verifikator');
@endphp

<div class="role-mobile-chrome" data-role-mobile-chrome>
    <header class="role-app-header">
        <a href="{{ $primaryRoute }}" class="role-app-brand" aria-label="Beranda S-DEGAN">
            <img src="{{ asset('images/logo.png') }}" alt="Lambang Kota Kediri">
            <span>S-DEGAN</span>
        </a>
        <button type="button" class="role-profile-trigger" aria-expanded="false" aria-controls="role-profile-sheet">
            <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
            <span class="visually-hidden">Buka menu akun</span>
        </button>
    </header>

    <section id="role-profile-sheet" class="role-profile-sheet" aria-hidden="true">
        <div class="role-profile-sheet__identity">
            <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
            <div>
                <strong>{{ Auth::user()->nama ?? 'Pengguna S-DEGAN' }}</strong>
                <span>{{ $isOperator ? 'Operator' : 'Verifikator' }}</span>
            </div>
        </div>
        <a href="{{ $dashboardRoute }}"><i class="fas fa-chart-line" aria-hidden="true"></i> Dashboard analitik</a>
        <a href="{{ $rekapRoute }}"><i class="fas fa-file-export" aria-hidden="true"></i> Rekap data</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Keluar</button>
        </form>
    </section>

    <nav class="role-bottom-nav" aria-label="Navigasi utama">
        <a href="{{ $primaryRoute }}" class="{{ $active === 'primary' ? 'is-active' : '' }}">
            <i class="{{ $isOperator ? 'fas fa-border-all' : 'fas fa-check-double' }}" aria-hidden="true"></i>
            <span>{{ $isOperator ? 'Input Data' : 'Validasi' }}</span>
        </a>
        <a href="{{ $historyRoute }}" class="{{ $active === 'history' ? 'is-active' : '' }}">
            <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
            <span>{{ $isOperator ? 'Riwayat' : 'Rekap' }}</span>
        </a>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trigger = document.querySelector('.role-profile-trigger');
        const sheet = document.getElementById('role-profile-sheet');

        if (!trigger || !sheet) return;

        trigger.addEventListener('click', function () {
            const isOpen = sheet.classList.toggle('is-open');
            sheet.setAttribute('aria-hidden', String(!isOpen));
            trigger.setAttribute('aria-expanded', String(isOpen));
        });
    });
</script>

@include('components.notification-handler')
