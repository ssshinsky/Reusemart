<?php

namespace App\Http\Controllers;

use App\Models\KelolaTransaksi;
use Illuminate\Http\Request;

class KelolaTransaksiController extends Controller
{
    // Menampilkan daftar semua transaksi
    public function index()
    {
        $kelolaTransaksi = KelolaTransaksi::all();
        return response()->json($kelolaTransaksi);
    }

    // Menampilkan transaksi berdasarkan ID
    public function show($id)
    {
        $kelolaTransaksi = KelolaTransaksi::find($id);
        if (!$kelolaTransaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }
        return response()->json($kelolaTransaksi);
    }

    // Menambahkan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|exists:transaksi_pembelian,id_pembelian',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
        ]);

        $kelolaTransaksi = KelolaTransaksi::create([
            'id_pembelian' => $request->id_pembelian,
            'id_pegawai' => $request->id_pegawai,
        ]);

        return response()->json($kelolaTransaksi, 201);
    }

    // Mengupdate transaksi berdasarkan ID
    public function update(Request $request, $id)
    {
        $kelolaTransaksi = KelolaTransaksi::find($id);
        if (!$kelolaTransaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }

        $request->validate([
            'id_pembelian' => 'required|exists:transaksi_pembelian,id_pembelian',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
        ]);

        $kelolaTransaksi->update([
            'id_pembelian' => $request->id_pembelian,
            'id_pegawai' => $request->id_pegawai,
        ]);

        return response()->json($kelolaTransaksi);
    }

    // Menghapus transaksi berdasarkan ID
    public function destroy($id)
    {
        $kelolaTransaksi = KelolaTransaksi::find($id);
        if (!$kelolaTransaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }

        $kelolaTransaksi->delete();
        return response()->json(['message' => 'Transaksi deleted successfully']);
    }
}
