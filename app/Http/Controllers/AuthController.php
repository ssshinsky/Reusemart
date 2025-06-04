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
        ]);

        $email = $request->email;
        $password = $request->password;

        // 1. Cek Pegawai
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai && Hash::check($password, $pegawai->password)) {
            $token = $pegawai->createToken('mobile_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $pegawai->id_pegawai,
                    'nama' => $pegawai->nama_pegawai,
                    'email' => $pegawai->email_pegawai,
                ],
                'role' => 'pegawai',
            ]);
        }

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

        // 4. Cek Organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi && Hash::check($password, $organisasi->password)) {
            $token = $organisasi->createToken('mobile_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $organisasi->id_organisasi,
                    'nama' => $organisasi->nama_organisasi,
                    'email' => $organisasi->email_organisasi,
                ],
                'role' => 'organisasi',
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Email atau password salah'], 401);
    }


    public function logoutapi(Request $request)
    {
        $request->user()->tokens()->delete(); // Menghapus semua token
        return response()->json(['status' => 'success', 'message' => 'Berhasil logout']);
    }

    public function saveFCMToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|unique:fcm_tokens,fcm_token',
            'role' => 'required|in:pembeli,penitip,hunter,kurir',
            'user_id' => 'required|integer',
            'device_type' => 'required|string',
        ]);

        $userId = $request->user_id;
        $role = $request->role;

        $data = ['fcm_token' => $request->fcm_token, 'device_type' => $request->device_type];

        Log::info("Saving FCM token: Role=$role, User ID=$userId, Token={$request->fcm_token}");

        switch ($role) {
            case 'pembeli':
                $data['id_pembeli'] = $userId;
                break;
            case 'penitip':
                $data['id_penitip'] = $userId;
                break;
            case 'hunter':
                $data['id_hunter'] = $userId;
                break;
            case 'kurir':
                $data['id_kurir'] = $userId;
                break;
        }

        try {
            FcmToken::updateOrCreate(
                ['fcm_token' => $request->fcm_token],
                $data
            );
            Log::info("FCM token saved successfully: Role=$role, User ID=$userId");
        } catch (\Exception $e) {
            Log::error("Failed to save FCM token: {$e->getMessage()}");
            return response()->json(['message' => 'Failed to save FCM token'], 500);
        }

        return response()->json(['message' => 'FCM token saved successfully']);
    }

}

