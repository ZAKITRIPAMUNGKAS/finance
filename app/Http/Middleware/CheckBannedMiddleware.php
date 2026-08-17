<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_banned) {
            $reason = auth()->user()->banned_reason ?? 'Pelanggaran ketentuan penggunaan platform.';
            auth()->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh Administrator: ' . $reason);
        }

        return $next($request);
    }
}
