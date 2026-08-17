<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->is_banned) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh Administrator: ' . ($user->banned_reason ?? 'Pelanggaran ketentuan layanan.'));
        }

        if (!$user->isAdmin()) {
            abort(403, 'Akses Ditolak: Halaman ini hanya dapat diakses oleh Superadministrator PortoFinance.');
        }

        return $next($request);
    }
}
