<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenitipan;
use Illuminate\Http\Request;

class TransaksiPenitipanController extends Controller
{
    public function myProduct()
    {
        $penitipId = session('user.id');

        // Ambil semua barang yang berasal dari penitip ini
        $products = Barang::whereHas('transaksiPenitipan', function ($query) use ($penitipId) {
            $query->where('id_penitip', $penitipId);
        })->with(['gambar', 'kategori'])->get();

        return view('penitip.my-product', compact('products'));
    }

    // Menampilkan daftar semua transaksi penitipan
    public function index()
    {
        $transaksiPenitipan = TransaksiPenitipan::all();
        return response()->json($transaksiPenitipan);
    }

    // Menampilkan transaksi penitipan berdasarkan ID
    public function show($id)
    {
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }
        return response()->json($transaksiPenitipan);
    }

    // Menambahkan transaksi penitipan baru
    public function store(Request $request)
    {
        $request->validate([
            'id_qc' => 'required|exists:pegawai,id_pegawai',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'required|date',
            'tanggal_berakhir' => 'required|date',
            'perpanjangan' => 'required|integer',
        ]);

        $transaksiPenitipan = TransaksiPenitipan::create([
            'id_qc' => $request->id_qc,
            'id_hunter' => $request->id_hunter,
            'id_penitip' => $request->id_penitip,
            'tanggal_penitipan' => $request->tanggal_penitipan,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'perpanjangan' => $request->perpanjangan,
        ]);

        return response()->json($transaksiPenitipan, 201);
    }

    // Mengupdate transaksi penitipan berdasarkan ID
    public function update(Request $request, $id)
    {
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }

        $request->validate([
            'id_qc' => 'nullable|exists:pegawai,id_pegawai',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_penitip' => 'nullable|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'perpanjangan' => 'nullable|integer',
        ]);

        $transaksiPenitipan->update([
            'id_qc' => $request->id_qc ?? $transaksiPenitipan->id_qc,
            'id_hunter' => $request->id_hunter ?? $transaksiPenitipan->id_hunter,
            'id_penitip' => $request->id_penitip ?? $transaksiPenitipan->id_penitip,
            'tanggal_penitipan' => $request->tanggal_penitipan ?? $transaksiPenitipan->tanggal_penitipan,
            'tanggal_berakhir' => $request->tanggal_berakhir ?? $transaksiPenitipan->tanggal_berakhir,
            'perpanjangan' => $request->perpanjangan ?? $transaksiPenitipan->perpanjangan,
        ]);

        return response()->json($transaksiPenitipan);
    }

    // Menghapus transaksi penitipan berdasarkan ID
    public function destroy($id)
    {
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }

        $transaksiPenitipan->delete();
        return response()->json(['message' => 'Transaksi penitipan deleted successfully']);
    }
}
