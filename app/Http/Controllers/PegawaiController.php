<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    // Register Pegawai
    public function register(Request $request)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id_role',
            'nama_pegawai' => 'required|string',
            'alamat_pegawai' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'gaji_pegawai' => 'required|numeric',
            'email_pegawai' => 'required|email|unique:pegawai,email_pegawai',
            'password' => 'required|string|min:6',
            'profil_pict' => 'nullable|integer',
        ]);

        $pegawai = Pegawai::create([
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'alamat_pegawai' => $request->alamat_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon,
            'gaji_pegawai' => $request->gaji_pegawai,
            'email_pegawai' => $request->email_pegawai,
            'password' => Hash::make($request->password),
            'profil_pict' => $request->profil_pict,
        ]);

        return response()->json([
            'pegawai' => $pegawai,
            'message' => 'Pegawai registered successfully'
        ], 201);
    }

    // Login Pegawai
    public function login(Request $request)
    {
        $request->validate([
            'email_pegawai' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $pegawai = Pegawai::where('email_pegawai', $request->email_pegawai)->first();

        if (!$pegawai || !Hash::check($request->password, $pegawai->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $pegawai->createToken('Pegawai Token')->plainTextToken;

        return response()->json([
            'pegawai' => $pegawai,
            'token' => $token
        ]);
    }

    // Logout Pegawai
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json(['message' => 'Not logged in'], 401);
    }

    // Menampilkan daftar semua pegawai
    public function index()
    {
        $pegawais = Pegawai::with('role')->get();
        return response()->json($pegawais);
    }

    // Menampilkan pegawai berdasarkan ID
    public function show($id)
    {
        $pegawai = Pegawai::with('role')->find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found'], 404);
        }
        return response()->json($pegawai);
    }

    // Menambahkan pegawai baru
    public function store(Request $request)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id',
            'nama_pegawai' => 'required|string',
            'alamat_pegawai' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'gaji_pegawai' => 'required|numeric',
            'email_pegawai' => 'required|email|unique:pegawai,email_pegawai',
            'password' => 'required|string|min:6',
            'profil_pict' => 'nullable|integer',
        ]);

        $pegawai = Pegawai::create([
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'alamat_pegawai' => $request->alamat_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon,
            'gaji_pegawai' => $request->gaji_pegawai,
            'email_pegawai' => $request->email_pegawai,
            'password' => Hash::make($request->password),
            'profil_pict' => $request->profil_pict,
        ]);

        return response()->json($pegawai, 201);
    }

    // Mengupdate pegawai berdasarkan ID
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found'], 404);
        }

        $request->validate([
            'id_role' => 'nullable|exists:role,id',
            'nama_pegawai' => 'nullable|string',
            'alamat_pegawai' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'nomor_telepon' => 'nullable|string',
            'gaji_pegawai' => 'nullable|numeric',
            'email_pegawai' => 'nullable|email|unique:pegawai,email_pegawai,' . $id . ',id_pegawai',
            'password' => 'nullable|string|min:6',
            'profil_pict' => 'nullable|integer',
        ]);

        $pegawai->update([
            'id_role' => $request->id_role ?? $pegawai->id_role,
            'nama_pegawai' => $request->nama_pegawai ?? $pegawai->nama_pegawai,
            'alamat_pegawai' => $request->alamat_pegawai ?? $pegawai->alamat_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir ?? $pegawai->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon ?? $pegawai->nomor_telepon,
            'gaji_pegawai' => $request->gaji_pegawai ?? $pegawai->gaji_pegawai,
            'email_pegawai' => $request->email_pegawai ?? $pegawai->email_pegawai,
            'password' => $request->password ? Hash::make($request->password) : $pegawai->password,
            'profil_pict' => $request->profil_pict ?? $pegawai->profil_pict,
        ]);

        return response()->json($pegawai);
    }

    // Menghapus pegawai berdasarkan ID
    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found'], 404);
        }

        $pegawai->delete();
        return response()->json(['message' => 'Pegawai deleted successfully']);
    }
}
