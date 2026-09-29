<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="login-container">
        <h2>Atur Ulang Password</h2>
        <form action="{{ route('forgot.password.post') }}" method="POST">
            @csrf
            <div class="input-group-forgot">
                <label for="id">Masukkan ID Anda</label>
                <input type="text" id="id" name="id" placeholder="Masukkan ID" required>
            </div>
            <div class="input-group-forgot">
                <label for="new_password">Password Baru</label>
                <div class="password-wrapper">
                    <input type="password" id="new_password" name="new_password" placeholder="Masukkan Password Baru" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        👁️
                    </span>
                </div>
            </div>
            <div class="input-group-forgot">
                <input type="submit" value="Simpan Password Baru">
            </div>
        </form>

        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('new_password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
