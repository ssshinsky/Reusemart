<?php

namespace App\Http\Controllers;

use App\Models\DiskusiProduk;
use Illuminate\Http\Request;

class DiskusiProdukController extends Controller
{
    // Menampilkan daftar semua diskusi produk
    public function index()
    {
        $diskusiProduk = DiskusiProduk::all();
        return response()->json($diskusiProduk);
    }

    // Menampilkan diskusi produk berdasarkan ID
    public function show($id)
    {
        $diskusiProduk = DiskusiProduk::find($id);
        if (!$diskusiProduk) {
            return response()->json(['message' => 'Diskusi Produk not found'], 404);
        }
        return response()->json($diskusiProduk);
    }

    // Menambahkan diskusi produk baru
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_pembeli' => 'nullable|exists:pembeli,id_pembeli',
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
            'diskusi' => 'required|string',
        ]);

        $diskusiProduk = DiskusiProduk::create([
            'id_barang' => $request->id_barang,
            'id_pembeli' => $request->id_pembeli,
            'id_pegawai' => $request->id_pegawai,
            'diskusi' => $request->diskusi,
        ]);

        return response()->json($diskusiProduk, 201);
    }

    // Mengupdate diskusi produk berdasarkan ID
    public function update(Request $request, $id)
    {
        $diskusiProduk = DiskusiProduk::find($id);
        if (!$diskusiProduk) {
            return response()->json(['message' => 'Diskusi Produk not found'], 404);
        }

        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_pembeli' => 'nullable|exists:pembeli,id_pembeli',
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
            'diskusi' => 'required|string',
        ]);

        $diskusiProduk->update([
            'id_barang' => $request->id_barang,
            'id_pembeli' => $request->id_pembeli,
            'id_pegawai' => $request->id_pegawai,
            'diskusi' => $request->diskusi,
        ]);

        return response()->json($diskusiProduk);
    }

    // Menghapus diskusi produk berdasarkan ID
    public function destroy($id)
    {
        $diskusiProduk = DiskusiProduk::find($id);
        if (!$diskusiProduk) {
            return response()->json(['message' => 'Diskusi Produk not found'], 404);
        }

        $diskusiProduk->delete();
        return response()->json(['message' => 'Diskusi Produk deleted successfully']);
    }
}
