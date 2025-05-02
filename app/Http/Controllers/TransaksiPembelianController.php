<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use Illuminate\Http\Request;

class TransaksiPembelianController extends Controller
{
    // Menampilkan daftar semua transaksi pembelian
    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::all();
        return response()->json($transaksiPembelian);
    }

    // Menampilkan transaksi pembelian berdasarkan ID
    public function show($id)
    {
        $transaksiPembelian = TransaksiPembelian::find($id);
        if (!$transaksiPembelian) {
            return response()->json(['message' => 'Transaksi pembelian not found'], 404);
        }
        return response()->json($transaksiPembelian);
    }

    // Menambahkan transaksi pembelian baru
    public function store(Request $request)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'id_alamat' => 'required|exists:alamat,id_alamat',
            'no_resi' => 'required|string',
            'tanggal_pembelian' => 'required|date',
            'waktu_pembayaran' => 'nullable|date',
            'bukti_tf' => 'nullable|string',
            'total_harga_barang' => 'nullable|numeric',
            'metode_pengiriman' => 'required|string',
            'ongkir' => 'required|numeric',
            'tanggal_ambil' => 'nullable|date',
            'tanggal_pengiriman' => 'nullable|date',
            'total_harga' => 'nullable|numeric',
            'status_transaksi' => 'nullable|string',
            'poin_terpakai' => 'nullable|integer',
            'poin_pembeli' => 'nullable|integer',
            'poin_penitip' => 'nullable|integer',
        ]);

        $transaksiPembelian = TransaksiPembelian::create([
            'id_keranjang' => $request->id_keranjang,
            'id_alamat' => $request->id_alamat,
            'no_resi' => $request->no_resi,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'waktu_pembayaran' => $request->waktu_pembayaran,
            'bukti_tf' => $request->bukti_tf,
            'total_harga_barang' => $request->total_harga_barang,
            'metode_pengiriman' => $request->metode_pengiriman,
            'ongkir' => $request->ongkir,
            'tanggal_ambil' => $request->tanggal_ambil,
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'total_harga' => $request->total_harga,
            'status_transaksi' => $request->status_transaksi ?? 'Menunggu Pembayaran',
            'poin_terpakai' => $request->poin_terpakai,
            'poin_pembeli' => $request->poin_pembeli,
            'poin_penitip' => $request->poin_penitip,
        ]);

        return response()->json($transaksiPembelian, 201);
    }

    // Mengupdate transaksi pembelian berdasarkan ID
    public function update(Request $request, $id)
    {
        $transaksiPembelian = TransaksiPembelian::find($id);
        if (!$transaksiPembelian) {
            return response()->json(['message' => 'Transaksi pembelian not found'], 404);
        }

        $request->validate([
            'id_keranjang' => 'nullable|exists:keranjang,id_keranjang',
            'id_alamat' => 'nullable|exists:alamat,id_alamat',
            'no_resi' => 'nullable|string',
            'tanggal_pembelian' => 'nullable|date',
            'waktu_pembayaran' => 'nullable|date',
            'bukti_tf' => 'nullable|string',
            'total_harga_barang' => 'nullable|numeric',
            'metode_pengiriman' => 'nullable|string',
            'ongkir' => 'nullable|numeric',
            'tanggal_ambil' => 'nullable|date',
            'tanggal_pengiriman' => 'nullable|date',
            'total_harga' => 'nullable|numeric',
            'status_transaksi' => 'nullable|string',
            'poin_terpakai' => 'nullable|integer',
            'poin_pembeli' => 'nullable|integer',
            'poin_penitip' => 'nullable|integer',
        ]);

        $transaksiPembelian->update([
            'id_keranjang' => $request->id_keranjang ?? $transaksiPembelian->id_keranjang,
            'id_alamat' => $request->id_alamat ?? $transaksiPembelian->id_alamat,
            'no_resi' => $request->no_resi ?? $transaksiPembelian->no_resi,
            'tanggal_pembelian' => $request->tanggal_pembelian ?? $transaksiPembelian->tanggal_pembelian,
            'waktu_pembayaran' => $request->waktu_pembayaran ?? $transaksiPembelian->waktu_pembayaran,
            'bukti_tf' => $request->bukti_tf ?? $transaksiPembelian->bukti_tf,
            'total_harga_barang' => $request->total_harga_barang ?? $transaksiPembelian->total_harga_barang,
            'metode_pengiriman' => $request->metode_pengiriman ?? $transaksiPembelian->metode_pengiriman,
            'ongkir' => $request->ongkir ?? $transaksiPembelian->ongkir,
            'tanggal_ambil' => $request->tanggal_ambil ?? $transaksiPembelian->tanggal_ambil,
            'tanggal_pengiriman' => $request->tanggal_pengiriman ?? $transaksiPembelian->tanggal_pengiriman,
            'total_harga' => $request->total_harga ?? $transaksiPembelian->total_harga,
            'status_transaksi' => $request->status_transaksi ?? $transaksiPembelian->status_transaksi,
            'poin_terpakai' => $request->poin_terpakai ?? $transaksiPembelian->poin_terpakai,
            'poin_pembeli' => $request->poin_pembeli ?? $transaksiPembelian->poin_pembeli,
            'poin_penitip' => $request->poin_penitip ?? $transaksiPembelian->poin_penitip,
        ]);

        return response()->json($transaksiPembelian);
    }

    // Menghapus transaksi pembelian berdasarkan ID
    public function destroy($id)
    {
        $transaksiPembelian = TransaksiPembelian::find($id);
        if (!$transaksiPembelian) {
            return response()->json(['message' => 'Transaksi pembelian not found'], 404);
        }

        $transaksiPembelian->delete();
        return response()->json(['message' => 'Transaksi pembelian deleted successfully']);
    }
}
