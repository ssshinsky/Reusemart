<?php

namespace App\Http\Controllers;

use App\Models\ItemKeranjang;
use Illuminate\Http\Request;

class ItemKeranjangController extends Controller
{
    // Menampilkan daftar semua item keranjang
    public function index()
    {
        $itemKeranjang = ItemKeranjang::all();
        return response()->json($itemKeranjang);
    }

    // Menampilkan item keranjang berdasarkan ID
    public function show($id)
    {
        $itemKeranjang = ItemKeranjang::find($id);
        if (!$itemKeranjang) {
            return response()->json(['message' => 'Item Keranjang not found'], 404);
        }
        return response()->json($itemKeranjang);
    }

    // Menambahkan item keranjang baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'id_barang' => 'required|exists:barang,id_barang',
            'is_selected' => 'required|boolean',
        ]);

        $itemKeranjang = ItemKeranjang::create([
            'id_pembeli' => $request->id_pembeli,
            'id_barang' => $request->id_barang,
            'is_selected' => $request->is_selected,
        ]);

        return response()->json($itemKeranjang, 201);
    }

    // Mengupdate item keranjang berdasarkan ID
    public function update(Request $request, $id)
    {
        $itemKeranjang = ItemKeranjang::find($id);
        if (!$itemKeranjang) {
            return response()->json(['message' => 'Item Keranjang not found'], 404);
        }

        $request->validate([
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'id_barang' => 'required|exists:barang,id_barang',
            'is_selected' => 'required|boolean',
        ]);

        $itemKeranjang->update([
            'id_pembeli' => $request->id_pembeli,
            'id_barang' => $request->id_barang,
            'is_selected' => $request->is_selected,
        ]);

        return response()->json($itemKeranjang);
    }

    // Menghapus item keranjang berdasarkan ID
    public function destroy($id)
    {
        $itemKeranjang = ItemKeranjang::find($id);
        if (!$itemKeranjang) {
            return response()->json(['message' => 'Item Keranjang not found'], 404);
        }

        $itemKeranjang->delete();
        return response()->json(['message' => 'Item Keranjang deleted successfully']);
    }
}
