<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\DiskusiProduk;
use Carbon\Carbon;

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
        try {
            $barang = Barang::with(['kategori', 'gambar'])->findOrFail($id);

            // dd($barang->gambar);

            $diskusi = DiskusiProduk::with(['pembeli', 'pegawai'])
                ->where('id_barang', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            $statusGaransi = $barang->status_garansi;
            $garansiBerlaku = false;
            if ($statusGaransi === 'garansi' && $barang->tanggal_garansi) {
                $tanggalGaransi = Carbon::parse($barang->tanggal_garansi);
                $tanggalSekarang = Carbon::now();
                $garansiBerlaku = $tanggalGaransi->gte($tanggalSekarang);
            }

            $diskusi = $diskusi->isEmpty() ? collect([]) : $diskusi;

            return view('umum.show', compact('barang', 'diskusi', 'statusGaransi', 'garansiBerlaku'));
        } catch (\Exception $e) {
            \Log::error('Error in BarangController@show: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            dd($e->getMessage(), $e->getTraceAsString()); // Tampilkan error di browser
        }
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
