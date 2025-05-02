<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PembeliController extends Controller
{
    // Menampilkan daftar semua pembeli
    public function index()
    {
        $pembelis = Pembeli::all();
        return response()->json($pembelis);
    }

    // Menampilkan pembeli berdasarkan ID
    public function show($id)
    {
        $pembeli = Pembeli::find($id);
        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli not found'], 404);
        }
        return response()->json($pembeli);
    }

    // Menambahkan pembeli baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string',
            'email_pembeli' => 'required|email|unique:pembeli,email_pembeli',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'password' => 'required|string|min:6',
            'profil_pict' => 'nullable|string',
        ]);

        $pembeli = Pembeli::create([
            'nama_pembeli' => $request->nama_pembeli,
            'email_pembeli' => $request->email_pembeli,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon,
            'password' => Hash::make($request->password),
            'profil_pict' => $request->profil_pict,
        ]);

        return response()->json($pembeli, 201);
    }

    // Mengupdate pembeli berdasarkan ID
    public function update(Request $request, $id)
    {
        $pembeli = Pembeli::find($id);
        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli not found'], 404);
        }

        $request->validate([
            'nama_pembeli' => 'nullable|string',
            'email_pembeli' => 'nullable|email|unique:pembeli,email_pembeli,' . $id . ',id_pembeli',
            'tanggal_lahir' => 'nullable|date',
            'nomor_telepon' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'profil_pict' => 'nullable|string',
        ]);

        $pembeli->update([
            'nama_pembeli' => $request->nama_pembeli ?? $pembeli->nama_pembeli,
            'email_pembeli' => $request->email_pembeli ?? $pembeli->email_pembeli,
            'tanggal_lahir' => $request->tanggal_lahir ?? $pembeli->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon ?? $pembeli->nomor_telepon,
            'password' => $request->password ? Hash::make($request->password) : $pembeli->password,
            'profil_pict' => $request->profil_pict ?? $pembeli->profil_pict,
        ]);

        return response()->json($pembeli);
    }

    // Menghapus pembeli berdasarkan ID
    public function destroy($id)
    {
        $pembeli = Pembeli::find($id);
        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli not found'], 404);
        }

        $pembeli->delete();
        return response()->json(['message' => 'Pembeli deleted successfully']);
    }
}
