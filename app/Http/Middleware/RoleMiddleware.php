<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') atau middleware('role:admin,kasir')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Validasi Role: Cek apakah role user ada dalam list role yang diizinkan (misal: 'admin' atau 'kasir')
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Tampilkan pesan jelas jika akses ditolak (bukan blank page)
        $allowedRoles = implode(' atau ', $roles);
        abort(403, "Akses Ditolak: Halaman ini hanya bisa diakses oleh role {$allowedRoles}. Role Anda saat ini adalah {$userRole}.");
    }
}
