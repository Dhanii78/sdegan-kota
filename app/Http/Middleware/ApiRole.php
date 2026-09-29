<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRole
{
    /**
     * Memeriksa role user yang sedang login melalui
     * authentication Sanctum.
     *
     * Penggunaan:
     * ->middleware('api.role:operator')
     * ->middleware('api.role:verifikator')
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {

        // Ambil user dari Sanctum
        $user = $request->user();

        // Jika belum login / token tidak valid
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Jika role tidak sesuai
        if ($user->role !== $role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke endpoint ini.',
                'role_user' => $user->role,
                'role_required' => $role,
            ], 403);
        }

        // Role sesuai → lanjutkan request
        return $next($request);
    }
}