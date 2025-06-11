<?php

namespace App\Http\Controllers;

use App\Models\TransaksiMerchandise;
use Illuminate\Http\Request;

class TransaksiMerchandiseController extends Controller
{
    // Menampilkan daftar semua transaksi merchandise
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'tanggal_klaim');
        $sortDir = $request->query('sort_dir', 'desc');

        $validSortColumns = ['tanggal_klaim', 'total_poin_penukaran', 'jumlah', 'id_transaksi_merchandise'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'tanggal_klaim';
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $transaksiMerchandises = TransaksiMerchandise::with(['merchandise', 'pembeli'])
            ->orderBy($sortBy, $sortDir)
            ->get();

        return view('CS.merchandise-claims', compact('transaksiMerchandises', 'sortBy', 'sortDir'));
    }

    public function juneClaims(Request $request)
    {
        $sortBy = $request->query('sort_by', 'tanggal_klaim');
        $sortDir = $request->query('sort_dir', 'desc');

        $validSortColumns = ['tanggal_klaim', 'total_poin_penukaran', 'jumlah', 'id_transaksi_merchandise'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'tanggal_klaim';
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $transaksiMerchandises = TransaksiMerchandise::with(['merchandise', 'pembeli'])
            ->where('total_poin_penukaran', 100)
            // ->whereYear('tanggal_klaim', 2025)
            ->orderBy($sortBy, $sortDir)
            ->get();

        return view('CS.june-merchandise-claims', compact('transaksiMerchandises', 'sortBy', 'sortDir'));
    }

    // Menampilkan transaksi merchandise berdasarkan ID
    public function show($id)
    {
        $transaksiMerchandise = TransaksiMerchandise::with(['merchandise', 'pembeli'])->find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }
        return response()->json($transaksiMerchandise);
    }

    // Menambahkan transaksi merchandise baru
    public function store(Request $request)
    {
        $request->validate([
            'id_merchandise' => 'required|exists:merchandise,id_merchandise',
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'jumlah' => 'required|integer|min:1',
            'total_poin_penukaran' => 'required|integer|min:0',
            'tanggal_klaim' => 'nullable|date',
            'status_transaksi' => 'nullable|string|max:50|in:belum diambil,diambil',
        ]);

        $transaksiMerchandise = TransaksiMerchandise::create([
            'id_merchandise' => $request->id_merchandise,
            'id_pembeli' => $request->id_pembeli,
            'jumlah' => $request->jumlah,
            'total_poin_penukaran' => $request->total_poin_penukaran,
            'tanggal_klaim' => $request->tanggal_klaim ?? now()->toDateString(),
            'status_transaksi' => $request->status_transaksi ?? 'belum diambil',
        ]);

        return response()->json($transaksiMerchandise, 201);
    }

    // Mengupdate transaksi merchandise berdasarkan ID
    public function update(Request $request, $id)
    {
        $transaksiMerchandise = TransaksiMerchandise::find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }

        // Cek jika tanggal_ambil_merch sudah diisi
        if ($transaksiMerchandise->tanggal_ambil_merch) {
            return redirect()->route('cs.merchandise-claim.index')->with('error', 'Tanggal ambil sudah diisi dan tidak dapat diubah!');
        }

        $request->validate([
            'tanggal_ambil_merch' => 'required|date|after_or_equal:' . $transaksiMerchandise->tanggal_klaim,
        ]);

        $transaksiMerchandise->update([
            'tanggal_ambil_merch' => $request->tanggal_ambil_merch,
            'status_transaksi' => 'diambil',
        ]);

        return redirect()->route('cs.merchandise-claim.index')->with('success', 'Tanggal ambil berhasil diisi dan status diubah menjadi diambil!');
    }

    // Menghapus transaksi merchandise berdasarkan ID
    public function destroy($id)
    {
        $transaksiMerchandise = TransaksiMerchandise::find($id);
        if (!$transaksiMerchandise) {
            return response()->json(['message' => 'Transaksi merchandise not found'], 404);
        }

        $transaksiMerchandise->delete();
        return response()->json(['message' => 'Transaksi merchandise deleted successfully']);
    }

    // Pencarian live untuk daftar klaim
    public function search(Request $request)
    {
        $query = $request->input('q');
        $transaksiMerchandises = TransaksiMerchandise::with(['merchandise', 'pembeli'])
            ->whereHas('pembeli', function ($q) use ($query) {
                $q->where('nama_pembeli', 'like', "%{$query}%");
            })
            ->orWhereHas('merchandise', function ($q) use ($query) {
                $q->where('nama_merch', 'like', "%{$query}%");
            })
            ->get();

        return view('CS.partials.merchandise-claims-table', compact('transaksiMerchandises'))->render();
    }
}