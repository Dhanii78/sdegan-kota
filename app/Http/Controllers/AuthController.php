<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Pengguna;

class AuthController extends Controller
{
    /**
     * ============================================================
     * HALAMAN LOGIN
     * ============================================================
     */
    public function showLoginForm()
    {
        return view('login');
    }


    /**
     * ============================================================
     * HALAMAN LUPA PASSWORD
     * ============================================================
     */
    public function showForgotForm()
    {
        return view('forgot_password');
    }


    /**
     * ============================================================
     * PROSES LUPA PASSWORD
     * ============================================================
     */
    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'integer',
                'exists:pengguna,id'
            ],

            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'id.required' => 'ID wajib diisi.',
            'id.integer' => 'ID harus berupa angka.',
            'id.exists' => 'ID pengguna tidak ditemukan.',

            'new_password.required' =>
                'Password baru wajib diisi.',

            'new_password.min' =>
                'Password minimal 8 karakter.',

            'new_password.regex' =>
                'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = Pengguna::find($request->id);

        if (!$user) {
            return redirect('/')
                ->withErrors([
                    'error' => 'Data pengguna tidak ditemukan.'
                ]);
        }

        $user->password = bcrypt($request->new_password);

        /*
        |--------------------------------------------------------------------------
        | Password berlaku selama 3 bulan
        |--------------------------------------------------------------------------
        */

        $user->password_expires_at = now()->addMonths(3);

        $user->save();

        Log::info('Password berhasil diubah', [
            'id' => $user->id,
            'time' => now(),
        ]);

        return redirect('/')
            ->with(
                'success',
                'Password berhasil diubah. Silakan login kembali.'
            );
    }


    /**
     * ============================================================
     * LOGIN
     * ============================================================
     */
    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | RATE LIMITER
        |--------------------------------------------------------------------------
        */

        $loginId = $request->input('id');

        $key = Str::lower((string) $loginId)
            . '|' .
            $request->ip();


        /*
        |--------------------------------------------------------------------------
        | LOG PERCOBAAN LOGIN
        |--------------------------------------------------------------------------
        */

        Log::info('Percobaan login masuk', [
            'id' => $loginId,
            'ip' => $request->ip(),
            'time' => now(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK RATE LIMITER
        |--------------------------------------------------------------------------
        */

        if (RateLimiter::tooManyAttempts($key, 3)) {

            $seconds = RateLimiter::availableIn($key);

            Log::warning(
                'Login diblokir oleh rate limiter',
                [
                    'id' => $loginId,
                    'ip' => $request->ip(),
                    'retry_after_seconds' => $seconds,
                ]
            );

            return back()
                ->withErrors([
                    'error' =>
                        "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI LOGIN
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'id' => [
                'required',
                'integer'
            ],

            'password' => [
                'required',
                'string'
            ],
        ], [
            'id.required' =>
                'ID wajib diisi.',

            'id.integer' =>
                'ID harus berupa angka.',

            'password.required' =>
                'Password wajib diisi.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | CREDENTIALS
        |--------------------------------------------------------------------------
        */

        $credentials = [
            'id' => $request->id,
            'password' => $request->password,
        ];


        /*
        |--------------------------------------------------------------------------
        | PROSES LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt($credentials)) {

            RateLimiter::hit($key, 60);

            Log::warning('Login gagal', [
                'id' => $request->id,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()
                ->withErrors([
                    'error' =>
                        'ID atau password yang Anda masukkan tidak sesuai.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN BERHASIL
        |--------------------------------------------------------------------------
        */

        RateLimiter::clear($key);

        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | LOG LOGIN BERHASIL
        |--------------------------------------------------------------------------
        */

        Log::info('Login berhasil', [
            'id' => $user->id,
            'nama' => $user->name ?? $user->nama ?? null,
            'role' => $user->role,
            'ip' => $request->ip(),
            'time' => now(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK PASSWORD EXPIRED
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->password_expires_at) &&
            $user->password_expires_at !== null &&
            now()->greaterThan($user->password_expires_at)
        ) {

            Log::warning(
                'User login dengan password kedaluwarsa',
                [
                    'id' => $user->id,
                    'role' => $user->role,
                    'expires_at' =>
                        $user->password_expires_at,
                ]
            );

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/')
                ->withErrors([
                    'error' =>
                        'Password Anda telah kedaluwarsa. Silakan ubah password terlebih dahulu.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        switch (strtolower($user->role)) {

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */

            case 'admin':

                Log::info(
                    'Redirect user ke dashboard admin',
                    [
                        'id' => $user->id
                    ]
                );

                return redirect()
                    ->route('dashboard_admin.index');


            /*
            |--------------------------------------------------------------------------
            | OPERATOR
            |--------------------------------------------------------------------------
            */

            case 'operator':

                Log::info(
                    'Redirect user ke dashboard operator',
                    [
                        'id' => $user->id
                    ]
                );

                return redirect()
                    ->route('dashboard_operator.index');


            /*
            |--------------------------------------------------------------------------
            | VERIFIKATOR
            |--------------------------------------------------------------------------
            */

            case 'verifikator':

                Log::info(
                    'Redirect user ke dashboard verifikator',
                    [
                        'id' => $user->id
                    ]
                );

                return redirect()
                    ->route('dashboard_verifikator.index');


            /*
            |--------------------------------------------------------------------------
            | ROLE TIDAK DIKENAL
            |--------------------------------------------------------------------------
            */

            default:

                Log::error(
                    'Role tidak dikenali saat login',
                    [
                        'id' => $user->id,
                        'role' => $user->role,
                    ]
                );

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/')
                    ->withErrors([
                        'error' =>
                            'Role pengguna tidak dikenali.'
                    ]);
        }
    }


    /**
     * ============================================================
     * LOGOUT
     * ============================================================
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logout', [
                'id' => $user->id,
                'role' => $user->role,
                'time' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with(
                'success',
                'Anda berhasil logout.'
            );
    }
}