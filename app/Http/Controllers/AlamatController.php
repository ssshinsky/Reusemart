<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    // Menampilkan daftar semua alamat
    public function index()
    {
        $alamat = Alamat::all();
        return response()->json($alamat);
    }

    // Menampilkan alamat berdasarkan ID
    public function show($id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }
        return response()->json($alamat);
    }

    // Menambahkan alamat baru
    public function store(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'nama_orang' => 'nullable|string|max:255',
            'label_alamat' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'nomor_telepon' => 'required|string|max:15',
            'kode_pos' => 'required|string|max:10',
            'is_default' => 'required|boolean',
        ]);

        $alamat = Alamat::create([
            'id_pembeli' => $request->id_pembeli,
            'nama_orang' => $request->nama_orang,
            'label_alamat' => $request->label_alamat,
            'alamat_lengkap' => $request->alamat_lengkap,
            'nomor_telepon' => $request->nomor_telepon,
            'kode_pos' => $request->kode_pos,
            'is_default' => $request->is_default,
        ]);

        return response()->json($alamat, 201);
    }

    // Mengupdate alamat berdasarkan ID
    public function update(Request $request, $id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }

        $request->validate([
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'nama_orang' => 'nullable|string|max:255',
            'label_alamat' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'nomor_telepon' => 'required|string|max:15',
            'kode_pos' => 'required|string|max:10',
            'is_default' => 'required|boolean',
        ]);

        $alamat->update([
            'id_pembeli' => $request->id_pembeli,
            'nama_orang' => $request->nama_orang,
            'label_alamat' => $request->label_alamat,
            'alamat_lengkap' => $request->alamat_lengkap,
            'nomor_telepon' => $request->nomor_telepon,
            'kode_pos' => $request->kode_pos,
            'is_default' => $request->is_default,
        ]);

        return response()->json($alamat);
    }

    // Menghapus alamat berdasarkan ID
    public function destroy($id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }

        $alamat->delete();
        return response()->json(['message' => 'Alamat deleted successfully']);
    }
}
