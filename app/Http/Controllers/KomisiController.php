<?php

namespace App\Http\Controllers;

use App\Models\Komisi;
use Illuminate\Http\Request;

class KomisiController extends Controller
{
    // Menampilkan daftar semua komisi
    public function index()
    {
        $komisis = Komisi::all();
        return response()->json($komisis);
    }

    // Menampilkan komisi berdasarkan ID
    public function show($id)
    {
        $komisi = Komisi::find($id);
        if (!$komisi) {
            return response()->json(['message' => 'Komisi not found'], 404);
        }
        return response()->json($komisi);
    }

    // Menambahkan komisi baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'nullable|exists:transaksi_pembelian,id_pembelian',
            'id_penitip' => 'nullable|exists:penitip,id_penitip',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_owner' => 'nullable|exists:pegawai,id_pegawai',
            'komisi_hunter' => 'nullable|numeric',
            'komisi_penitip' => 'nullable|numeric',
            'komisi_reusemart' => 'nullable|numeric',
            'bonus_penitip_terjual_cepat' => 'nullable|numeric'
        ]);

        $komisi = Komisi::create([
            'id_pembelian' => $request->id_pembelian,
            'id_penitip' => $request->id_penitip,
            'id_hunter' => $request->id_hunter,
            'id_owner' => $request->id_owner,
            'komisi_hunter' => $request->komisi_hunter,
            'komisi_penitip' => $request->komisi_penitip,
            'komisi_reusemart' => $request->komisi_reusemart,
            'bonus_penitip_terjual_cepat' => $request->bonusPenitipTerjualCepat,
        ]);

        return response()->json($komisi, 201);
    }

    // Mengupdate komisi berdasarkan ID
    public function update(Request $request, $id)
    {
        $komisi = Komisi::find($id);
        if (!$komisi) {
            return response()->json(['message' => 'Komisi not found'], 404);
        }

        $request->validate([
            'id_pembelian' => 'nullable|exists:transaksi_pembelian,id_pembelian',
            'id_penitip' => 'nullable|exists:penitip,id_penitip',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_owner' => 'nullable|exists:pegawai,id_pegawai',
            'komisi_hunter' => 'nullable|numeric',
            'komisi_penitip' => 'nullable|numeric',
            'komisi_reusemart' => 'nullable|numeric',
        ]);

        $komisi->update([
            'id_pembelian' => $request->id_pembelian,
            'id_penitip' => $request->id_penitip,
            'id_hunter' => $request->id_hunter,
            'id_owner' => $request->id_owner,
            'komisi_hunter' => $request->komisi_hunter,
            'komisi_penitip' => $request->komisi_penitip,
            'komisi_reusemart' => $request->komisi_reusemart,
        ]);

        return response()->json($komisi);
    }

    // Menghapus komisi berdasarkan ID
    public function destroy($id)
    {
        $komisi = Komisi::find($id);
        if (!$komisi) {
            return response()->json(['message' => 'Komisi not found'], 404);
        }

        $komisi->delete();
        return response()->json(['message' => 'Komisi deleted successfully']);
    }
}
