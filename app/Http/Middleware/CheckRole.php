<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Memeriksa apakah user sudah login
     * dan memiliki role yang sesuai.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {

        // Belum login
        if (!Auth::check()) {
            return redirect('/')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // User sudah login tetapi role tidak sesuai
        if (Auth::user()->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Role sesuai
        return $next($request);
    }
}