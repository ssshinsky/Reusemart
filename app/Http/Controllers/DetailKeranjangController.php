<?php

namespace App\Http\Controllers;

use App\Models\DetailKeranjang;
use Illuminate\Http\Request;

class DetailKeranjangController extends Controller
{
    // Menampilkan daftar semua detail keranjang
    public function index()
    {
        $detailKeranjang = DetailKeranjang::all();
        return response()->json($detailKeranjang);
    }

    // Menampilkan detail keranjang berdasarkan ID
    public function show($id)
    {
        $detailKeranjang = DetailKeranjang::find($id);
        if (!$detailKeranjang) {
            return response()->json(['message' => 'Detail Keranjang not found'], 404);
        }
        return response()->json($detailKeranjang);
    }

    // Menambahkan detail keranjang baru
    public function store(Request $request)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'id_item_keranjang' => 'required|exists:item_keranjang,id_item_keranjang',
        ]);

        $detailKeranjang = DetailKeranjang::create([
            'id_keranjang' => $request->id_keranjang,
            'id_item_keranjang' => $request->id_item_keranjang,
        ]);

        return response()->json($detailKeranjang, 201);
    }

    // Mengupdate detail keranjang berdasarkan ID
    public function update(Request $request, $id)
    {
        $detailKeranjang = DetailKeranjang::find($id);
        if (!$detailKeranjang) {
            return response()->json(['message' => 'Detail Keranjang not found'], 404);
        }

        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'id_item_keranjang' => 'required|exists:item_keranjang,id_item_keranjang',
        ]);

        $detailKeranjang->update([
            'id_keranjang' => $request->id_keranjang,
            'id_item_keranjang' => $request->id_item_keranjang,
        ]);

        return response()->json($detailKeranjang);
    }

    // Menghapus detail keranjang berdasarkan ID
    public function destroy($id)
    {
        $detailKeranjang = DetailKeranjang::find($id);
        if (!$detailKeranjang) {
            return response()->json(['message' => 'Detail Keranjang not found'], 404);
        }

        $detailKeranjang->delete();
        return response()->json(['message' => 'Detail Keranjang deleted successfully']);
    }
}
