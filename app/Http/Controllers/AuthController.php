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
                    'nama' => $pegawai->nama_pegawai
                ],
                'role' => match ($pegawai->id_role) {
                    1 => 'owner', 2 => 'admin', 3 => 'cs', 4 => 'gudang', 5 => 'kurir', 6 => 'hunter'
                }
            ]);
            return match ($pegawai->id_role) {
                1 => redirect('/owner/dashboard'),
                2 => redirect('/admin'),
                3 => redirect('/cs/dashboard'),
                4 => redirect('/gudang/dashboard'),
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
            $request->session()->regenerate();
            return redirect('/penitip/myProduct');
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
                    'poin_pembeli' => $pembeli->poin_pembeli ?? 0, // Tambahkan poin_pembeli
                ],
                'role' => 'pembeli',
            ]);
            $request->session()->regenerate();
            return redirect('/');
        }

        // 4. Cek Organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi && Hash::check($password, $organisasi->password)) {
            Auth::guard('organisasi')->login($organisasi);
            return redirect('/organisasi');
        }

        return back()->with('error', 'Email atau password salah.');
    }
}

