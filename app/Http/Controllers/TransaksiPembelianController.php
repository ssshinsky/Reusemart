<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\Keranjang;
use App\Models\Pembeli;
use App\Models\DetailKeranjang;
use App\Models\ItemKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransaksiPembelianController extends Controller
{
    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::all();
        return response()->json($transaksiPembelian);
    }

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

        foreach ($keranjang->detailKeranjang as $detail) {
            $barang = $detail->itemKeranjang->barang;
            $barang->save();
        }

        DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->delete();
        $keranjang->delete();

        return redirect()->route('pembeli.keranjang')->with('success', 'Checkout dibatalkan otomatis karena melewati batas waktu.');
    }

    public function bayar(Request $request)
    {
        Log::info('Bayar Request:', ['input' => $request->all()]);

        $request->validate([
            'poin_ditukar' => 'required|numeric|min:0',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'total_final' => 'required|numeric|min:0',
            'bonus_poin' => 'required|numeric|min:0',
        ]);

        $user = session('user');
        $role = session('role');
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

        $keranjangId = session('checkout_keranjang_id');
        $alamatId = session('checkout_id_alamat');
        $metode = session('checkout_metode_pengiriman');
        $totalHarga = session('checkout_total_harga');

        if (!$keranjangId || !$metode || !$totalHarga) {
            Log::error('Session data missing', [
                'keranjang_id' => $keranjangId,
                'metode' => $metode,
                'total_harga' => $totalHarga
            ]);
            return redirect()->back()->with('error', 'Data checkout tidak lengkap.');
        }

        $keranjang = Keranjang::with('detailKeranjang.itemKeranjang.barang')->findOrFail($keranjangId);
        $items = $keranjang->detailKeranjang->map->itemKeranjang;
        if ($items->isEmpty()) {
            Log::error('No items in keranjang', ['keranjang_id' => $keranjangId]);
            return redirect()->back()->with('error', 'Keranjang kosong atau tidak valid.');
        }

        $totalHargaBarang = $items->sum(fn($item) => $item->barang->harga_barang);
        $ongkir = ($totalHargaBarang >= 1500000 || $metode !== 'kurir') ? 0 : 100000;
        $tahunBulan = now()->format('Y.m.');
        $lastTransaksi = TransaksiPembelian::where('no_resi', 'like', $tahunBulan . '%')
            ->orderBy('no_resi', 'desc')
            ->first();
        $nomorUrut = $lastTransaksi ? (int)substr($lastTransaksi->no_resi, -3) + 1 : 1;
        $noResi = $tahunBulan . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        $poinDitukar = (int)$request->poin_ditukar;
        if ($poinDitukar > $pembeli->poin_pembeli) {
            Log::error('Poin ditukar lebih besar dari poin dimiliki', [
                'poin_ditukar' => $poinDitukar,
                'poin_pembeli' => $pembeli->poin_pembeli,
            ]);
            return redirect()->back()->with('error', 'Poin yang ditukar melebihi poin yang dimiliki.');
        }

        $path = $request->file('pembayaran')->store('pembayaran', 'public');

        try {
            DB::beginTransaction();

            $transaksi = TransaksiPembelian::create([
                'no_resi' => $noResi,
                'id_keranjang' => $keranjangId,
                'id_alamat' => $metode === 'kurir' ? $alamatId : null,
                'tanggal_pembelian' => now(),
                'waktu_pembayaran' => now(),
                'bukti_tf' => $path,
                'total_harga_barang' => $totalHargaBarang,
                'metode_pengiriman' => $metode,
                'ongkir' => $ongkir,
                'total_harga' => $request->total_final,
                'status_transaksi' => 'diproses',
                'poin_terpakai' => $poinDitukar,
                'poin_pembeli' => (int)$request->bonus_poin,
                'poin_penitip' => 0,
            ]);

            $bonusPoin = (int)$request->bonus_poin;
            $pembeli->poin_pembeli = ($pembeli->poin_pembeli - $poinDitukar) + $bonusPoin;
            $pembeli->save();

            foreach ($items as $item) {
                $item->barang->update(['status_barang' => 'sold']);
            }

            $selectedItems = session('checkout_selected_items');
            ItemKeranjang::whereIn('id_item_keranjang', $selectedItems)->delete();
            DetailKeranjang::where('id_keranjang', $keranjangId)->delete();
            $keranjang->delete();

            session()->forget([
                'checkout_keranjang_id',
                'checkout_selected_items',
                'checkout_metode_pengiriman',
                'checkout_id_alamat',
                'checkout_total_harga',
            ]);

            DB::commit();
            Log::info('Transaksi berhasil', [
                'transaksi_id' => $transaksi->id_pembelian,
                'poin_ditukar' => $poinDitukar,
                'bonus_poin' => $bonusPoin,
                'poin_pembeli_baru' => $pembeli->poin_pembeli,
            ]);

            return redirect()->route('index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan transaksi.');
        }
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

    public function history()
    {
        $user = session('user');
        if (!$user || session('role') !== 'pembeli') {
            Log::warning('Unauthorized access to history', ['user' => $user, 'role' => session('role')]);
            return redirect()->route('login')->with('error', 'Anda harus login sebagai pembeli.');
        }

        $idPembeli = $user['id'];
        $pembeli = Pembeli::find($idPembeli);
        if (!$pembeli) {
            Log::error('Pembeli not found', ['id_pembeli' => $idPembeli]);
            return redirect()->back()->with('error', 'Data pembeli tidak ditemukan.');
        }

        $riwayat = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip'])
            ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($idPembeli) {
                $query->where('id_pembeli', $idPembeli);
            })
            ->get();

        return view('Pembeli.history', compact('riwayat'));
    }

    public function showRatingPage($id)
    {
        $user = session('user');
        if (!$user || session('role') !== 'pembeli') {
            Log::warning('Unauthorized access to rating page', ['user' => $user, 'role' => session('role')]);
            return redirect()->route('login')->with('error', 'Anda harus login sebagai pembeli.');
        }

        $idPembeli = $user['id'];
        $transaksi = TransaksiPembelian::with('keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip')
            ->where('id_pembelian', $id)
            ->where('status_transaksi', 'selesai')
            ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($idPembeli) {
                $query->where('id_pembeli', $idPembeli);
            })
            ->firstOrFail();

        // Periksa apakah ada barang yang belum dirating
        $hasUnratedItems = $transaksi->keranjang->detailKeranjang->contains(function ($detail) {
            return is_null($detail->itemKeranjang->barang->rating);
        });

        if (!$hasUnratedItems) {
            return redirect()->route('pembeli.purchase')->with('info', 'Semua item dalam transaksi ini sudah dirating.');
        }

        return view('Pembeli.rating', compact('transaksi'));
    }

    public function rateTransaction(Request $request, $id)
    {
        $user = session('user');
        if (!$user || session('role') !== 'pembeli') {
            Log::warning('Unauthorized rating attempt', ['user' => $user, 'role' => session('role')]);
            return redirect()->route('login')->with('error', 'Anda harus login sebagai pembeli.');
        }

        $idPembeli = $user['id'];
        $transaksi = TransaksiPembelian::with('keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip')
            ->where('id_pembelian', $id)
            ->where('status_transaksi', 'selesai')
            ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($idPembeli) {
                $query->where('id_pembeli', $idPembeli);
            })
            ->firstOrFail();

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
        ], [
            'ratings.required' => 'Harap berikan rating untuk semua item.',
            'ratings.*.required' => 'Rating untuk setiap item wajib diisi.',
            'ratings.*.integer' => 'Rating harus berupa angka bulat.',
            'ratings.*.min' => 'Rating minimal adalah 1.',
            'ratings.*.max' => 'Rating maksimal adalah 5.',
        ]);

        try {
            DB::beginTransaction();

            $ratings = $request->ratings;
            foreach ($transaksi->keranjang->detailKeranjang as $detail) {
                $barang = $detail->itemKeranjang->barang;
                $barangId = $barang->id_barang;
                $rating = $ratings[$barangId] ?? null;

                if ($rating && is_null($barang->rating)) {
                    // Simpan rating di tabel barang
                    $barang->update(['rating' => $rating]);

                    // Update rata_rating dan banyak_rating di penitip
                    $transaksiPenitipan = $barang->transaksiPenitipan;
                    if ($transaksiPenitipan) {
                        $penitip = $transaksiPenitipan->penitip;
                        $currentAverage = $penitip->rata_rating ?? 0;
                        $currentCount = $penitip->banyak_rating ?? 0;

                        // Hitung rata-rata baru: ((rata_rating * banyak_rating) + rating) / (banyak_rating + 1)
                        $newAverage = ($currentCount > 0)
                            ? (($currentAverage * $currentCount) + $rating) / ($currentCount + 1)
                            : $rating;

                        // Update penitip
                        $penitip->update([
                            'rata_rating' => $newAverage,
                            'banyak_rating' => $currentCount + 1,
                        ]);

                        Log::info('Rating processed', [
                            'transaksi_id' => $id,
                            'barang_id' => $barangId,
                            'rating' => $rating,
                            'penitip_id' => $penitip->id_penitip,
                            'new_average' => $newAverage,
                            'new_count' => $currentCount + 1,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('pembeli.purchase')->with('success', 'Rating berhasil disubmit!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rating transaction: ' . $e->getMessage(), [
                'transaksi_id' => $id,
                'user_id' => $idPembeli,
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan rating.');
        }
    }
}