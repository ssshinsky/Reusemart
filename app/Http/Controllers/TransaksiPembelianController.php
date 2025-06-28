<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\Keranjang;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\DetailKeranjang;
use App\Models\ItemKeranjang;
use App\Models\Barang;
use App\Models\Alamat;
use App\Models\Schedule;
use App\Models\Delivery;
use App\Models\Komisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiPembelianController extends Controller
{
    private $baseUrl = 'http://10.53.9.31:8000/api';

    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip',
            'keranjang.detailKeranjang.itemKeranjang.pembeli'
        ])->get();
        return response()->json($transaksiPembelian);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'id_alamat' => 'nullable|exists:alamat,id_alamat',
            'no_resi' => 'required|string',
            'tanggal_pembelian' => 'required|date',
            'waktu_pembayaran' => 'nullable|date',
            'bukti_tf' => 'nullable|string',
            'total_harga_barang' => 'nullable|numeric',
            'metode_pengiriman' => 'required|in:kurir,ambil',
            'ongkir' => 'required|numeric',
            'tanggal_ambil' => 'nullable|date',
            'tanggal_pengiriman' => 'nullable|date',
            'total_harga' => 'nullable|numeric',
            'status_transaksi' => 'nullable|in:Menunggu Pembayaran,Menunggu Konfirmasi,Disiapkan,Dikirim,Selesai,Dibatalkan',
            'poin_terpakai' => 'nullable|integer|min:0',
            'poin_pembeli' => 'nullable|integer|min:0',
            'poin_penitip' => 'nullable|integer|min:0',
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
            'poin_terpakai' => $request->poin_terpakai ?? 0,
            'poin_pembeli' => $request->poin_pembeli ?? 0,
            'poin_penitip' => $request->poin_penitip ?? 0,
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
            'metode_pengiriman' => 'nullable|in:kurir,ambil',
            'ongkir' => 'nullable|numeric',
            'tanggal_ambil' => 'nullable|date',
            'tanggal_pengiriman' => 'nullable|date',
            'total_harga' => 'nullable|numeric',
            'status_transaksi' => 'nullable|in:Menunggu Pembayaran,Menunggu Konfirmasi,Disiapkan,Dikirim,Selesai,Dibatalkan',
            'poin_terpakai' => 'nullable|integer|min:0',
            'poin_pembeli' => 'nullable|integer|min:0',
            'poin_penitip' => 'nullable|integer|min:0',
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

        // Kembalikan status barang
        foreach ($keranjang->detailKeranjang as $detail) {
            $barang = $detail->itemKeranjang->barang;
            if ($barang) {
                $barang->status_barang = 'tersedia';
                $barang->save();
            }
        }

        DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->delete();
        $keranjang->delete();

        return redirect()->route('pembeli.cart')->with('success', 'Checkout dibatalkan otomatis karena melewati batas waktu.');
    }

    // Proses pembayaran
    public function bayar(Request $request)
    {
        Log::info('Bayar Request:', $request->all());

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
                Log::error('Missing session data', [
                    'keranjangId' => $keranjangId,
                    'metodePengiriman' => $metodePengiriman,
                    'totalHarga' => $totalHarga,
                ]);
                return redirect()->back()->with('error', 'Data checkout tidak lengkap.');
            }

            // Validasi keranjang
            $keranjang = Keranjang::find($keranjangId);
            if (!$keranjang) {
                Log::error('Keranjang not found', ['keranjangId' => $keranjangId]);
                return redirect()->back()->with('error', 'Keranjang tidak ditemukan.');
            }

            // Validasi alamat (jika metode bukan 'ambil')
            if ($metodePengiriman === 'kurir' && $idAlamat && !Alamat::find($idAlamat)) {
                Log::error('Alamat not found', ['idAlamat' => $idAlamat]);
                return redirect()->back()->with('error', 'Alamat tidak ditemukan.');
            }

            // Simpan bukti pembayaran
            $buktiTf = null;
            if ($request->hasFile('bukti_pembayaran')) {
                try {
                    $buktiTf = $request->file('bukti_pembayaran')->store('pembayaran', 'public');
                    Log::info('File stored successfully', ['path' => $buktiTf]);
                } catch (\Exception $e) {
                    Log::error('Failed to store file', ['error' => $e->getMessage()]);
                    return redirect()->back()->with('error', 'Gagal menyimpan bukti pembayaran: ' . $e->getMessage());
                }
            } else {
                Log::error('No file uploaded');
                return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
            }

            // Hitung ongkir
            $ongkir = ($totalHarga >= 1500000 || $metodePengiriman !== 'kurir') ? 0 : 100000;

            // Generate nomor resi
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

           ItemKeranjang::whereIn('id_item_keranjang', session('checkout_selected_items'))->update(['is_selected' => true]);

            // Commit transaksi
            DB::commit();

            // Clear session
            session()->forget(['checkout_keranjang_id', 'checkout_selected_items', 'checkout_metode_pengiriman', 'checkout_id_alamat', 'checkout_total_harga']);

            Log::info('Transaksi created:', $transaksi->toArray());

            return redirect()->route('pembeli.purchase')->with('success', 'Pembayaran berhasil! Menunggu konfirmasi admin.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed in bayar', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create transaksi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate(['bukti_tf' => 'required|image|mimes:jpeg,png,jpg|max:2048']);
        $transaksi = TransaksiPembelian::findOrFail($id);

        if ($request->hasFile('bukti_tf')) {
            $filename = time() . '.' . $request->bukti_tf->extension();
            $request->bukti_tf->storeAs('public/bukti_tf', $filename);
            $transaksi->bukti_tf = $filename;
            $transaksi->status_transaksi = 'Menunggu Konfirmasi';
            $transaksi->save();
        }

        return back()->with('success', 'Bukti transfer berhasil diupload.');
    }

    public function show()
    {
        $transaksi = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip',
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
                Log::warning('Transaksi sudah diproses', ['id_pembelian' => $id_pembelian, 'status' => $transaksi->status_transaksi]);
                return redirect()->back()->with('error', 'Transaksi sudah diproses atau tidak dalam status Menunggu Konfirmasi.');
            }

            $request->validate([
                'is_valid' => 'required|boolean',
            ]);

            DB::beginTransaction();

            if ($request->is_valid) {
                $transaksi->status_transaksi = 'Disiapkan';
                $transaksi->save();

                $keranjang = Keranjang::find($transaksi->id_keranjang);
                $detailKeranjang = DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->get();
                $penitipIds = [];

                foreach ($detailKeranjang as $detail) {
                    $item = ItemKeranjang::find($detail->id_item_keranjang);
                    if ($item && $item->barang) {
                        $barang = $item->barang;
                        $transaksiPenitipan = \App\Models\TransaksiPenitipan::find($barang->id_transaksi_penitipan);

                        if ($transaksiPenitipan) {
                            $penitipId = $transaksiPenitipan->id_penitip;
                            $penitipIds[] = $penitipId;
                        }
                    }
                }

                foreach ($keranjang->detailKeranjang as $detail) {
                    $barang = $detail->itemKeranjang->barang ?? null;
                    if ($barang) {
                        $barang->status_barang = 'sold'; 
                        $barang->save();
                    }
                }

                // // Kirim notifikasi ke penitip
                // foreach ($penitipIds as $penitipId) {
                //     Http::post($this->baseUrl . '/send-notification', [
                //         'user_id' => $penitipId,
                //         'role' => 'penitip',
                //         'title' => 'Barang Laku!',
                //         'body' => 'Barang Anda dalam transaksi ' . $transaksi->no_resi . ' telah terjual.',
                //         'type' => 'barang_laku',
                //         'id' => $transaksi->id_keranjang,
                //     ]);
                // }

                // // Kirim notifikasi ke pembeli
                // $pembeliId = $keranjang->id_pembeli;
                // Http::post($this->baseUrl . '/send-notification', [
                //     'user_id' => $pembeliId,
                //     'role' => 'pembeli',
                //     'title' => 'Pembayaran Terverifikasi',
                //     'body' => 'Pembayaran untuk transaksi ' . $transaksi->no_resi . ' telah diverifikasi.',
                //     'type' => 'barang_laku',
                //     'id' => $transaksi->id_keranjang,
                // ]);

                DB::commit();
                return redirect()->route('transaksi-pembelian.index')->with('success', 'Bukti pembayaran valid. Status transaksi diubah ke Disiapkan.');
            } else {
                $transaksi->status_transaksi = 'Dibatalkan';
                $transaksi->save();

                // Kembalikan status barang
                $keranjang = Keranjang::find($transaksi->id_keranjang);
                foreach ($keranjang->detailKeranjang as $detail) {
                    $barang = $detail->itemKeranjang->barang;
                    if ($barang) {
                        $barang->status_barang = 'tersedia';
                        $barang->save();
                    }
                }

                // // Kirim notifikasi ke pembeli
                // $pembeliId = $keranjang->id_pembeli;
                // Http::post($this->baseUrl . '/send-notification', [
                //     'user_id' => $pembeliId,
                //     'role' => 'pembeli',
                //     'title' => 'Transaksi Dibatalkan',
                //     'body' => 'Transaksi ' . $transaksi->no_resi . ' dibatalkan karena bukti pembayaran tidak valid.',
                //     'type' => 'transaksi_dibatalkan',
                //     'id' => $transaksi->id_keranjang,
                // ]);

                DB::commit();
                return redirect()->route('transaksi-pembelian.index')->with('success', 'Bukti pembayaran tidak valid. Transaksi dibatalkan.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed in verify', ['id_pembelian' => $id_pembelian, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to verify transaksi', ['id_pembelian' => $id_pembelian, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memverifikasi transaksi: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $transaksi = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip',
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

    public function riwayat(Request $request)
    {
        $user = session('user');
        $role = session('role');

        if ($role !== 'pembeli' || !$user) {
            Log::error('Unauthorized access to riwayat', ['user' => $user, 'role' => $role]);
            return response('Unauthorized', 403);
        }

        $idPembeli = $user['id'];
        Log::info('Fetching riwayat for id_pembeli', ['id_pembeli' => $idPembeli]);

        $transaksi = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang'])
            ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
            ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
            ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
            ->where('item_keranjang.id_pembeli', $idPembeli)
            ->orderBy('transaksi_pembelian.created_at', 'desc')
            ->select('transaksi_pembelian.*')
            ->distinct()
            ->paginate(10);

        Log::info('Riwayat results', ['count' => $transaksi->count(), 'data' => $transaksi->toArray()]);

        return view('pembeli.history', compact('transaksi'));
    }

    public function detail($id)
    {
        $user = session('user');
        $role = session('role');

        if ($role !== 'pembeli' || !$user) {
            Log::error('Unauthorized access to detail', ['user' => $user, 'role' => $role]);
            return response('Unauthorized', 403);
        }

        $idPembeli = $user['id'];
        Log::info('Fetching detail for id_pembeli and transaction id', ['id_pembeli' => $idPembeli, 'id_transaksi' => $id]);

        $transaksi = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang'])
            ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
            ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
            ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
            ->where('item_keranjang.id_pembeli', $idPembeli)
            ->where('transaksi_pembelian.id_pembelian', $id)
            ->select('transaksi_pembelian.*')
            ->first();

        if (!$transaksi) {
            Log::error('No transaction found', ['id_pembeli' => $idPembeli, 'id_transaksi' => $id]);
            abort(404, 'Transaksi tidak ditemukan');
        }

        Log::info('Detail transaction', ['transaksi' => $transaksi->toArray()]);

        return view('pembeli.transaksi_detail', compact('transaksi'));
    }

    public function riwayatPembelian()
    {
        $riwayat = TransaksiPembelian::with('keranjang.detailKeranjang.itemKeranjang.barang')
            ->where('id_pembeli', Auth::guard('pembeli')->id())
            ->latest()
            ->get();

        return view('pembeli.riwayat', compact('riwayat'));
    }

    public function detailPembelian($id)
    {
        $transaksi = TransaksiPembelian::with('keranjang.detailKeranjang.itemKeranjang.barang', 'alamat')
            ->where('id_pembeli', Auth::guard('pembeli')->id())
            ->findOrFail($id);

        return view('pembeli.riwayat_detail', compact('transaksi'));
    }

    public function updateDeliveryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,dikirim,selesai',
        ]);

        $delivery = Delivery::findOrFail($id);
        $delivery->update(['status' => $request->status]);

        if ($request->status === 'dikirim') {
            $users = [
                ['id' => $delivery->penitip_id, 'role' => 'penitip'],
                ['id' => $delivery->pembeli_id, 'role' => 'pembeli'],
            ];

            foreach ($users as $user) {
                Http::post($this->baseUrl . '/send-notification', [
                    'user_id' => $user['id'],
                    'role' => $user['role'],
                    'title' => 'Barang Telah Dikirim',
                    'body' => 'Barang dengan ID ' . $delivery->barang_id . ' telah dikirim oleh kurir.',
                    'type' => 'barang_dikirim',
                    'id' => $delivery->barang_id,
                ]);
            }
        }

        return response()->json(['message' => 'Delivery status updated']);
    }

    public function validatePayment(Request $request, $transactionId)
    {
        $transaction = TransaksiPembelian::findOrFail($transactionId);
        $transaction->update(['status_transaksi' => 'Disiapkan']);

        $keranjang = Keranjang::find($transaction->id_keranjang);
        $detailKeranjang = DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->get();
        $penitipIds = [];

        foreach ($detailKeranjang as $detail) {
            $item = ItemKeranjang::find($detail->id_item_keranjang);
            if ($item && $item->barang) {
                $barang = $item->barang;
                $transaksiPenitipan = \App\Models\TransaksiPenitipan::find($barang->id_transaksi_penitipan);

                if ($transaksiPenitipan) {
                    $penitipId = $transaksiPenitipan->id_penitip;
                    $penitipIds[] = $penitipId;
                }
            }
        }

        foreach ($penitipIds as $penitipId) {
            Http::post($this->baseUrl . '/send-notification', [
                'user_id' => $penitipId,
                'role' => 'penitip',
                'title' => 'Barang Laku!',
                'body' => 'Barang Anda dalam transaksi ' . $transaction->no_resi . ' telah terjual.',
                'type' => 'barang_laku',
                'id' => $transaction->id_keranjang,
            ]);
        }

        Http::post($this->baseUrl . '/send-notification', [
            'user_id' => $keranjang->id_pembeli,
            'role' => 'pembeli',
            'title' => 'Pembayaran Terverifikasi',
            'body' => 'Pembayaran untuk transaksi ' . $transaction->no_resi . ' telah diverifikasi.',
            'type' => 'barang_laku',
            'id' => $transaction->id_keranjang,
        ]);

        return response()->json(['message' => 'Payment validated']);
    }

    public function createDeliverySchedule(Request $request)
    {
        $request->validate([
            'penitip_id' => 'required|exists:penitip,id_penitip',
            'pembeli_id' => 'required|exists:pembeli,id_pembeli',
            'kurir_id' => 'required|exists:pegawai,id_pegawai',
            'barang_id' => 'required|exists:barang,id_barang',
            'tanggal_pengiriman' => 'required|date',
        ]);

        $schedule = Schedule::create($request->all());

        $users = [
            ['id' => $schedule->penitip_id, 'role' => 'penitip'],
            ['id' => $schedule->pembeli_id, 'role' => 'pembeli'],
            ['id' => $schedule->kurir_id, 'role' => 'pegawai'],
        ];

        foreach ($users as $user) {
            Http::post($this->baseUrl . '/send-notification', [
                'user_id' => $user['id'],
                'role' => $user['role'],
                'title' => 'Jadwal Pengiriman',
                'body' => 'Jadwal pengiriman untuk barang ID ' . $schedule->barang_id . ' telah dibuat.',
                'type' => 'jadwal_pengiriman',
                'id' => $schedule->id,
            ]);
        }

        return response()->json(['message' => 'Delivery schedule created']);
    }

    public function createPickupSchedule(Request $request)
    {
        $request->validate([
            'penitip_id' => 'required|exists:penitip,id_penitip',
            'pembeli_id' => 'required|exists:pembeli,id_pembeli',
            'barang_id' => 'required|exists:barang,id_barang',
            'tanggal_ambil' => 'required|date',
        ]);

        $schedule = Schedule::create($request->all());

        $users = [
            ['id' => $schedule->penitip_id, 'role' => 'penitip'],
            ['id' => $schedule->pembeli_id, 'role' => 'pembeli'],
        ];

        foreach ($users as $user) {
            Http::post($this->baseUrl . '/send-notification', [
                'user_id' => $user['id'],
                'role' => $user['role'],
                'title' => 'Jadwal Pengambilan Barang',
                'body' => 'Jadwal pengambilan untuk barang ID ' . $schedule->barang_id . ' telah dibuat.',
                'type' => 'jadwal_pengambilan',
                'id' => $schedule->id,
            ]);
        }

        return response()->json(['message' => 'Pickup schedule created']);
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
        
        $riwayatQuery = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip'])
            ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($idPembeli) {
                $query->where('id_pembeli', $idPembeli);
            });
        
        $riwayatQuery->latest();

        $riwayat = $riwayatQuery->get();
        $topSeller = Penitip::where('badge', 1)->first();

        return view('Pembeli.history', compact('riwayat', 'topSeller'));
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
                    $barang->update(['rating' => $rating]);

                    $transaksiPenitipan = $barang->transaksiPenitipan;
                    if ($transaksiPenitipan) {
                        $penitip = $transaksiPenitipan->penitip;
                        $currentAverage = $penitip->rata_rating ?? 0;
                        $currentCount = $penitip->banyak_rating ?? 0;

                        $newAverage = ($currentCount > 0)
                            ? (($currentAverage * $currentCount) + $rating) / ($currentCount + 1)
                            : $rating;

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
            Log::error('Error rating transaction', ['transaksi_id' => $id, 'user_id' => $idPembeli, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan rating.');
        }
    }

    public function processTopSeller()
    {
        Log::info('processTopSeller started', ['user_id' => Auth::guard('pegawai')->id()]);
        $this->ensureAdmin();

        try {
            DB::beginTransaction();

            $lastMonth = Carbon::now()->subMonth()->format('Y-m');
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $existingTopSeller = Penitip::where('badge', 1)
                ->where('updated_at', '>=', $lastMonthStart)
                ->where('updated_at', '<=', $lastMonthEnd)
                ->exists();
            Log::info('existingTopSeller check', ['exists' => $existingTopSeller]);
            if ($existingTopSeller) {
                DB::rollBack();
                return redirect()->back()->with('error', "Top Seller untuk bulan $lastMonth sudah diproses.");
            }

            Penitip::where('badge', 1)->update(['badge' => 0]);

            $topSeller = TransaksiPembelian::where('status_transaksi', 'Selesai')
                ->whereBetween('tanggal_pembelian', [$lastMonthStart, $lastMonthEnd])
                ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
                ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
                ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
                ->join('barang', 'item_keranjang.id_barang', '=', 'barang.id_barang')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                ->select(
                    'penitip.id_penitip',
                    'penitip.nama_penitip',
                    DB::raw('SUM(keranjang.banyak_barang) as sold_count')
                )
                ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
                ->orderByDesc('sold_count')
                ->first();

            Log::info('TopSeller query result', ['topSeller' => $topSeller ? $topSeller->toArray() : null]);
            if (!$topSeller) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tidak ada transaksi selesai bulan lalu untuk memilih Top Seller.');
            }

            $totalSales = TransaksiPembelian::where('status_transaksi', 'Selesai')
                ->whereBetween('tanggal_pembelian', [$lastMonthStart, $lastMonthEnd])
                ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
                ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
                ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
                ->join('barang', 'item_keranjang.id_barang', '=', 'barang.id_barang')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->where('transaksi_penitipan.id_penitip', $topSeller->id_penitip)
                ->sum(DB::raw('barang.harga_barang * keranjang.banyak_barang'));

            $bonusPoints = floor($totalSales * 0.005);

            $penitip = Penitip::findOrFail($topSeller->id_penitip);
            $penitip->update([
                'badge' => 1,
                'poin_penitip' => ($penitip->poin_penitip ?? 0) + $bonusPoints
            ]);

            Log::info('Top Seller processed', [
                'month' => $lastMonth,
                'penitip_id' => $topSeller->id_penitip,
                'nama_penitip' => $topSeller->nama_penitip,
                'sold_count' => $topSeller->sold_count,
                'total_sales' => $totalSales,
                'bonus_points' => $bonusPoints
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Memberikan Top Seller $lastMonth: {$topSeller->nama_penitip} sebanyak $bonusPoints poin.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Top Seller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal memproses Top Seller: ' . $e->getMessage());
        }
    }

    public function getPurchaseHistory()
    {
        try {
            Log::debug('Starting getPurchaseHistory', ['user_id' => Auth::guard('pembeli')->id()]);
            $pembeli = Auth::guard('pembeli')->user();

            if (!$pembeli) {
                Log::warning('Pembeli not authenticated');
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $riwayat = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip'])
                ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($pembeli) {
                    $query->where('id_pembeli', $pembeli->id_pembeli);
                })
                ->take(50)
                ->get()
                ->map(function ($transaksi) {
                    return [
                        'id_pembelian' => $transaksi->id_pembelian,
                        'tanggal_transaksi' => $transaksi->created_at ? $transaksi->created_at->format('Y-m-d') : 'Unknown',
                        'total_harga' => $transaksi->total_harga ?? 0,
                        'ongkir' => $transaksi->ongkir ?? 0,
                        'metode_pengiriman' => $transaksi->metode_pengiriman ?? 'Unknown',
                        'status_transaksi' => $transaksi->status_transaksi ?? 'Unknown',
                        'items' => $transaksi->keranjang->detailKeranjang->map(function ($detail) {
                            $barang = $detail->itemKeranjang->barang;
                            return [
                                'nama_barang' => $barang->nama_barang ?? 'Unknown',
                                'harga_barang' => $barang->harga_barang ?? 0,
                                'rating' => $barang->rating ?? null,
                                'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                    ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                    : null,
                            ];
                        })->toArray(),
                    ];
                });

            Log::info('Purchase history fetched', ['count' => count($riwayat)]);
            return response()->json($riwayat);
        } catch (\Exception $e) {
            Log::error('Error in getPurchaseHistory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching purchase history'], 500);
        }
    }

    public function getPurchaseHistoryById($id)
    {
        try {
            Log::debug('Fetching purchase history by ID', ['id_pembeli' => $id]);

            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['message' => 'Invalid ID'], 400);
            }

            $pembeli = Pembeli::find($id);
            if (!$pembeli) {
                Log::warning('Pembeli not found', ['id_pembeli' => $id]);
                return response()->json(['message' => 'Pembeli not found'], 404);
            }

            $riwayat = TransaksiPembelian::with(['keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.penitip'])
                ->whereHas('keranjang.detailKeranjang.itemKeranjang', function ($query) use ($id) {
                    $query->where('id_pembeli', $id);
                })
                ->take(50)
                ->get()
                ->map(function ($transaksi) {
                    return [
                        'id_pembelian' => $transaksi->id_pembelian,
                        'tanggal_transaksi' => $transaksi->created_at ? $transaksi->created_at->format('Y-m-d') : 'Unknown',
                        'total_harga' => $transaksi->total_harga ?? 0,
                        'ongkir' => $transaksi->ongkir ?? 0,
                        'metode_pengiriman' => $transaksi->metode_pengiriman ?? 'Unknown',
                        'status_transaksi' => $transaksi->status_transaksi ?? 'Unknown',
                        'items' => $transaksi->keranjang->detailKeranjang->map(function ($detail) {
                            $barang = $detail->itemKeranjang->barang;
                            return [
                                'nama_barang' => $barang->nama_barang ?? 'Unknown',
                                'harga_barang' => $barang->harga_barang ?? 0,
                                'rating' => $barang->rating ?? null,
                                'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                    ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                    : null,
                            ];
                        })->toArray(),
                    ];
                });

            Log::info('Purchase history fetched', ['id_pembeli' => $id, 'count' => count($riwayat)]);
            return response()->json($riwayat);
        } catch (\Exception $e) {
            Log::error('Error fetching purchase history by ID', [
                'id_pembeli' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching purchase history'], 500);
        }
    }

    /**
     * Update status barang yang lewat 7 hari dari tanggal_berakhir
     * dan masih 'menunggu pengambilan penitip' jadi 'barang untuk donasi'
     */
    protected function updateExpiredConsignmentItems()
    {
        try {
            $sevenDaysAgo = Carbon::now()->subDays(7);

            // Ambil barang yang memenuhi syarat
            $expiredItems = Barang::where('status_barang', 'menunggu pengambilan penitip')
                ->where('tanggal_berakhir', '<=', $sevenDaysAgo)
                ->get();

            if ($expiredItems->isEmpty()) {
                Log::info('No expired consignment items found for donation update.');
                return;
            }

            // Update status barang
            $updatedCount = 0;
            foreach ($expiredItems as $item) {
                $item->status_barang = 'For Donation';
                $item->save();
                $updatedCount++;

                Log::info("Updated item to donation: id_barang={$item->id_barang}, nama_barang={$item->nama_barang}, tanggal_berakhir={$item->tanggal_berakhir}");
            }

            Log::info("Updated {$updatedCount} expired consignment items to 'barang untuk donasi'.");
        } catch (\Exception $e) {
            Log::error("Error updating expired consignment items: {$e->getMessage()}");
        }
    }

    public function indextop()
    {
        // Panggil fungsi update status barang
        $this->updateExpiredConsignmentItems();

        // Ambil barang terbatas (logika existing)
        $barangTerbatas = \App\Models\Barang::with('gambar')
            ->where('status_barang', 'tersedia')
            ->take(12) // Contoh limit
            ->get();

        // Ambil Top Seller
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');

        $topSeller = Penitip::where('badge', 1)->first();

        $topSellerDetails = null;
        if ($topSeller) {
            $topSellerDetails = TransaksiPembelian::where('status_transaksi', 'Selesai')
                ->whereBetween('tanggal_pembelian', [$lastMonthStart, $lastMonthEnd])
                ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
                ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
                ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
                ->join('barang', 'item_keranjang.id_barang', '=', 'barang.id_barang')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->where('transaksi_penitipan.id_penitip', $topSeller->id_penitip)
                ->select(
                    'penitip.id_penitip',
                    'penitip.nama_penitip',
                    DB::raw('SUM(keranjang.banyak_barang) as sold_count'),
                    DB::raw('SUM(barang.harga_barang * keranjang.banyak_barang) as total_sales')
                )
                ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
                ->first();
        }

        return view('welcome', compact('barangTerbatas', 'topSeller', 'topSellerDetails', 'lastMonth'));
    }

    public function indextopapi()
    {
        // Ambil Top Seller
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');

        $topSeller = Penitip::where('badge', 1)->first();

        $topSellerDetails = null;
        if ($topSeller) {
            $topSellerDetails = TransaksiPembelian::where('status_transaksi', 'Selesai')
                ->whereBetween('tanggal_pembelian', [$lastMonthStart, $lastMonthEnd])
                ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
                ->join('detail_keranjang', 'keranjang.id_keranjang', '=', 'detail_keranjang.id_keranjang')
                ->join('item_keranjang', 'detail_keranjang.id_item_keranjang', '=', 'item_keranjang.id_item_keranjang')
                ->join('barang', 'item_keranjang.id_barang', '=', 'barang.id_barang')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->where('transaksi_penitipan.id_penitip', $topSeller->id_penitip)
                ->select(
                    'penitip.id_penitip',
                    'penitip.nama_penitip',
                    'penitip.profil_pict',
                    DB::raw('SUM(keranjang.banyak_barang) as sold_count'),
                    DB::raw('SUM(barang.harga_barang * keranjang.banyak_barang) as total_sales')
                )
                ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                ->groupBy('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                ->first();
        }

        $response = [
            'last_month' => $lastMonth,
            'top_seller' => $topSellerDetails ? [
                'id_penitip' => $topSellerDetails->id_penitip,
                'nama_penitip' => $topSellerDetails->nama_penitip,
                'profil_pict' => $topSellerDetails->profil_pict ? asset('storage/' . $topSellerDetails->profil_pict) : null,
                'sold_count' => (int) $topSellerDetails->sold_count,
                'total_sales' => (float) $topSellerDetails->total_sales,
            ] : null,
        ];

        return response()->json($response);
    }

    public function processTransactionCompletion($id_pembelian)
    {
        DB::transaction(function () use ($id_pembelian) {
            $transaksi = TransaksiPembelian::with([
                'pembeli',
                'keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan',
                'keranjang.detailKeranjang.itemKeranjang.barang.transaksiPenitipan.hunter',
            ])->find($id_pembelian);

            if (!$transaksi) {
                abort(404, 'Transaksi tidak ditemukan.');
            }

            // Pemicu: Jika status transaksi berubah menjadi 'Done', 'Expired', atau 'In Delivery'
            $isTransactionDone = $transaksi->status_transaksi === 'Done';
            $isTransactionExpired = $transaksi->status_transaksi === 'Expired';
            $isTransactionInDelivery = $transaksi->status_transaksi === 'In Delivery';

            if (!$isTransactionDone && !$isTransactionExpired && !$isTransactionInDelivery) {
                return response()->json(['message' => 'Transaksi belum pada status final (Done/Expired/In Delivery).'], 400);
            }

            if ($transaksi->keranjang && $transaksi->keranjang->detailKeranjang) {
                foreach ($transaksi->keranjang->detailKeranjang as $detailKeranjang) {
                    $barang = $detailKeranjang->itemKeranjang->barang;
                // --- Lakukan Perhitungan untuk setiap barang dalam transaksi ---
                
                    $hargaJual = $barang->harga_barang;
                    $isPerpanjangan = (bool) $barang->perpanjangan;
                    $isHunting = !is_null($barang->transaksiPenitipan->id_hunter);
                    $tanggalPenitipan = Carbon::parse($barang->transaksiPenitipan->tanggal_penitipan);
                    $tanggalLaku = Carbon::parse($transaksi->waktu_pembayaran); // Tanggal laku adalah waktu pembayaran

                    // 1. Hitung Komisi ReuseMart Dasar (20% atau 30%)
                    $komisiReuseMartDasar = ($isPerpanjangan ? 0.30 : 0.20) * $hargaJual;

                    // 2. Hitung Pengurang Komisi untuk Hunter (5% jika hasil hunting)
                    $pengurangHunter = 0;
                    if ($isHunting) {
                        $pengurangHunter = 0.05 * $hargaJual;
                    }

                    // 3. Hitung Bonus Terjual Cepat untuk Penitip (10% dari Komisi ReuseMart Dasar)
                    $bonusPenitipTerjualCepat = 0;
                    // Hanya berikan bonus jika barang terjual dan laku < 7 hari
                    if ($isTransactionDone && $tanggalPenitipan->diffInDays($tanggalLaku) < 7) {
                        $bonusPenitipTerjualCepat = 0.10 * $komisiReuseMartDasar;
                    }

                    // 4. Hitung Komisi ReuseMart Final
                    $komisiReuseMartFinal = $komisiReuseMartDasar - $pengurangHunter - $bonusPenitipTerjualCepat;
                    // Pastikan tidak ada komisi negatif
                    if ($komisiReuseMartFinal < 0) {
                        $komisiReuseMartFinal = 0;
                    }

                    // 5. Hitung Penghasilan Penitip
                    // Penghasilan penitip = Harga Jual - Komisi yang diambil ReuseMart
                    $penghasilanPenitip = $hargaJual - $komisiReuseMartFinal - $pengurangHunter;

                    // Simpan Komisi ke Tabel `komisi` 
                    Komisi::create([
                        'id_pembelian' => $transaksi->id_pembelian,
                        'id_penitip' => $barang->transaksiPenitipan->id_penitip,
                        'id_hunter' => $barang->transaksiPenitipan->id_hunter,
                        'id_owner' => 1, // Ganti dengan ID owner yang sesuai
                        'komisi_hunter' => $pengurangHunter,
                        'komisi_penitip' => $penghasilanPenitip,
                        'komisi_reusemart' => $komisiReuseMartFinal,
                        'bonus_penitip_terjual_cepat' => $bonusPenitipTerjualCepat
                    ]);

                    // --- Menambahkan Saldo ke Akun Penitip ---
                    $penitip = Penitip::find($barang->transaksiPenitipan->id_penitip);
                    if ($penitip) {
                        $penitip->saldo_penitip += $penghasilanPenitip;
                        $penitip->save();
                    }

                    // --- Update status_barang ---
                    if ($isTransactionDone || $isTransactionInDelivery) {
                        $barang->status_barang = 'Sold';
                    } elseif ($isTransactionExpired) {
                        $barang->status_barang = 'For Donation';
                    } 
                    $barang->save();
                }
            }

            // --- Menambahkan Poin ke Akun Pembeli (Hanya jika transaksi 'Done') ---
            if ($isTransactionDone) {
                $pembeli = $transaksi->pembeli;
                if ($pembeli) {
                    // total_harga_barang adalah total dari harga_barang di keranjang, sebelum ongkir/potongan poin
                    $totalBelanjaSebelumPotongan = $transaksi->total_harga_barang;
                    $totalBelanjaSetelahPotonganPoin = $transaksi->total_harga_barang - ($transaksi->poin_terpakai * 100);

                    $poinDasar = floor($totalBelanjaSetelahPotonganPoin / 10000);

                    $bonusPoin = 0;
                    if ($totalBelanjaSebelumPotongan > 500000) { // Cek total belanja sebelum potongan poin untuk bonus
                        $bonusPoin = floor(0.20 * $poinDasar);
                    }

                    $totalPoinDidapat = $poinDasar + $bonusPoin;

                    $pembeli->poin_pembeli += $totalPoinDidapat;
                    $pembeli->save();
                }
            }
        });
        return response()->json(['message' => 'Proses penyelesaian transaksi berhasil.'], 200);
    }

    // Contoh endpoint untuk mengubah status transaksi menjadi Done atau Expired
    public function updateTransactionStatus(Request $request, $id_pembelian)
    {
        $request->validate([
            'status' => 'required|in:Done,Canceled', // Memasukkan "Expired" sebagai "Canceled" di DB
        ]);

        $transaksi = TransaksiPembelian::find($id_pembelian);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $transaksi->status_transaksi = $request->status;
        $transaksi->save();

        // Panggil fungsi proses setelah status diupdate
        $this->processTransactionCompletion($id_pembelian);

        return response()->json(['message' => 'Status transaksi berhasil diperbarui dan diproses.'], 200);
    }
}