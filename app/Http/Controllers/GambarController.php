<?php

namespace App\Http\Controllers;

use App\Models\Gambar;
use Illuminate\Http\Request;

class GambarController extends Controller
{
    // Menampilkan daftar semua gambar
    public function index()
    {
        $gambar = Gambar::all();
        return response()->json($gambar);
    }

    // Menampilkan gambar berdasarkan ID
    public function show($id)
    {
        $gambar = Gambar::find($id);
        if (!$gambar) {
            return response()->json(['message' => 'Gambar not found'], 404);
        }
        return response()->json($gambar);
    }

    // Menambahkan gambar baru
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'gambar_barang' => 'required|string',
        ]);

        $gambar = Gambar::create([
            'id_barang' => $request->id_barang,
            'gambar_barang' => $request->gambar_barang,
        ]);

        return response()->json($gambar, 201);
    }

    // Mengupdate gambar berdasarkan ID
    public function update(Request $request, $id)
    {
        $gambar = Gambar::find($id);
        if (!$gambar) {
            return response()->json(['message' => 'Gambar not found'], 404);
        }

        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'gambar_barang' => 'required|string',
        ]);

        $gambar->update([
            'id_barang' => $request->id_barang,
            'gambar_barang' => $request->gambar_barang,
        ]);

        return response()->json($gambar);
    }

    // Menghapus gambar berdasarkan ID
    public function destroy($id)
    {
        $gambar = Gambar::find($id);
        if (!$gambar) {
            return response()->json(['message' => 'Gambar not found'], 404);
        }

        $gambar->delete();
        return response()->json(['message' => 'Gambar deleted successfully']);
    }
}
