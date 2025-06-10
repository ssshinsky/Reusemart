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
            }
        }

        // Log isi session SEBELUM invalidate (jika kamu mau debug)
        logger('User before invalidate: ' . json_encode(session('user')));
        logger('Role before invalidate: ' . json_encode(session('role')));

        session()->forget(['user', 'role']);
        $request->session()->invalidate();        // Ini menghapus semua data session
        $request->session()->regenerateToken();   // Regenerasi CSRF token

        return redirect('/')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
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
            $request->session()->regenerate();
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

    public function loginapi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Coba login sebagai Pegawai
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai) {
            // Perhatian: Pastikan password di database ter-hash.
            if (Hash::check($password, $pegawai->password)) {
                if ($pegawai->is_active == 0) { // Asumsi 0 = Non Active
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun pegawai tidak aktif. Silakan hubungi administrator.',
                    ], 401);
                }
                $token = $pegawai->createToken('mobile_token')->plainTextToken;
                // Pastikan Anda sudah meng-import Role: use App\Models\Role;
                $roleName = \App\Models\Role::find($pegawai->id_role)->nama_role ?? 'unknown';
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user_type' => $roleName, // Kirim nama role (owner, admin, cs, dll)
                    'user' => $pegawai->toArray(), // Kirim data user lengkap
                ], 200);
            }
        }

        // Coba login sebagai Penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip) {
            if (Hash::check($password, $penitip->password)) {
                if ($penitip->status_penitip == 'Non Active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun penitip tidak aktif. Silakan hubungi administrator.',
                    ], 401);
                }
                $token = $penitip->createToken('mobile_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user_type' => 'penitip',
                    'user' => $penitip->toArray(),
                ], 200);
            }
        }

        // Coba login sebagai Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli) {
            if (Hash::check($password, $pembeli->password)) {
                if ($pembeli->status_pembeli == 'Non Active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun pembeli tidak aktif. Silakan hubungi administrator.',
                    ], 401);
                }
                $token = $pembeli->createToken('mobile_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user_type' => 'pembeli',
                    'user' => $pembeli->toArray(),
                ], 200);
            }
        }

        // Coba login sebagai Organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi) {
            if (Hash::check($password, $organisasi->password)) {
                if ($organisasi->status_organisasi == 'Non Active') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun organisasi tidak aktif. Silakan hubungi administrator.',
                    ], 401);
                }
                $token = $organisasi->createToken('mobile_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                    'user_type' => 'organisasi',
                    'user' => $organisasi->toArray(),
                ], 200);
            }
        }

        // Jika tidak ada user yang cocok atau password salah
        return response()->json(['success' => false, 'message' => 'Email atau password salah.'], 401);
    }

    public function logoutapi(Request $request)
    {
        $request->user()->tokens()->delete(); // Menghapus semua token
        return response()->json(['status' => 'success', 'message' => 'Berhasil logout']);
    }

}

