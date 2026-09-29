<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Login - Sistem Deteksi Dini Gejolak Harga Pangan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS Khusus Login -->
    <link rel="stylesheet" href="{{ url('css/login.css') }}">
    <!-- CSS Responsif Global untuk HP/Mobile -->
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body>
    <div class="left-side">
        <img src="{{ url('images/login.png') }}" alt="Login Illustration">
        <p class="logo-text">Sistem Deteksi Dini Gejolak Harga Pangan</p>
    </div>
    
    <div class="right-side">
        <div class="login-container">
            <h2>LOGIN</h2>
            
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label for="id">ID</label>
                    <input type="text" id="id" name="id" placeholder="Enter your ID" value="{{ old('id') }}" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper-login">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" aria-label="Toggle Password">
                            <svg class="eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <button type="submit" class="submit-btn">Login</button>
                </div>

                <div class="forgot-password">
                    <a href="{{ route('forgot.password') }}">Lupa password?</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
            </form>

            <div class="additional-links">
                <p>Anda Pengguna Umum?? Klik Tombol ini untuk Mengunjungi Dashboard</p>
                <button type="button" class="dashboard-btn" onclick="goToDashboard()">Dashboard</button>
            </div>
        </div>
    </div>

    <script>
        function goToDashboard() {
            window.location.href = "{{ url('dashboard_umum') }}";
        }

        // Auto-hide alert
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);

        // Toggle Password Visibility
        const togglePasswordBtn = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePasswordBtn.classList.add('active');
                } else {
                    passwordInput.type = 'password';
                    togglePasswordBtn.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>