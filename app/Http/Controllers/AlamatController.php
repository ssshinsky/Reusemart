<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Pembeli;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    // API: Tampilkan semua alamat
    public function index()
    {
        $alamat = Alamat::all();
        return response()->json($alamat);
    }

    // API: Tampilkan alamat berdasarkan ID
    public function show($id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }
        return response()->json($alamat);
    }

    // Web: Tambah alamat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_orang' => 'required|string|max:255',
            'label_alamat' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'kode_pos' => 'required|string|max:10',
            'no_telepon' => 'required|string|max:15',
        ]);

        $user = session('user');
        if (!$user || !isset($user['id'])) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $validated['id_pembeli'] = $user['id'];
        $validated['is_default'] = 0;

        Alamat::create($validated);

        return redirect()->route('pembeli.alamat')->with('success', 'Alamat berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return redirect()->back()->with('error', 'Alamat tidak ditemukan.');
        }

        $user = session('user');
        if (!$user || $alamat->id_pembeli != $user['id']) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

         $validated = $request->validate([
            'nama_orang' => 'required|string|max:255',
            'label_alamat' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'kode_pos' => 'required|string|max:10',
        ]);

        if ($request->is_default) {
            Alamat::where('id_pembeli', $alamat->id_pembeli)
                ->where('id_alamat', '!=', $id)
                ->update(['is_default' => false]);
        }

        $alamat->update($request->all());

        return redirect()->route('pembeli.alamat')->with('success', 'Alamat berhasil diperbarui');
    }

    // Web: Hapus alamat
    public function destroy($id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return redirect()->back()->with('error', 'Alamat tidak ditemukan.');
        }

        $user = session('user');
        if (!$user || $alamat->id_pembeli != $user['id']) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $alamat->delete();

        return redirect()->route('pembeli.alamat')->with('success', 'Alamat berhasil dihapus');
    }

    // Web: Tampilkan alamat untuk user login
    public function alamatPembeli(Request $request)
    {
        $user = session('user');

        if (!$user || !isset($user['id'])) {
            return redirect('/')->with('error', 'Akses ditolak. Silakan login terlebih dahulu.');
        }

        $pembeli = Pembeli::find($user['id']);
        $query = Alamat::where('id_pembeli', $user['id']);

        // Ambil parameter search
        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_orang', 'like', "%{$search}%")
                  ->orWhere('label_alamat', 'like', "%{$search}%")
                  ->orWhere('alamat_lengkap', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%")
                  ->orWhere('kabupaten', 'like', "%{$search}%")
                  ->orWhere('kode_pos', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $alamatList = $query->get();

        return view('Pembeli.alamat', compact('alamatList', 'pembeli'));
    }

    // Web: Set alamat sebagai default
    public function setDefault($id)
    {
        $alamat = Alamat::find($id);
        if (!$alamat) {
            return redirect()->back()->with('error', 'Alamat tidak ditemukan.');
        }

        $user = session('user');
        if (!$user || $alamat->id_pembeli != $user['id']) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        Alamat::where('id_pembeli', $alamat->id_pembeli)->update(['is_default' => false]);

        $alamat->is_default = true;
        $alamat->save();

        return redirect()->back()->with('success', 'Alamat default diperbarui.');
    }
}
