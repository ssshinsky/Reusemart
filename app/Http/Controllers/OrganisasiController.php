<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrganisasiController extends Controller
{
    // Menampilkan daftar semua organisasi
    public function index()
    {
        $organisasis = Organisasi::all();
        return response()->json($organisasis);
    }

    // Menampilkan organisasi berdasarkan ID
    public function show($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi not found'], 404);
        }
        return response()->json($organisasi);
    }

    // Menambahkan organisasi baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_organisasi' => 'required|string',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
            'email_organisasi' => 'required|email|unique:organisasi,email_organisasi',
            'password' => 'required|string|min:6',
        ]);

        $organisasi = Organisasi::create([
            'nama_organisasi' => $request->nama_organisasi,
            'alamat' => $request->alamat,
            'kontak' => $request->kontak,
            'email_organisasi' => $request->email_organisasi,
            'password' => Hash::make($request->password),
        ]);

        if (!$request->expectsJson()) {
            return redirect('/')->with('success', 'Register organisasi berhasil. Silakan login.');
        }

        return response()->json($organisasi, 201);
    }

    // Mengupdate organisasi berdasarkan ID
    public function update(Request $request, $id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi not found'], 404);
        }

        $request->validate([
            'nama_organisasi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string',
            'email_organisasi' => 'nullable|email|unique:organisasi,email_organisasi,' . $id . ',id_organisasi',
            'password' => 'nullable|string|min:6',
        ]);

        $organisasi->update([
            'nama_organisasi' => $request->nama_organisasi ?? $organisasi->nama_organisasi,
            'alamat' => $request->alamat ?? $organisasi->alamat,
            'kontak' => $request->kontak ?? $organisasi->kontak,
            'email_organisasi' => $request->email_organisasi ?? $organisasi->email_organisasi,
            'password' => $request->password ? Hash::make($request->password) : $organisasi->password,
        ]);

        return response()->json($organisasi);
    }

    // Menghapus organisasi berdasarkan ID
    public function destroy($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi not found'], 404);
        }

        $organisasi->delete();
        return response()->json(['message' => 'Organisasi deleted successfully']);
    }
}
