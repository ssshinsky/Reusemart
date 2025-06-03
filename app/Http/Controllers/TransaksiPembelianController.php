<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\Keranjang;
use App\Models\DetailKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiPembelianController extends Controller
{
    // 🔓 API: Menampilkan semua transaksi pembelian
    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::all();
        return response()->json($transaksiPembelian);
    }
  
    // 🔓 API: Menampilkan satu transaksi pembelian
    public function show($id)
    {
        $transaksiPembelian = TransaksiPembelian::find($id);
        if (!$transaksiPembelian) {
            return response()->json(['message' => 'Transaksi pembelian not found'], 404);
        }
        return response()->json($transaksiPembelian);
    }

    // 🔓 API: Tambah transaksi baru
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

    // 🔓 API: Update transaksi
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

        $transaksiPembelian->update($request->all());

        return response()->json($transaksiPembelian);
    }

    // 🔓 API: Hapus transaksi
    public function destroy($id)
    {
        $transaksiPembelian = TransaksiPembelian::find($id);
        if (!$transaksiPembelian) {
            return response()->json(['message' => 'Transaksi pembelian not found'], 404);
        }

        $transaksiPembelian->delete();
        return response()->json(['message' => 'Transaksi pembelian deleted successfully']);
    }
  
    public function batalkanOtomatis($id)
    {
        $keranjang = Keranjang::with('detailKeranjang.itemKeranjang.barang')->find($id);

        if (!$keranjang) {
            return redirect()->route('pembeli.cart')->with('error', 'Keranjang tidak ditemukan.');
        }

        // Kembalikan stok barang
        foreach ($keranjang->detailKeranjang as $detail) {
            $barang = $detail->itemKeranjang->barang;
            $barang->save();
        }

        // Hapus data keranjang dan detail
        DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->delete();
        $keranjang->delete();

        return redirect()->route('pembeli.keranjang')->with('success', 'Checkout dibatalkan otomatis karena melewati batas waktu.');
    }

    public function bayar(Request $request)
    {
        // Debugging input
        Log::info('Bayar Request:', ['input' => $request->all()]);

        // Validasi input dari form
        $request->validate([
            'poin_ditukar' => 'required|numeric|min:0',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'total_final' => 'required|numeric|min:0',
            'bonus_poin' => 'required|numeric|min:0',
        ], [
            'poin_ditukar.required' => 'Masukkan jumlah poin yang ditukar.',
            'poin_ditukar.numeric' => 'Jumlah poin yang ditukar harus berupa angka.',
            'poin_ditukar.min' => 'Jumlah poin yang ditukar tidak boleh kurang dari 0.',
            'bukti_pembayaran.required' => 'Upload bukti pembayaran diperlukan.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar.',
            'total_final.required' => 'Total pembayaran tidak valid.',
            'bonus_poin.required' => 'Bonus poin tidak valid.',
        ]);

        // Ambil data dari session
        $user = session('user');
        $role = session('role');

        // Cek apakah yang login adalah pembeli
        if ($role !== 'pembeli' || !$user) {
            Log::error('User not logged in or not pembeli', ['user' => $user, 'role' => $role]);
            return redirect()->route('login')->with('error', 'Anda harus login sebagai pembeli.');
        }

        $idPembeli = $user['id'];
        $pembeli = Pembeli::find($idPembeli);
        if (!$pembeli) {
            Log::error('Pembeli not found', ['id_pembeli' => $idPembeli]);
            return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
        }

        // Ambil data dari session
        $keranjangId = session('checkout_keranjang_id');
        $alamatId = session('checkout_id_alamat');
        $metode = session('checkout_metode_pengiriman');
        $totalHarga = session('checkout_total_harga');

        // Cek keranjang
        $keranjang = Keranjang::with('detailKeranjang.itemKeranjang.barang')->findOrFail($keranjangId);
        $items = $keranjang->detailKeranjang->map->itemKeranjang;

        if ($items->isEmpty()) {
            Log::error('No items in keranjang', ['keranjang_id' => $keranjangId]);
            return redirect()->back()->with('error', 'Keranjang kosong atau tidak valid.');
        }

        // Hitung total harga barang (kuantitas = 1)
        $totalHargaBarang = $items->sum(fn($item) => $item->barang->harga_barang);
        $ongkir = ($totalHargaBarang >= 1500000 || $metode !== 'kurir') ? 0 : 100000;
        $tahunBulan = now()->format('Y.m.');
        $lastTransaksi = TransaksiPembelian::where('no_resi', 'like', $tahunBulan . '%')
            ->orderBy('no_resi', 'desc')
            ->first();
        $nomorUrut = $lastTransaksi ? (int)substr($lastTransaksi->no_resi, -3) + 1 : 1;
        $noResi = $tahunBulan . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // Cek poin yang ditukar
        $poinDitukar = (int)$request->poin_ditukar;
        if ($poinDitukar > $pembeli->poin_pembeli) {
            Log::error('Poin ditukar lebih besar dari poin dimiliki', [
                'poin_ditukar' => $poinDitukar,
                'poin_pembeli' => $pembeli->poin_pembeli,
            ]);
            return redirect()->back()->with('error', 'Poin yang ditukar melebihi poin yang dimiliki.');
        }

        // Simpan bukti pembayaran
        $path = $request->file('bukti_pembayaran')->store('pembayaran', 'public');

        // Buat transaksi (tanpa poin_ditukar)
        $transaksi = TransaksiPembelian::create([
            'no_resi' => $noResi,
            'id_pembeli' => $idPembeli,
            'id_keranjang' => $keranjangId,
            'id_alamat' => $metode === 'kurir' ? $alamatId : null,
            'tanggal_pembelian' => now(),
            'total_harga_barang' => $totalHargaBarang,
            'metode_pengiriman' => $metode,
            'ongkir' => $ongkir,
            'total_harga' => $request->total_final,
            'status_transaksi' => 'diproses',
            'bukti_pembayaran' => $path,
            'poin_terpakai' => $poinDitukar,
        ]);

        // Update poin pembeli dengan bonus poin (hanya di database)
        $bonusPoin = (int)$request->bonus_poin;
        $pembeli->poin_pembeli = ($pembeli->poin_pembeli - $poinDitukar) + $bonusPoin;
        $pembeli->save();

        foreach ($items as $item) {
            $item->barang->update(['status_barang' => 'sold']);
        }

        // Hapus item keranjang
        $selectedItems = session('checkout_selected_items');
        ItemKeranjang::whereIn('id_item_keranjang', $selectedItems)->delete();

        // Hapus detail keranjang dan keranjang
        DetailKeranjang::where('id_keranjang', $keranjangId)->delete();
        $keranjang->delete();

        // Hapus session checkout
        session()->forget([
            'checkout_keranjang_id',
            'checkout_selected_items',
            'checkout_metode_pengiriman',
            'checkout_id_alamat',
            'checkout_total_harga',
        ]);

        Log::info('Transaksi berhasil', [
            'transaksi_id' => $transaksi->id_transaksi,
            'poin_ditukar' => $poinDitukar,
            'bonus_poin' => $bonusPoin,
            'poin_pembeli_baru' => $pembeli->poin_pembeli,
        ]);

        return redirect()->route('pembeli.detailTransaksi', $transaksi->id_transaksi)
                        ->with('success', 'Transaksi berhasil dibuat. Silakan lakukan pembayaran.');
    }


    public function uploadBukti(Request $request, $id)
    {
        $request->validate(['bukti_tf' => 'required|image|max:2048']);
        $transaksi = TransaksiPembelian::findOrFail($id);

        if ($request->hasFile('bukti_tf')) {
            $filename = time() . '.' . $request->bukti_tf->extension();
            $request->bukti_tf->storeAs('public/bukti_tf', $filename);
            $transaksi->bukti_tf = $filename;
            $transaksi->save();
        }

        return back()->with('success', 'Bukti transfer berhasil diupload.');
    }

    // ✅ Web: Riwayat transaksi untuk pembeli login
    public function riwayat()
    {
        $riwayat = TransaksiPembelian::with('detailPembelians.barang')
            ->where('id_pembeli', Auth::guard('pembeli')->id())
            ->latest()
            ->get();

        return view('pembeli.riwayat', compact('riwayat'));
    }

    // ✅ Web: Detail transaksi
    public function detail($id)
    {
        $transaksi = TransaksiPembelian::with('detailPembelians.barang', 'alamat')
            ->where('id_pembeli', Auth::guard('pembeli')->id())
            ->findOrFail($id);

        return view('pembeli.riwayat_detail', compact('transaksi'));
    }
}
