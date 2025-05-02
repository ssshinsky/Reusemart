<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    // Menampilkan daftar semua keranjang
    public function index()
    {
        $keranjangs = Keranjang::all();
        return response()->json($keranjangs);
    }

    // Menampilkan keranjang berdasarkan ID
    public function show($id)
    {
        $keranjang = Keranjang::find($id);
        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang not found'], 404);
        }
        return response()->json($keranjang);
    }

    // Menambahkan keranjang baru
    public function store(Request $request)
    {
        $request->validate([
            'banyak_barang' => 'required|integer',
        ]);

        $keranjang = Keranjang::create([
            'banyak_barang' => $request->banyak_barang,
        ]);

        return response()->json($keranjang, 201);
    }

    // Mengupdate keranjang berdasarkan ID
    public function update(Request $request, $id)
    {
        $keranjang = Keranjang::find($id);
        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang not found'], 404);
        }

        $request->validate([
            'banyak_barang' => 'required|integer',
        ]);

        $keranjang->update([
            'banyak_barang' => $request->banyak_barang,
        ]);

        return response()->json($keranjang);
    }

    // Menghapus keranjang berdasarkan ID
    public function destroy($id)
    {
        $keranjang = Keranjang::find($id);
        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang not found'], 404);
        }

        $keranjang->delete();
        return response()->json(['message' => 'Keranjang deleted successfully']);
    }
}
