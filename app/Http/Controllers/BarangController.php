<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Menampilkan daftar semua barang
    public function index()
    {
        $barang = Barang::all();
        return response()->json($barang);
    }

    // Menampilkan barang berdasarkan ID
    public function show($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }
        return response()->json($barang);
    }

    // Menambahkan barang baru
    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_transaksi_penitipan' => 'required|exists:transaksi_penitipan,id_transaksi_penitipan',
            'kode_barang' => 'required|string|max:10',
            'nama_barang' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'berat_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'status_garansi' => 'required|string|max:255',
            'status_barang' => 'required|string|max:255',
            'tanggal_garansi' => 'nullable|date',
        ]);

        $barang = Barang::create([
            'id_kategori' => $request->id_kategori,
            'id_transaksi_penitipan' => $request->id_transaksi_penitipan,
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'harga_barang' => $request->harga_barang,
            'berat_barang' => $request->berat_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
            'status_garansi' => $request->status_garansi,
            'status_barang' => $request->status_barang,
            'tanggal_garansi' => $request->tanggal_garansi,
        ]);

        return response()->json($barang, 201);
    }

    // Mengupdate barang berdasarkan ID
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_transaksi_penitipan' => 'required|exists:transaksi_penitipan,id_transaksi_penitipan',
            'kode_barang' => 'required|string|max:10',
            'nama_barang' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'berat_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'status_garansi' => 'required|string|max:255',
            'status_barang' => 'required|string|max:255',
            'tanggal_garansi' => 'nullable|date',
        ]);

        $barang->update([
            'id_kategori' => $request->id_kategori,
            'id_transaksi_penitipan' => $request->id_transaksi_penitipan,
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'harga_barang' => $request->harga_barang,
            'berat_barang' => $request->berat_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
            'status_garansi' => $request->status_garansi,
            'status_barang' => $request->status_barang,
            'tanggal_garansi' => $request->tanggal_garansi,
        ]);

        return response()->json($barang);
    }

    // Menghapus barang berdasarkan ID
    public function destroy($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $barang->delete();
        return response()->json(['message' => 'Barang deleted successfully']);
    }
}
