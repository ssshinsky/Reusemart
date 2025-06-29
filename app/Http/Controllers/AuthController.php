<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Organisasi;
use App\Models\FcmToken;

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
            return match ((int) $pegawai->id_role) {
                1 => redirect('/owner/dashboard'),    
                2 => redirect('/admin'),    
                3 => redirect('/cs/dashboard'),       
                4 => redirect('/gudang/dashboard'),
                5 => redirect('/kurir'),
                6 => redirect('/hunter'),
                default => redirect('/'),
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
            return redirect('/penitip/myproduct');
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
            return redirect('/organisasi');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function loginapi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $email = $request->email;
        $password = $request->password;
        
        // 2. Cek Penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip && Hash::check($password, $penitip->password)) {
            $token = $penitip->createToken('mobile_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $penitip->id_penitip,
                    'nama' => $penitip->nama_penitip,
                    'email' => $penitip->email_penitip,
                ],
                'role' => 'penitip',
            ]);
        }

        // 3. Cek Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli && Hash::check($password, $pembeli->password)) {
            $token = $pembeli->createToken('mobile_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $pembeli->id_pembeli,
                    'nama' => $pembeli->nama_pembeli,
                    'email' => $pembeli->email_pembeli,
                    'poin' => $pembeli->poin_pembeli ?? 0,
                ],
                'role' => 'pembeli',
            ]);
        }
        
        // 1. Cek Pegawai
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if (!$pegawai || !Hash::check($password, $pegawai->password)) {
            \Log::error('Login: Invalid credentials', ['email' => $email]);
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau kata sandi salah'
            ], 401);
        }else{
            // // Buat token Sanctum
            $token = $pegawai->createToken('mobile_token', ['guard:api_pegawai'])->plainTextToken;

            // Tentukan role berdasarkan id_role
            $role = match ($pegawai->id_role) {
                5 => 'kurir',
                1 => 'admin',
                2 => 'owner',
                6 => 'hunter',
                default => 'Pegawai',
            };

            \Log::info('Login: Success', [
                'id_pegawai' => $pegawai->id_pegawai,
                'id_role' => $pegawai->id_role,
                'role' => $role,
                'token' => $token,
            ]);

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $pegawai->id_pegawai,
                    'nama' => $pegawai->nama_pegawai,
                    'email' => $pegawai->email_pegawai,
                    'id_role' => $pegawai->id_role,
                ],
                'role' => $role,
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Email atau password salah yaa'], 401);
    }


    public function logoutapi(Request $request)
    {
        $request->user()->tokens()->delete(); // Menghapus semua token
        return response()->json(['status' => 'success', 'message' => 'Berhasil logout']);
    }

    public function profileKurir(Request $request)
    {
        $user = $request->user();
        if ($user instanceof Pegawai && $user->id_role == 5) {
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }
}

