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

    public function logout(Request $request)
    {
        foreach (['pegawai', 'penitip', 'pembeli', 'organisasi'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                session()->forget('role');
                session()->forget('user');
            }
        }

        // Hapus semua session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // 1. Cek Pegawai
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai && Hash::check($password, $pegawai->password)) {
            Auth::guard('pegawai')->login($pegawai);
            session([
                'user' => [
                    'id' => $pegawai->id_pegawai,
                    'nama' => $pegawai->nama_pegawai,
                    'email' => $pegawai->email_pegawai,
                ],
                'role' => 'admin',
            ]);
            return match ($pegawai->id_role) {
                1 => redirect('/owner/dashboard'),    
                2 => redirect('/admin'),    
                3 => redirect('/cs/dashboard'),       
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
            session([
                'user' => [
                    'id' => $penitip->id_penitip,
                    'nama' => $penitip->nama_penitip,
                    'email' => $penitip->email_penitip,
                ],
                'role' => 'penitip',
            ]);
            return redirect('/');
        }

        // 3. Cek Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli && Hash::check($password, $pembeli->password)) {
            Auth::guard('pembeli')->login($pembeli);
            session([
                'user' => [
                    'id' => $pembeli->id_pembeli,
                    'nama' => $pembeli->nama_pembeli,
                    'email' => $pembeli->email_pembeli,
                ],
                'role' => 'pembeli',
            ]);
            return redirect()->route('/');
        }

        // 4. Cek Organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi && Hash::check($password, $organisasi->password)) {
            Auth::guard('organisasi')->login($organisasi);
            session([
                'user' => [
                    'id' => $organisasi->id_organisasi,
                    'nama' => $organisasi->nama_organisasi,
                    'email' => $organisasi->email_organisasi,
                ],
                'role' => 'organisasi',
            ]);
            return redirect('/');
        }

        return back()->with('error', 'Email atau password salah.');
    }
}

