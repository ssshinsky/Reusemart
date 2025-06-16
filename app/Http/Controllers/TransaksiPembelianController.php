<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\Komisi;
use App\Models\Keranjang;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\DetailKeranjang;
use App\Models\ItemKeranjang;
use App\Models\Alamat;
use App\Models\Schedule;
use App\Models\Delivery;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiPembelianController extends Controller
{
//     private $baseUrl = 'http://10.53.9.31:8000/api';
  
    // 🔓 API: Menampilkan semua transaksi pembelian
    public function index()
    {
        $transaksiPembelian = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.barang.penitip',
            'keranjang.detailKeranjang.itemKeranjang.pembeli'
        ])->get();
        return response()->json($transaksiPembelian);
    }
  
    // 🔓 API: Menampilkan satu transaksi pembelian
    public function showAPI($id)
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

    // Membatalkan transaksi secara otomatis
    public function batalkanOtomatis($id)
    {
        $keranjang = Keranjang::with('detailKeranjang.itemKeranjang.barang')->find($id);

        if (!$keranjang) {
            return redirect()->route('pembeli.cart')->with('error', 'Keranjang tidak ditemukan.');
        }

        // Kembalikan stok barang
        foreach ($keranjang->detailKeranjang as $detail) {
            $barang = $detail->itemKeranjang->barang;
            if ($barang) {
                $barang->stok += $detail->jumlah; // Asumsi ada kolom stok di model Barang
                $barang->save();
            }
        }

        // Hapus data keranjang dan detail
        DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->delete();
        $keranjang->delete();

        return redirect()->route('pembeli.keranjang')->with('success', 'Checkout dibatalkan otomatis karena melewati batas waktu.');
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

            // Hapus item keranjang
            ItemKeranjang::whereIn('id_item_keranjang', session('checkout_selected_items'))->delete();

            // Commit transaksi
            DB::commit();

            // Clear session
            session()->forget(['checkout_keranjang_id', 'checkout_selected_items', 'checkout_metode_pengiriman', 'checkout_id_alamat', 'checkout_total_harga']);

            Log::info('Transaksi created:', $transaksi->toArray());

            return redirect()->route('pembeli.riwayat')->with('success', 'Pembayaran berhasil! Menunggu konfirmasi admin.');
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

    // Upload bukti transfer
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

    // Menampilkan daftar transaksi untuk CS
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

    // Verifikasi transaksi
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
                    if ($item && $item->barang && $item->barang->penitip) {
                        $penitipIds[] = $item->barang->penitip->id_penitip;
                    }
                }

                // Kirim notifikasi ke penitip
                foreach ($penitipIds as $penitipId) {
                    Http::post($this->baseUrl . '/send-notification', [
                        'user_id' => $penitipId,
                        'role' => 'penitip',
                        'title' => 'Barang Laku!',
                        'body' => 'Barang Anda dalam transaksi ' . $transaksi->no_resi . ' telah terjual.',
                        'type' => 'barang_laku',
                        'id' => $transaksi->id_keranjang,
                    ]);
                }

                // Kirim notifikasi ke pembeli
                $pembeliId = $keranjang->id_pembeli;
                Http::post($this->baseUrl . '/send-notification', [
                    'user_id' => $pembeliId,
                    'role' => 'pembeli',
                    'title' => 'Pembayaran Terverifikasi',
                    'body' => 'Pembayaran untuk transaksi ' . $transaksi->no_resi . ' telah diverifikasi.',
                    'type' => 'barang_laku',
                    'id' => $transaksi->id_keranjang,
                ]);

                DB::commit();
                return redirect()->route('transaksi-pembelian.index')->with('success', 'Bukti pembayaran valid. Status transaksi diubah ke Disiapkan.');
            } else {
                $transaksi->status_transaksi = 'Dibatalkan';
                $transaksi->save();

                // Kembalikan stok barang
                $keranjang = Keranjang::find($transaksi->id_keranjang);
                foreach ($keranjang->detailKeranjang as $detail) {
                    $barang = $detail->itemKeranjang->barang;
                    if ($barang) {
                        $barang->stok += $detail->jumlah;
                        $barang->save();
                    }
                }

                // Kirim notifikasi ke pembeli
                $pembeliId = $keranjang->id_pembeli;
                Http::post($this->baseUrl . '/send-notification', [
                    'user_id' => $pembeliId,
                    'role' => 'pembeli',
                    'title' => 'Transaksi Dibatalkan',
                    'body' => 'Transaksi ' . $transaksi->no_resi . ' dibatalkan karena bukti pembayaran tidak valid.',
                    'type' => 'transaksi_dibatalkan',
                    'id' => $transaksi->id_keranjang,
                ]);

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

    // Pencarian transaksi
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

    // Menampilkan riwayat transaksi pembeli
    public function riwayatAPI(Request $request)
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

    // Menampilkan detail transaksi
    public function detailAPI($id)
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

    // Mengupdate status pengiriman
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

    // Validasi pembayaran
    public function validatePayment(Request $request, $transactionId)
    {
        $transaction = TransaksiPembelian::findOrFail($transactionId);
        $transaction->update(['status_transaksi' => 'Disiapkan']);

        $keranjang = Keranjang::find($transaction->id_keranjang);
        $detailKeranjang = DetailKeranjang::where('id_keranjang', $keranjang->id_keranjang)->get();
        $penitipIds = [];

        foreach ($detailKeranjang as $detail) {
            $item = ItemKeranjang::find($detail->id_item_keranjang);
            if ($item && $item->barang && $item->barang->penitip) {
                $penitipIds[] = $item->barang->penitip->id_penitip;
            }
        }

        // Kirim notifikasi ke penitip
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

        // Kirim notifikasi ke pembeli
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

    // Membuat jadwal pengiriman
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

    // Membuat jadwal pengambilan barang
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
