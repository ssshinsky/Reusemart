<?php

namespace App\Http\Controllers;

use App\Models\TransaksiMerchandise;
use Illuminate\Http\Request;

class TransaksiMerchandiseController extends Controller
{
    // Menampilkan daftar semua transaksi merchandise
    public function index()
    {
        $transaksiMerchandise = TransaksiMerchandise::all();
        return response()->json($transaksiMerchandise);
    }

    // Menampilkan transaksi merchandise berdasarkan ID
    public function show($id)
    {
        $transaksiMerchandise = TransaksiMerchandise::find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }
        return response()->json($transaksiMerchandise);
    }

    // Menambahkan transaksi merchandise baru
    public function store(Request $request)
    {
        $request->validate([
            'id_merchandise' => 'required|exists:merchandise,id_merchandise',
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'jumlah' => 'required|integer',
            'total_poin_penukaran' => 'required|integer',
            'tanggal_klaim' => 'nullable|date',
        ]);

        $transaksiMerchandise = TransaksiMerchandise::create([
            'id_merchandise' => $request->id_merchandise,
            'id_pembeli' => $request->id_pembeli,
            'jumlah' => $request->jumlah,
            'total_poin_penukaran' => $request->total_poin_penukaran,
            'tanggal_klaim' => $request->tanggal_klaim,
        ]);

        return response()->json($transaksiMerchandise, 201);
    }

    // Mengupdate transaksi merchandise berdasarkan ID
    public function update(Request $request, $id)
    {
        $transaksiMerchandise = TransaksiMerchandise::find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }

        $request->validate([
            'id_merchandise' => 'nullable|exists:merchandise,id_merchandise',
            'id_pembeli' => 'nullable|exists:pembeli,id_pembeli',
            'jumlah' => 'nullable|integer',
            'total_poin_penukaran' => 'nullable|integer',
            'tanggal_klaim' => 'nullable|date',
        ]);

        $transaksiMerchandise->update([
            'id_merchandise' => $request->id_merchandise ?? $transaksiMerchandise->id_merchandise,
            'id_pembeli' => $request->id_pembeli ?? $transaksiMerchandise->id_pembeli,
            'jumlah' => $request->jumlah ?? $transaksiMerchandise->jumlah,
            'total_poin_penukaran' => $request->total_poin_penukaran ?? $transaksiMerchandise->total_poin_penukaran,
            'tanggal_klaim' => $request->tanggal_klaim ?? $transaksiMerchandise->tanggal_klaim,
        ]);

        return response()->json($transaksiMerchandise);
    }

    // Menghapus transaksi merchandise berdasarkan ID
    public function destroy($id)
    {
        $transaksiMerchandise = TransaksiMerchandise::find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }

        $transaksiMerchandise->delete();
        return response()->json(['message' => 'Transaksi merchandise deleted successfully']);
    }
}
