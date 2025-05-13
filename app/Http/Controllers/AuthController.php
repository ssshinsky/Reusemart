<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = $request->email;
    $password = $request->password;

    // 1. Cek Pembeli
    $pembeli = \App\Models\Pembeli::where('email_pembeli', $email)->first();
    if ($pembeli && Hash::check($password, $pembeli->password)) {
        session([
            'user' => [
                'id' => $pembeli->id_pembeli,
                'nama' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
            ],
            'role' => 'pembeli',
        ]);
        return redirect('/pembeli/profile');
    }

    // 2. Cek Penitip
    $penitip = \App\Models\Penitip::where('email_penitip', $request->email)->first();
    if ($penitip && Hash::check($request->password, $penitip->password)) {
        session([
            'user' => [
                'id' => $penitip->id_penitip,
                'nama' => $penitip->nama_penitip,
                'email' => $penitip->email_penitip,
            ],
            'role' => 'penitip',
        ]);
        return redirect('/penitip/profile');
    }


    // 3. Cek Organisasi
    $organisasi = \App\Models\Organisasi::where('email_organisasi', $email)->first();
    if ($organisasi && Hash::check($password, $organisasi->password)) {
        session([
            'user' => [
                'id' => $organisasi->id_organisasi,
                'nama' => $organisasi->nama_organisasi,
                'email' => $organisasi->email_organisasi,
            ],
            'role' => 'organisasi',
        ]);
        return redirect('/organisasi/profile');
    }

    // 4. Cek Pegawai (Admin)
    $pegawai = \App\Models\Pegawai::where('email_pegawai', $email)->first();
    if ($pegawai && Hash::check($password, $pegawai->password)) {
        session([
            'user' => $pegawai,
            'role' => 'admin',
        ]);
        return redirect('/admin/dashboard');
    }

    return back()->withErrors(['login' => 'Email atau password salah']);
}
}