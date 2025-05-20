<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    // Menampilkan daftar semua donasi
    public function index()
    {
        $donasi = Donasi::all();
        return response()->json($donasi);
    }

    // Menampilkan donasi berdasarkan ID
    public function show($id)
    {
        $donasi = Donasi::find($id);
        if (!$donasi) {
            return response()->json(['message' => 'Donasi not found'], 404);
        }
        return response()->json($donasi);
    }

    // Menambahkan donasi baru
    public function store(Request $request)
    {
        $request->validate([
            'id_request' => 'required|exists:request_donasi,id_request',
            'id_barang' => 'required|exists:barang,id_barang',
            'tanggal_donasi' => 'required|date',
            'nama_penerima' => 'required|string',
        ]);

        $donasi = Donasi::create([
            'id_request' => $request->id_request,
            'id_barang' => $request->id_barang,
            'tanggal_donasi' => $request->tanggal_donasi,
            'nama_penerima' => $request->nama_penerima,
        ]);

        // Perbarui status_request di RequestDonasi
        $requestDonasi = RequestDonasi::find($request->id_request);
        if ($requestDonasi) {
            $requestDonasi->update(['status_request' => 'sudah di donasikan']);
        }

        return response()->json($donasi, 201);
    }

    // Mengupdate donasi berdasarkan ID
    public function update(Request $request, $id)
    {
        $donasi = Donasi::find($id);
        if (!$donasi) {
            return response()->json(['message' => 'Donasi not found'], 404);
        }

        $request->validate([
            'id_request' => 'required|exists:request_donasi,id_request',
            'id_barang' => 'required|exists:barang,id_barang',
            'tanggal_donasi' => 'required|date',
            'nama_penerima' => 'required|string',
        ]);

        $donasi->update([
            'id_request' => $request->id_request,
            'id_barang' => $request->id_barang,
            'tanggal_donasi' => $request->tanggal_donasi,
            'nama_penerima' => $request->nama_penerima,
        ]);

        return response()->json($donasi);
    }

    // Menghapus donasi berdasarkan ID
    public function destroy($id)
    {
        $donasi = Donasi::find($id);
        if (!$donasi) {
            return response()->json(['message' => 'Donasi not found'], 404);
        }

        $donasi->delete();
        return response()->json(['message' => 'Donasi deleted successfully']);
    }
}
