<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect sesuai role saat sudah login
                switch ($guard) {
                    case 'pegawai':
                        $roleId = Auth::guard($guard)->user()->id_role;
                        return match ($roleId) {
                            1 => redirect('/owner/dashboard'),
                            2 => redirect('/admin'),
                            3 => redirect('/cs/dashboard'),
                            4 => redirect('/gudang'),
                            5 => redirect('/kurir'),
                            6 => redirect('/hunter'),
                            // default => redirect('/pegawai'),
                        };
                    case 'penitip':
                        return redirect('/dashboard-penitip');
                    case 'pembeli':
                        return redirect('/pembeli/profile');
                    case 'organisasi':
                        return redirect('/dashboard-organisasi');
                }
            }
        }

        return $next($request);
    }
}
