<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiPegawaiRoleMiddleware
{
    public function handle(Request $request, Closure $next, $roleId)
    {
        \Log::info('ApiPegawaiRoleMiddleware: Checking auth', [
            'guard' => 'api_pegawai',
            'is_authenticated' => Auth::guard('api_pegawai')->check(),
            'user' => Auth::guard('api_pegawai')->user() ? Auth::guard('api_pegawai')->user()->toArray() : null,
            'roleId' => $roleId,
        ]);

        if (Auth::guard('api_pegawai')->check()) {
            $pegawai = Auth::guard('api_pegawai')->user();
            if ($pegawai->id_role == $roleId) {
                \Log::info('ApiPegawaiRoleMiddleware: Access granted', [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'id_role' => $pegawai->id_role,
                ]);
                return $next($request);
            }
            \Log::error('ApiPegawaiRoleMiddleware: Role mismatch', [
                'id_pegawai' => $pegawai->id_pegawai,
                'id_role' => $pegawai->id_role,
                'required_role' => $roleId,
            ]);
        } else {
            \Log::error('ApiPegawaiRoleMiddleware: Not authenticated with guard api_pegawai');
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
    }
}