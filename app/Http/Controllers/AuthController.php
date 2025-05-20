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
        session()->flush();
        return redirect('/');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

    // 1. Cek Pembeli
    $pembeli = \App\Models\Pembeli::where('email_pembeli', $request->email)->first();
    if ($pembeli && Hash::check($request->password, $pembeli->password)) {
        session([
            'user' => [
                'id' => $pembeli->id_pembeli,
                'nama' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
            ],
            'role' => 'pembeli',
        ]);
        return redirect('/');
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
        return redirect('/');
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
        return redirect('/');
    }

    // 4. Cek Pegawai (Admin)
    $pegawai = \App\Models\Pegawai::where('email_pegawai', $email)->first();
    if ($pegawai && Hash::check($password, $pegawai->password)) {
        session([
            'user' => $pegawai,
            'role' => 'admin',
        ]);
        return redirect('/admin');
    }

    return back()->withErrors(['login' => 'Email atau password salah']);
}
}
