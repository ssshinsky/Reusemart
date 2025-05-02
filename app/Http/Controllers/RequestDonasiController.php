<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use Illuminate\Http\Request;

class RequestDonasiController extends Controller
{
    // Menampilkan daftar semua request donasi
    public function index()
    {
        $requestDonasi = RequestDonasi::with(['organisasi', 'pegawai'])->get();
        return response()->json($requestDonasi);
    }

    // Menampilkan request donasi berdasarkan ID
    public function show($id)
    {
        $requestDonasi = RequestDonasi::with(['organisasi', 'pegawai'])->find($id);
        if (!$requestDonasi) {
            return response()->json(['message' => 'Request donasi not found'], 404);
        }
        return response()->json($requestDonasi);
    }

    // Menambahkan request donasi baru
    public function store(Request $request)
    {
        $request->validate([
            'id_organisasi' => 'required|exists:organisasi,id_organisasi',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'request' => 'required|string',
        ]);

        $requestDonasi = RequestDonasi::create([
            'id_organisasi' => $request->id_organisasi,
            'id_pegawai' => $request->id_pegawai,
            'request' => $request->request,
        ]);

        return response()->json($requestDonasi, 201);
    }

    // Mengupdate request donasi berdasarkan ID
    public function update(Request $request, $id)
    {
        $requestDonasi = RequestDonasi::find($id);
        if (!$requestDonasi) {
            return response()->json(['message' => 'Request donasi not found'], 404);
        }

        $request->validate([
            'id_organisasi' => 'nullable|exists:organisasi,id_organisasi',
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
            'request' => 'nullable|string',
        ]);

        $requestDonasi->update([
            'id_organisasi' => $request->id_organisasi ?? $requestDonasi->id_organisasi,
            'id_pegawai' => $request->id_pegawai ?? $requestDonasi->id_pegawai,
            'request' => $request->request ?? $requestDonasi->request,
        ]);

        return response()->json($requestDonasi);
    }

    // Menghapus request donasi berdasarkan ID
    public function destroy($id)
    {
        $requestDonasi = RequestDonasi::find($id);
        if (!$requestDonasi) {
            return response()->json(['message' => 'Request donasi not found'], 404);
        }

        $requestDonasi->delete();
        return response()->json(['message' => 'Request donasi deleted successfully']);
    }
}
