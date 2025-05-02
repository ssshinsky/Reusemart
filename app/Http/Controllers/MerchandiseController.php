<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use Illuminate\Http\Request;

class MerchandiseController extends Controller
{
    // Menampilkan daftar semua merchandise
    public function index()
    {
        $merchandises = Merchandise::all();
        return response()->json($merchandises);
    }

    // Menampilkan merchandise berdasarkan ID
    public function show($id)
    {
        $merchandise = Merchandise::find($id);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }
        return response()->json($merchandise);
    }

    // Menambahkan merchandise baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'nama_merch' => 'required|string',
            'poin' => 'required|integer',
            'stok' => 'required|integer',
            'gambar_merch' => 'required|string', // Asumsi gambar adalah URL atau path
        ]);

        $merchandise = Merchandise::create([
            'id_pegawai' => $request->id_pegawai,
            'nama_merch' => $request->nama_merch,
            'poin' => $request->poin,
            'stok' => $request->stok,
            'gambar_merch' => $request->gambar_merch,
        ]);

        return response()->json($merchandise, 201);
    }

    // Mengupdate merchandise berdasarkan ID
    public function update(Request $request, $id)
    {
        $merchandise = Merchandise::find($id);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }

        $request->validate([
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
            'nama_merch' => 'nullable|string',
            'poin' => 'nullable|integer',
            'stok' => 'nullable|integer',
            'gambar_merch' => 'nullable|string',
        ]);

        $merchandise->update([
            'id_pegawai' => $request->id_pegawai ?? $merchandise->id_pegawai,
            'nama_merch' => $request->nama_merch ?? $merchandise->nama_merch,
            'poin' => $request->poin ?? $merchandise->poin,
            'stok' => $request->stok ?? $merchandise->stok,
            'gambar_merch' => $request->gambar_merch ?? $merchandise->gambar_merch,
        ]);

        return response()->json($merchandise);
    }

    // Menghapus merchandise berdasarkan ID
    public function destroy($id)
    {
        $merchandise = Merchandise::find($id);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }

        $merchandise->delete();
        return response()->json(['message' => 'Merchandise deleted successfully']);
    }
}
