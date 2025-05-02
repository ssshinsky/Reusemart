<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenitipController extends Controller
{
    // Menampilkan daftar semua penitip
    public function index()
    {
        $penitips = Penitip::all();
        return response()->json($penitips);
    }

    // Menampilkan penitip berdasarkan ID
    public function show($id)
    {
        $penitip = Penitip::find($id);
        if (!$penitip) {
            return response()->json(['message' => 'Penitip not found'], 404);
        }
        return response()->json($penitip);
    }

    // Menambahkan penitip baru
    public function store(Request $request)
    {
        $request->validate([
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip',
            'password' => 'required|string|min:6',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
            'rata_rating' => 'required|numeric',
            'status_penitip' => 'required|string',
            'saldo_penitip' => 'required|numeric',
            'profil_pict' => 'nullable|string',
            'badge' => 'nullable|boolean',
        ]);

        $penitip = Penitip::create([
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'rata_rating' => $request->rata_rating,
            'status_penitip' => $request->status_penitip,
            'saldo_penitip' => $request->saldo_penitip,
            'profil_pict' => $request->profil_pict,
            'badge' => $request->badge,
        ]);

        return response()->json($penitip, 201);
    }

    // Mengupdate penitip berdasarkan ID
    public function update(Request $request, $id)
    {
        $penitip = Penitip::find($id);
        if (!$penitip) {
            return response()->json(['message' => 'Penitip not found'], 404);
        }

        $request->validate([
            'nik_penitip' => 'nullable|string|unique:penitip,nik_penitip,' . $id . ',id_penitip',
            'nama_penitip' => 'nullable|string',
            'email_penitip' => 'nullable|email|unique:penitip,email_penitip,' . $id . ',id_penitip',
            'password' => 'nullable|string|min:6',
            'no_telp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rata_rating' => 'nullable|numeric',
            'status_penitip' => 'nullable|string',
            'saldo_penitip' => 'nullable|numeric',
            'profil_pict' => 'nullable|string',
            'badge' => 'nullable|boolean',
        ]);

        $penitip->update([
            'nik_penitip' => $request->nik_penitip ?? $penitip->nik_penitip,
            'nama_penitip' => $request->nama_penitip ?? $penitip->nama_penitip,
            'email_penitip' => $request->email_penitip ?? $penitip->email_penitip,
            'password' => $request->password ? Hash::make($request->password) : $penitip->password,
            'no_telp' => $request->no_telp ?? $penitip->no_telp,
            'alamat' => $request->alamat ?? $penitip->alamat,
            'rata_rating' => $request->rata_rating ?? $penitip->rata_rating,
            'status_penitip' => $request->status_penitip ?? $penitip->status_penitip,
            'saldo_penitip' => $request->saldo_penitip ?? $penitip->saldo_penitip,
            'profil_pict' => $request->profil_pict ?? $penitip->profil_pict,
            'badge' => $request->badge ?? $penitip->badge,
        ]);

        return response()->json($penitip);
    }

    // Menghapus penitip berdasarkan ID
    public function destroy($id)
    {
        $penitip = Penitip::find($id);
        if (!$penitip) {
            return response()->json(['message' => 'Penitip not found'], 404);
        }

        $penitip->delete();
        return response()->json(['message' => 'Penitip deleted successfully']);
    }
}
