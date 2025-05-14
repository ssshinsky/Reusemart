<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Organisasi;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // 1. Cek Pegawai
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai && Hash::check($password, $pegawai->password)) {
            Auth::guard('pegawai')->login($pegawai);
            return match ($pegawai->id_role) {
                1 => redirect('/owner'),    
                2 => redirect('/admin'),    
                3 => redirect('/cs'),       
                4 => redirect('/gudang'),
                5 => redirect('/kurir'),
                6 => redirect('/hunter'),
                default => redirect('/pegawai'),
            };
        }

        // 2. Cek Penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip && Hash::check($password, $penitip->password)) {
            Auth::guard('penitip')->login($penitip);
            return redirect('/dashboard-penitip');
        }

        // 3. Cek Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli && Hash::check($password, $pembeli->password)) {
            Auth::guard('pembeli')->login($pembeli);
            return redirect('/dashboard-pembeli');
        }

        // 4. Cek Organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi && Hash::check($password, $organisasi->password)) {
            Auth::guard('organisasi')->login($organisasi);
            return redirect('/dashboard-organisasi');
        }

        return back()->with('error', 'Email atau password salah.');
    }
}
