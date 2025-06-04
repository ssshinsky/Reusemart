<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\Keranjang;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\DetailKeranjang;
use App\Models\ItemKeranjang;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class TransaksiPembelianController extends Controller
{
    // Menampilkan daftar semua transaksi pembelian
    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::all();
        return response()->json($transaksiPembelian);
    }

    // Menampilkan transaksi pembelian berdasarkan ID
    // public function show($id)
    // {
    //     $transaksi = TransaksiPembelian::with(['keranjang.detailKeranjang.barang', 'alamat'])
    //                     ->where('id_pembelian', $id)
    //                     ->firstOrFail();

    //     return view('pembeli.purchase', compact('transaksi'));
    // }
    
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
        \Log::info('Bayar Request:', $request->all());

        try {
            // Validasi input
            $request->validate([
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'poin_ditukar' => 'nullable|integer|min:0',
                'total_final' => 'required|numeric|min:0',
                'bonus_poin' => 'nullable|integer|min:0',
            ]);

            $user = session('user');
            $idPembeli = $user['id'];
            $keranjangId = session('checkout_keranjang_id');
            $metodePengiriman = session('checkout_metode_pengiriman');
            $idAlamat = $metodePengiriman === 'ambil' ? null : session('checkout_id_alamat');
            $totalHarga = session('checkout_total_harga');

            // Validasi session
            if (!$keranjangId || !$metodePengiriman || !$totalHarga) {
                \Log::error('Missing session data', [
                    'keranjangId' => $keranjangId,
                    'metodePengiriman' => $metodePengiriman,
                    'totalHarga' => $totalHarga,
                ]);
                return redirect()->back()->with('error', 'Data checkout tidak lengkap.');
            }

            // Validasi keranjang
            $keranjang = Keranjang::find($keranjangId);
            if (!$keranjang) {
                \Log::error('Keranjang not found', ['keranjangId' => $keranjangId]);
                return redirect()->back()->with('error', 'Keranjang tidak ditemukan.');
            }

            // Validasi alamat (jika metode bukan 'ambil')
            if ($metodePengiriman === 'kurir' && $idAlamat && !Alamat::find($idAlamat)) {
                \Log::error('Alamat not found', ['idAlamat' => $idAlamat]);
                return redirect()->back()->with('error', 'Alamat tidak ditemukan.');
            }

            // Simpan bukti pembayaran
            $buktiTf = null;
            if ($request->hasFile('bukti_pembayaran')) {
                try {
                    $buktiTf = $request->file('bukti_pembayaran')->store('pembayaran', 'public');
                    \Log::info('File stored successfully', ['path' => $buktiTf]);
                } catch (\Exception $e) {
                    \Log::error('Failed to store file', ['error' => $e->getMessage()]);
                    return redirect()->back()->with('error', 'Gagal menyimpan bukti pembayaran: ' . $e->getMessage());
                }
            } else {
                \Log::error('No file uploaded');
                return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
            }

            // Hitung ongkir
            $ongkir = ($totalHarga >= 1500000 || $metodePengiriman !== 'kurir') ? 0 : 100000;

            // Log data sebelum create
            \Log::info('Data to create transaksi:', [
                'id_keranjang' => $keranjangId,
                'id_alamat' => $idAlamat,
                'no_resi' => 'RESI-' . strtoupper(uniqid()),
                'tanggal_pembelian' => now(),
                'waktu_pembayaran' => now(),
                'bukti_tf' => $buktiTf,
                'total_harga_barang' => $totalHarga - $ongkir,
                'metode_pengiriman' => $metodePengiriman,
                'ongkir' => $ongkir,
                'total_harga' => $request->total_final,
                'status_transaksi' => 'Menunggu Konfirmasi',
                'poin_terpakai' => $request->poin_ditukar ?? 0,
                'poin_pembeli' => $request->bonus_poin ?? 0,
                'poin_penitip' => 0,
            ]);

            $tahun = now()->format('Y');
            $bulan = now()->format('m');

            $jumlahTransaksiBulanIni = TransaksiPembelian::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count();

            $nomorUrut = str_pad($jumlahTransaksiBulanIni + 1, 3, '0', STR_PAD_LEFT);
            $noResi = $tahun . '.' . $bulan . '.' . $nomorUrut;

            // Simpan transaksi dalam transaksi database
            DB::beginTransaction();
            $transaksi = TransaksiPembelian::create([
                'id_keranjang' => $keranjangId,
                'id_alamat' => $idAlamat,
                'no_resi' => $noResi,
                'tanggal_pembelian' => now(),
                'waktu_pembayaran' => now(),
                'bukti_tf' => $buktiTf,
                'total_harga_barang' => $totalHarga - $ongkir,
                'metode_pengiriman' => $metodePengiriman,
                'ongkir' => $ongkir,
                'total_harga' => $request->total_final,
                'status_transaksi' => 'Menunggu Konfirmasi',
                'poin_terpakai' => $request->poin_ditukar ?? 0,
                'poin_pembeli' => $request->bonus_poin ?? 0,
                'poin_penitip' => 0,
            ]);

            // Update poin pembeli
            $pembeli = Pembeli::find($idPembeli);
            $newPoin = ($pembeli->poin_pembeli - ($request->poin_ditukar ?? 0)) + ($request->bonus_poin ?? 0);
            if ($newPoin < 0) {
                throw new \Exception('Poin pembeli tidak mencukupi.');
            }
            $pembeli->poin_pembeli = $newPoin;
            $pembeli->save();

            // Hapus item keranjang
            ItemKeranjang::whereIn('id_item_keranjang', session('checkout_selected_items'))->delete();

            // Commit transaksi
            DB::commit();

            // Clear session
            session()->forget(['checkout_keranjang_id', 'checkout_selected_items', 'checkout_metode_pengiriman', 'checkout_id_alamat', 'checkout_total_harga']);

            \Log::info('Transaksi created:', $transaksi->toArray());

            return redirect()->route('pembeli.riwayat')->with('success', 'Pembayaran berhasil! Menunggu konfirmasi admin.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in bayar', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create transaksi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
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

   public function show()
    {
        $transaksi = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.penitip',
            'keranjang.detailKeranjang.itemKeranjang.pembeli'
        ])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        return view('cs.transaksi-pembelian.index', compact('transaksi'));
    }

    public function verify(Request $request, $id_pembelian)
    {
        try {
            $transaksi = TransaksiPembelian::findOrFail($id_pembelian);

            if ($transaksi->status_transaksi !== 'Menunggu Konfirmasi') {
                \Log::warning('Transaksi sudah diproses', ['id_pembelian' => $id_pembelian, 'status' => $transaksi->status_transaksi]);
                return redirect()->back()->with('error', 'Transaksi sudah diproses atau tidak dalam status Menunggu Konfirmasi.');
            }

            $request->validate([
                'is_valid' => 'required|boolean',
            ]);

            \DB::beginTransaction();

            if ($request->is_valid) {
                $transaksi->status_transaksi = 'Disiapkan';
                $transaksi->save();

                $keranjang = Keranjang::find($transaksi->id_keranjang);
                $detailKeranjang = DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->get();
                $penitipIds = [];

                foreach ($detailKeranjang as $detail) {
                    $item = ItemKeranjang::find($detail->id_item_keranjang);
                    if ($item && $item->barang && $item->barang->penitip) {
                        $penitipIds[] = $item->barang->penitip->id_penitip;
                    }
                }

                $penitip = Penitip::whereIn('id_penitip', $penitipIds)->get();
                if ($penitip->isNotEmpty()) {
                    Notification::send($penitip, new TransaksiDisiapkanNotification($transaksi));
                    \Log::info('Notifikasi dikirim ke penitip', ['id_pembelian' => $id_pembelian, 'penitip_ids' => $penitipIds]);
                } else {
                    \Log::warning('Tidak ada penitip ditemukan untuk transaksi', ['id_pembelian' => $id_pembelian]);
                }

                \DB::commit();
                return redirect()->route('transaksi-pembelian.index')->with('success', 'Bukti pembayaran valid. Status transaksi diubah ke Disiapkan.');
            } else {
                $transaksi->status_transaksi = 'Dibatalkan';
                $transaksi->save();

                \DB::commit();
                \Log::info('Transaksi verified as invalid', ['id_pembelian' => $id_pembelian]);
                return redirect()->route('transaksi-pembelian.index')->with('success', 'Bukti pembayaran tidak valid. Transaksi dibatalkan.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed in verify', ['id_pembelian' => $id_pembelian, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to verify transaksi', ['id_pembelian' => $id_pembelian, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memverifikasi transaksi: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $transaksi = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.penitip',
            'keranjang.detailKeranjang.itemKeranjang.pembeli'
        ])
            ->where('no_resi', 'LIKE', "%{$query}%")
            ->orWhereHas('keranjang.detailKeranjang.itemKeranjang.pembeli', function ($q) use ($query) {
                $q->where('nama_pembeli', 'LIKE', "%{$query}%");
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        return view('cs.verifikasi_transaksi_table', compact('transaksi'))->render();
    }


}
