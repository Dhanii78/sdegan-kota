@php
    $adminDashRoute = route('dashboard_admin.index');
    $adminRekapRoute = route('rekap.admin');
    $adminDataPanganRoute = route('dashboard_admin.sub_menu.data_pangan');
    $adminPenggunaRoute = route('pengguna.index');
    $adminSumberDataRoute = route('SumberData.index');

    $active = $active ?? 'dashboard';
@endphp

<div class="admin-mobile-chrome" data-admin-mobile-chrome>
    <header class="role-app-header">
        <button type="button" class="admin-hamburger-btn" id="adminHamburgerBtn" aria-expanded="false" aria-label="Buka menu navigasi">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <a href="{{ $adminDashRoute }}" class="role-app-brand" aria-label="Dashboard Admin S-DEGAN">
            <img src="{{ asset('images/logo.png') }}" alt="Lambang Kota Kediri">
            <span>S-DEGAN</span>
        </a>
        <button type="button" class="role-profile-trigger" id="adminProfileTrigger" aria-expanded="false">
            <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
        </button>
    </header>

    {{-- Sidebar Drawer --}}
    <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop"></div>
    <nav id="adminSidebar" class="admin-sidebar" aria-label="Menu navigasi admin">
        <div class="admin-sidebar__header">
            <div class="admin-sidebar__identity">
                <i class="fas fa-user-shield" aria-hidden="true"></i>
                <div>
                    <strong>{{ Auth::user()->nama ?? 'Admin' }}</strong>
                    <span>Administrator</span>
                </div>
            </div>
            <button type="button" class="admin-sidebar__close" id="adminSidebarClose" aria-label="Tutup menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="admin-sidebar__nav">
            <li>
                <a href="{{ $adminDashRoute }}" class="{{ $active === 'dashboard' ? 'is-active' : '' }}">
                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ $adminDataPanganRoute }}" class="{{ $active === 'data_pangan' ? 'is-active' : '' }}">
                    <i class="fas fa-database" aria-hidden="true"></i>
                    Data Pangan
                </a>
            </li>
            <li>
                <a href="{{ $adminRekapRoute }}" class="{{ $active === 'rekap' ? 'is-active' : '' }}">
                    <i class="fas fa-file-export" aria-hidden="true"></i>
                    Rekap Data
                </a>
            </li>
            <li>
                <a href="{{ $adminPenggunaRoute }}" class="{{ $active === 'pengguna' ? 'is-active' : '' }}">
                    <i class="fas fa-users" aria-hidden="true"></i>
                    Pengguna
                </a>
            </li>
            <li>
                <a href="{{ $adminSumberDataRoute }}" class="{{ $active === 'sumber_data' ? 'is-active' : '' }}">
                    <i class="fas fa-book" aria-hidden="true"></i>
                    Regulasi / Sumber Data
                </a>
            </li>
        </ul>
        <div class="admin-sidebar__footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="admin-sidebar__logout">
                    <i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    {{-- Profile Dropdown --}}
    <section id="adminProfileSheet" class="role-profile-sheet" aria-hidden="true">
        <div class="role-profile-sheet__identity">
            <i class="fas fa-user-shield" aria-hidden="true"></i>
            <div>
                <strong>{{ Auth::user()->nama ?? 'Admin' }}</strong>
                <span>Administrator</span>
            </div>
        </div>
        <a href="{{ $adminDashRoute }}"><i class="fas fa-chart-line" aria-hidden="true"></i> Dashboard</a>
        <a href="{{ $adminRekapRoute }}"><i class="fas fa-file-export" aria-hidden="true"></i> Rekap data</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Keluar</button>
        </form>
    </section>
</div>

<style>
/* ===================== ADMIN MOBILE CHROME ===================== */
.admin-mobile-chrome { display: none; }

@media (max-width: 767px) {
    .admin-mobile-chrome { display: block; }

    /* Hamburger button */
    .admin-hamburger-btn {
        display: inline-grid;
        width: 42px;
        height: 42px;
        place-items: center;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--role-ink, #08202b);
        font-size: 1.35rem;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .admin-hamburger-btn:hover { background: rgba(0,0,0,0.06); }

    /* Sidebar backdrop */
    .admin-sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 140;
        background: rgba(0, 0, 0, 0.45);
    }
    .admin-sidebar-backdrop.is-open { display: block; }

    /* Sidebar drawer */
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 150;
        width: min(290px, 80vw);
        height: 100dvh;
        display: flex;
        flex-direction: column;
        background: #fff;
        box-shadow: 4px 0 20px rgba(8, 32, 43, 0.18);
        transform: translateX(-100%);
        transition: transform 220ms ease;
        overflow-y: auto;
    }
    .admin-sidebar.is-open { transform: translateX(0); }

    /* Sidebar header */
    .admin-sidebar__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 16px 14px;
        border-bottom: 1px solid #dce5dc;
        background: linear-gradient(135deg, #056bad 0%, #034e80 100%);
        color: #fff;
    }
    .admin-sidebar__identity {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .admin-sidebar__identity > i { font-size: 2rem; opacity: 0.9; }
    .admin-sidebar__identity strong,
    .admin-sidebar__identity span { display: block; }
    .admin-sidebar__identity strong { font-size: 0.95rem; font-weight: 700; }
    .admin-sidebar__identity span { font-size: 0.75rem; opacity: 0.8; margin-top: 2px; }

    .admin-sidebar__close {
        padding: 8px;
        border: 0;
        border-radius: 6px;
        background: rgba(255,255,255,0.15);
        color: #fff;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .admin-sidebar__close:hover { background: rgba(255,255,255,0.28); }

    /* Nav links */
    .admin-sidebar__nav {
        list-style: none;
        margin: 10px 0;
        padding: 0 8px;
        flex: 1;
    }
    .admin-sidebar__nav li { margin: 2px 0; }
    .admin-sidebar__nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 8px;
        color: #08202b;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .admin-sidebar__nav a i { width: 20px; text-align: center; font-size: 1.1rem; color: #424d42; }
    .admin-sidebar__nav a:hover { background: #e0f3ff; color: #056bad; }
    .admin-sidebar__nav a.is-active {
        background: #056bad;
        color: #fff;
    }
    .admin-sidebar__nav a.is-active i { color: #fff; }

    /* Footer / logout */
    .admin-sidebar__footer {
        padding: 12px;
        border-top: 1px solid #dce5dc;
    }
    .admin-sidebar__logout {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #ffc9c9;
        border-radius: 8px;
        background: #fde8e8;
        color: #c6181d;
        font: inherit;
        font-size: 0.91rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
    }
    .admin-sidebar__logout:hover { background: #fcc; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById('adminHamburgerBtn');
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const closeBtn = document.getElementById('adminSidebarClose');
    const profileTrigger = document.getElementById('adminProfileTrigger');
    const profileSheet = document.getElementById('adminProfileSheet');

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
    }
    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
    }

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // Profile dropdown
    if (profileTrigger && profileSheet) {
        profileTrigger.addEventListener('click', function () {
            const isOpen = profileSheet.classList.toggle('is-open');
            profileSheet.setAttribute('aria-hidden', String(!isOpen));
            profileTrigger.setAttribute('aria-expanded', String(isOpen));
        });
        document.addEventListener('click', function (e) {
            if (!profileTrigger.contains(e.target) && !profileSheet.contains(e.target)) {
                profileSheet.classList.remove('is-open');
                profileSheet.setAttribute('aria-hidden', 'true');
                profileTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>

@include('components.notification-handler')
