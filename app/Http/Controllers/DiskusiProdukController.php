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
            'diskusi' => 'required|string|max:1000',
        ]);

        $pembeli = auth('api_pembeli')->user();

        $diskusi = DiskusiProduk::create([
            'id_barang' => $request->id_barang,
            'diskusi' => $request->diskusi,
            'id_pembeli' => $pembeli->id_pembeli,
        ]);

        return response()->json([
            'diskusi' => $diskusi->diskusi,
            'created_at' => now()->format('d M Y, H:i')
        ]);
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
