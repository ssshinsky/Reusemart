<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenitipan;
use App\Models\Barang;
use App\Models\Gambar;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Kategori;
use App\Models\KelolaTransaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class TransaksiPenitipanController extends Controller
{
    /**
     * Memastikan pengguna adalah gudang (id_role = 4)
     */
    private function ensureGudang()
    {
        $this->cekTransaksiHangus();
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 4) {
            abort(403, 'Akses ditolak. Hanya pengguna gudang yang diizinkan.');
        }
    }

    private function cekTransaksiHangus()
    {
        $transaksis = TransaksiPembelian::where('status_transaksi', 'Ready for Pickup')
            ->whereNotNull('tanggal_pengambilan')
            ->whereNull('tanggal_ambil')
            ->whereDate('tanggal_pengambilan', '<', Carbon::now()->subDays(2))
            ->with([
                'keranjang.detailKeranjang.itemKeranjang.barang', 
            ])
            ->get();

        foreach ($transaksis as $transaksi) {
            $transaksi->update(['status_transaksi' => 'Expired']);

            $transaksiPembelianController = new TransaksiPembelianController();
            $transaksiPembelianController->processTransactionCompletion($transaksi->id_pembelian);

            if ($transaksi->keranjang && $transaksi->keranjang->detailKeranjang) {
                foreach ($transaksi->keranjang->detailKeranjang as $detailKeranjang) {
                    $barang = $detailKeranjang->itemKeranjang->barang;
                    if ($barang) { // Pastikan barang ditemukan
                        $barang->update(['status_barang' => 'For Donation']);
                    }
                }
            }
        }
    }

    public function dashboard()
    {
        $this->ensureGudang();
        $totalTransactions = TransaksiPenitipan::whereDate('tanggal_penitipan', Carbon::today())->count();
        $totalItems = Barang::whereHas('transaksiPenitipan')->count();
        return view('gudang.dashboard', compact('totalTransactions', 'totalItems'));
    }

    public function create()
    {
        $this->ensureGudang();
        $penitips = Penitip::all();
        $qcs = Pegawai::whereHas('role', function($query) {
            $query->where('nama_role', 'gudang');
        })->get();
        $hunters = Pegawai::where('id_role', 6)->get(); // Perbaikan: Gunakan id_role untuk hunter
        $kategoris = Kategori::all();

        return view('gudang.add_transaction', compact('penitips', 'qcs', 'hunters', 'kategoris'));
    }

    public function storeTransaction(Request $request)
    {
        $this->ensureGudang();
        try {
            $validated = $request->validate([
                'id_penitip' => 'required|exists:penitip,id_penitip',
                'id_qc' => 'required|exists:pegawai,id_pegawai',
                'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
                'tanggal_masuk' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.id_kategori' => 'required|exists:kategori,id_kategori',
                'items.*.nama_barang' => 'required|string|max:255',
                'items.*.harga_barang' => 'required|numeric|min:1',
                'items.*.berat_barang' => 'required|numeric|min:0.01',
                'items.*.deskripsi_barang' => 'required|string|max:255',
                'items.*.status_garansi' => 'required|in:berlaku,tidak',
                'items.*.tanggal_garansi' => 'required_if:items.*.status_garansi,berlaku|date|nullable',
                'items.*.images' => 'required|array|min:2',
                'items.*.images.*' => 'file|mimes:jpeg,png,jpg|max:2048',
            ]);

            Log::info('Validasi berhasil', $validated);

            $transaksi = TransaksiPenitipan::create([
                'id_qc' => $request->id_qc,
                'id_hunter' => $request->id_hunter ?: null,
                'id_penitip' => $request->id_penitip,
                'tanggal_penitipan' => $request->tanggal_masuk,
            ]);

            Log::info('Transaksi penitipan disimpan', ['id' => $transaksi->id_transaksi_penitipan]);

            foreach ($request->items as $index => $item) {
                $firstLetter = strtoupper(substr($item['nama_barang'], 0, 1));
                $kodeBarang = $firstLetter . str_pad($transaksi->id_transaksi_penitipan, 4, '0', STR_PAD_LEFT) . str_pad($index + 1, 2, '0', STR_PAD_LEFT);

                $statusGaransi = $item['status_garansi'] === 'berlaku' ? 'garansi' : 'tidak garansi';
                $barang = Barang::create([
                    'id_kategori' => $item['id_kategori'],
                    'id_transaksi_penitipan' => $transaksi->id_transaksi_penitipan,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $item['nama_barang'],
                    'harga_barang' => $item['harga_barang'],
                    'berat_barang' => $item['berat_barang'],
                    'deskripsi_barang' => $item['deskripsi_barang'],
                    'status_garansi' => $statusGaransi,
                    'status_barang' => 'tersedia',
                    'tanggal_garansi' => $item['status_garansi'] === 'berlaku' ? $item['tanggal_garansi'] : null,
                    'tanggal_berakhir' => Carbon::parse($request->tanggal_masuk)->addDays(30),
                    'perpanjangan' => 0,
                ]);

                Log::info('Barang disimpan', ['id' => $barang->id_barang]);

                if ($request->hasFile("items.{$index}.images")) {
                    $images = $request->file("items.{$index}.images");
                    if (is_array($images)) {
                        Log::info("Jumlah gambar terdeteksi untuk item $index:", ['count' => count($images)]);
                        foreach ($images as $imageIndex => $image) {
                            if ($image && $image->isValid()) {
                                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                                $fileName = strtolower(str_replace(' ', '_', $originalName)) . "_" . $imageIndex . "_" . time() . '.' . $image->getClientOriginalExtension();
                                $fullPath = storage_path('app/public/gambar/' . $fileName);

                                $directory = storage_path('app/public/gambar');
                                if (!file_exists($directory)) {
                                    if (!mkdir($directory, 0755, true)) {
                                        Log::error('Gagal membuat folder', ['directory' => $directory]);
                                        throw new \Exception("Gagal membuat folder {$directory}.");
                                    }
                                    Log::info('Folder gambar dibuat', ['directory' => $directory]);
                                }

                                if (!is_writable($directory)) {
                                    Log::error('Folder tidak bisa ditulis', ['directory' => $directory]);
                                    throw new \Exception("Folder {$directory} tidak bisa ditulis.");
                                }

                                $path = $image->storeAs('gambar', $fileName, 'public');

                                Log::info('Menyimpan gambar', [
                                    'fileName' => $fileName,
                                    'fullPath' => $fullPath,
                                    'path' => $path,
                                    'disk_root' => Storage::disk('public')->path(''),
                                    'file_size' => $image->getSize(),
                                    'file_mime' => $image->getMimeType(),
                                ]);

                                if ($path && file_exists($fullPath)) {
                                    Gambar::create([
                                        'id_barang' => $barang->id_barang,
                                        'gambar_barang' => $fileName,
                                    ]);
                                    Log::info('Gambar berhasil disimpan', [
                                        'id_barang' => $barang->id_barang,
                                        'gambar_barang' => $fileName,
                                        'path' => $path,
                                    ]);
                                } else {
                                    Log::error('Gagal menyimpan gambar ke storage', [
                                        'file' => $fileName,
                                        'fullPath' => $fullPath,
                                        'path' => $path,
                                        'exists' => file_exists($fullPath),
                                        'error' => $image->getErrorMessage() ?: 'Tidak ada error spesifik',
                                        'upload_error_code' => $image->getError(),
                                        'php_upload_limits' => [
                                            'upload_max_filesize' => ini_get('upload_max_filesize'),
                                            'post_max_size' => ini_get('post_max_size'),
                                        ],
                                    ]);
                                    throw new \Exception("Gagal menyimpan gambar {$fileName} ke storage.");
                                }
                            } else {
                                Log::error('Gambar tidak valid: ' . ($image ? $image->getClientOriginalName() : 'null'));
                                throw new \Exception("Gambar tidak valid: " . ($image ? $image->getClientOriginalName() : 'null'));
                            }
                        }
                    } else {
                        Log::warning("Gambar untuk item $index bukan array", ['images' => $images]);
                        throw new \Exception("Gambar untuk item $index bukan array.");
                    }
                } else {
                    Log::warning('Tidak ada gambar yang diunggah untuk item ' . $index);
                    throw new \Exception("Tidak ada gambar yang diunggah untuk item $index.");
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'transaction_id' => $transaksi->id_transaksi_penitipan,
                'redirect' => route('gudang.transaction.list')
            ]);

        } catch (\Exception $e) {
            Log::error('Error menyimpan transaksi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->ensureGudang();
        return $this->storeTransaction($request);
    }

    public function searchTransaction(Request $request)
    {
        $this->ensureGudang();
        $query = TransaksiPenitipan::with(['penitip', 'barang']);
        if ($request->id_transaksi) {
            $query->where('id_transaksi_penitipan', $request->id_transaksi);
        }
        if ($request->nama_barang) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->nama_barang}%");
            });
        }
        $transactions = $query->get();
        return view('gudang.search_transaction', compact('transactions'));
    }

    public function editTransaction($id)
    {
        $this->ensureGudang();
        $transaction = TransaksiPenitipan::with(['barang.gambar'])->findOrFail($id);
        $penitips = Penitip::all();
        $qcs = Pegawai::whereHas('role', function ($query) {
            $query->where('nama_role', 'gudang');
        })->get();
        $hunters = $qcs;
        $kategoris = Kategori::all();

        foreach ($transaction->barang as $item) {
            $item->tanggal_garansi_formatted = $item->tanggal_garansi ? Carbon::parse($item->tanggal_garansi)->format('Y-m-d') : '';
            $item->tanggal_berakhir_formatted = $item->tanggal_berakhir ? Carbon::parse($item->tanggal_berakhir)->format('Y-m-d') : '';
        }

        $transaction->tanggal_penitipan_formatted = $transaction->tanggal_penitipan ? Carbon::parse($transaction->tanggal_penitipan)->format('Y-m-d') : '';

        return view('gudang.edit_transaction', compact('transaction', 'penitips', 'qcs', 'hunters', 'kategoris'));
    }

    public function updateTransaction(Request $request, $id)
{
    $this->ensureGudang();
    try {
        $transaction = TransaksiPenitipan::with(['barang', 'barang.gambar'])->findOrFail($id);

        $validated = $request->validate([
            'id_qc' => 'required|exists:pegawai,id_pegawai',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.id_kategori' => 'required|exists:kategori,id_kategori',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_barang' => 'required|numeric|min:1',
            'items.*.berat_barang' => 'required|numeric|min:0.01',
            'items.*.deskripsi_barang' => 'required|string|max:255',
            'items.*.status_garansi' => 'required|in:berlaku,tidak',
            'items.*.tanggal_garansi' => 'required_if:items.*.status_garansi,berlaku|date|nullable',
            'items.*.tanggal_berakhir' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request, $transaction) {
                    $penitipanDate = Carbon::parse($request->tanggal_penitipan);
                    $endDate = Carbon::parse($value);
                    if ($endDate < $penitipanDate) {
                        $fail("{$attribute} tidak boleh kurang dari tanggal penitipan ({$penitipanDate->format('Y-m-d')}).");
                    }
                },
            ],
            'items.*.status_barang' => 'required|in:tersedia,selesai,sedang dikirim,menunggu pengambilan,diproses,dibatalkan,menunggu pembayaran,didonasikan,barang untuk donasi,In Delivery',
            'items.*.images' => 'nullable|array',
            'items.*.images.*' => 'file|mimes:jpeg,png,jpg|max:2048',
            'items.*.delete_images' => 'nullable|array',
        ]);

        Log::info('Validasi update berhasil', $validated);

        $transaction->update([
            'id_qc' => $validated['id_qc'],
            'id_hunter' => $validated['id_hunter'] ?? null,
            'id_penitip' => $validated['id_penitip'],
            'tanggal_penitipan' => $validated['tanggal_penitipan'],
        ]);

        Log::info('Transaksi diperbarui', ['id' => $transaction->id_transaksi_penitipan]);

        foreach ($validated['items'] as $index => $item) {
            $barang = Barang::findOrFail($item['id_barang']);

            $statusGaransi = $item['status_garansi'] === 'berlaku' ? 'garansi' : 'tidak garansi';
            $barang->update([
                'id_kategori' => $item['id_kategori'],
                'nama_barang' => $item['nama_barang'],
                'harga_barang' => $item['harga_barang'],
                'berat_barang' => $item['berat_barang'],
                'deskripsi_barang' => $item['deskripsi_barang'],
                'status_garansi' => $statusGaransi,
                'tanggal_garansi' => $item['status_garansi'] === 'berlaku' ? $item['tanggal_garansi'] : null,
                'tanggal_berakhir' => $item['tanggal_berakhir'],
                'status_barang' => $item['status_barang'],
            ]);

            Log::info('Barang diperbarui', ['id' => $barang->id_barang]);

            if ($request->has("items.{$index}.delete_images")) {
                $imagesToDelete = $request->input("items.{$index}.delete_images");
                foreach ($imagesToDelete as $imageId) {
                    if ($imageId) {
                        $gambar = Gambar::where('id_gambar', $imageId)->where('id_barang', $barang->id_barang)->first();
                        if ($gambar) {
                            $filePath = 'gambar/' . $gambar->gambar_barang;
                            if (Storage::disk('public')->exists($filePath)) {
                                Storage::disk('public')->delete($filePath);
                                Log::info('Gambar dihapus dari storage', ['file' => $filePath]);
                            }
                            $gambar->delete();
                            Log::info('Gambar dihapus dari database', ['id_gambar' => $imageId]);
                        } else {
                            Log::warning('Gambar tidak ditemukan untuk dihapus', ['id_gambar' => $imageId, 'id_barang' => $barang->id_barang]);
                        }
                    } else {
                        Log::warning('ID gambar null, lewati penghapusan', ['index' => $index]);
                    }
                }
            }

            if ($request->hasFile("items.{$index}.images")) {
                $images = $request->file("items.{$index}.images");
                Log::info("Jumlah gambar yang diupload untuk item $index:", ['count' => count($images)]);

                foreach ($images as $imageIndex => $image) {
                    if ($image && $image->isValid()) {
                        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                        $fileName = strtolower(str_replace(' ', '_', $originalName)) . "_" . $imageIndex . "_" . time() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('gambar', $fileName, 'public');

                        if ($path && Storage::disk('public')->exists('gambar/' . $fileName)) {
                            Gambar::create([
                                'id_barang' => $barang->id_barang,
                                'gambar_barang' => $fileName,
                            ]);
                            Log::info('Gambar baru berhasil disimpan', [
                                'id_barang' => $barang->id_barang,
                                'gambar_barang' => $fileName,
                                'path' => $path,
                            ]);
                        } else {
                            Log::error('Gagal menyimpan gambar baru', ['file' => $fileName, 'path' => $path]);
                            throw new \Exception("Gagal menyimpan gambar baru {$fileName}.");
                        }
                    } else {
                        Log::warning('File gambar tidak valid', ['index' => $index, 'imageIndex' => $imageIndex]);
                    }
                }
            } else {
                Log::info("Tidak ada gambar baru untuk item $index");
            }
        }

        return redirect()->route('gudang.transaction.list')->with('success', 'Transaksi berhasil diperbarui.');
    } catch (\Exception $e) {
        Log::error('Error memperbarui transaksi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())->withInput();
    }
}
    public function myProduct()
    {
        $this->ensureGudang();
        $penitipId = session('user.id');
        $products = Barang::whereHas('transaksiPenitipan', function ($query) use ($penitipId) {
            $query->where('id_penitip', $penitipId);
        })->with(['gambar', 'kategori'])->get();

        return view('penitip.my-product', compact('products'));
    }

    public function index()
    {
        $this->ensureGudang();
        $transaksiPenitipan = TransaksiPenitipan::all();
        return response()->json($transaksiPenitipan);
    }

    public function show($id)
    {
        $this->ensureGudang();
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }
        return response()->json($transaksiPenitipan);
    }

    public function update(Request $request, $id)
    {
        $this->ensureGudang();
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }

        $request->validate([
            'id_qc' => 'nullable|exists:pegawai,id_pegawai',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'id_penitip' => 'nullable|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'nullable|date',
        ]);

        $transaksiPenitipan->update([
            'id_qc' => $request->id_qc ?? $transaksiPenitipan->id_qc,
            'id_hunter' => $request->id_hunter ?? $transaksiPenitipan->id_hunter,
            'id_penitip' => $request->id_penitip ?? $transaksiPenitipan->id_penitip,
            'tanggal_penitipan' => $request->tanggal_penitipan ?? $transaksiPenitipan->tanggal_penitipan,
        ]);

        return response()->json($transaksiPenitipan);
    }

    public function destroy($id)
    {
        $this->ensureGudang();
        $transaksiPenitipan = TransaksiPenitipan::find($id);
        if (!$transaksiPenitipan) {
            return response()->json(['message' => 'Transaksi penitipan not found'], 404);
        }

        $transaksiPenitipan->delete();
        return response()->json(['message' => 'Transaksi penitipan deleted successfully']);
    }

    public function transactionList(Request $request)
    {
        $this->ensureGudang();
        $query = TransaksiPenitipan::with(['penitip', 'barang.gambar', 'qc', 'hunter']);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('id_transaksi_penitipan', 'like', "%{$keyword}%")
                  ->orWhere('tanggal_penitipan', 'like', "%{$keyword}%")
                  ->orWhereHas('penitip', function ($q) use ($keyword) {
                      $q->where('nama_penitip', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('qc', function ($q) use ($keyword) {
                      $q->where('nama_pegawai', 'like', "%{$keyword}%")
                        ->orWhere('id_pegawai', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('hunter', function ($q) use ($keyword) {
                      $q->where('nama_pegawai', 'like', "%{$keyword}%")
                        ->orWhere('id_pegawai', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('barang', function ($q) use ($keyword) {
                      $q->where('kode_barang', 'like', "%{$keyword}%")
                        ->orWhere('nama_barang', 'like', "%{$keyword}%")
                        ->orWhere('harga_barang', 'like', "%{$keyword}%")
                        ->orWhere('berat_barang', 'like', "%{$keyword}%")
                        ->orWhere('deskripsi_barang', 'like', "%{$keyword}%")
                        ->orWhere('status_barang', 'like', "%{$keyword}%")
                        ->orWhere('status_garansi', 'like', "%{$keyword}%")
                        ->orWhere('tanggal_garansi', 'like', "%{$keyword}%")
                        ->orWhere('tanggal_berakhir', 'like', "%{$keyword}%")
                        ->orWhere('perpanjangan', 'like', "%{$keyword}%")
                        ->orWhereHas('kategori', function ($q) use ($keyword) {
                            $q->where('nama_kategori', 'like', "%{$keyword}%");
                        });
                  });
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $tanggal_mulai = $request->input('tanggal_mulai');
            $query->where('tanggal_penitipan', '>=', $tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $tanggal_selesai = $request->input('tanggal_selesai');
            $query->where('tanggal_penitipan', '<=', $tanggal_selesai);
        }
        
        // membuat agar transaksi ditampilkan dari yang terbaru ke yang terlama
        $query->latest(); 

        $transactions = $query->get();
        return view('gudang.transaction_list', compact('transactions'));
    }

    public function printNote($id)
    {
        $this->ensureGudang();
        try {
            $transaction = TransaksiPenitipan::with(['penitip', 'barang', 'qc', 'hunter'])->findOrFail($id);

            $year = Carbon::parse($transaction->created_at)->format('y');
            $month = Carbon::parse($transaction->created_at)->format('m');
            $nomorUrut = str_pad($transaction->id_transaksi_penitipan, 3, '0', STR_PAD_LEFT);
            $noNota = "$year.$month.$nomorUrut";

            $delivery = $transaction->id_hunter ? 'Hunter ReuseMart (' . ($transaction->hunter->nama_pegawai ?? 'N/A') . ')' : '-';

            $formattedData = [
                'no_nota' => $noNota,
                'tanggal_penitipan' => Carbon::parse($transaction->created_at)->format('d/m/Y H:i:s'),
                'masa_penitipan' => Carbon::parse($transaction->barang->max('tanggal_berakhir'))->format('d/m/Y'),
                'penitip_nama' => ($transaction->penitip->id_penitip ?? 'N/A') . ' / ' . ($transaction->penitip->nama_penitip ?? 'N/A'),
                'penitip_alamat' => $transaction->penitip->alamat ?? 'N/A',
                'delivery' => $delivery,
                'qc_kode' => 'P' . str_pad($transaction->id_qc, 2, '0', STR_PAD_LEFT),
                'qc_nama' => $transaction->qc->nama_pegawai ?? 'N/A',
                'barang_list' => $transaction->barang->map(function ($item) {
                    $garansi = $item->status_garansi == 'garansi' 
                        ? 'Garansi ON ' . Carbon::parse($item->tanggal_garansi)->format('M Y')
                        : '';
                    return [
                        'nama' => $item->nama_barang,
                        'harga' => number_format($item->harga_barang, 0, ',', '.'),
                        'garansi' => $garansi,
                        'berat' => $item->berat_barang . ' kg',
                    ];
                })->all(),
            ];

            $pdf = PDF::loadView('gudang.print_note', compact('transaction', 'formattedData'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('nota_penitipan_' . $transaction->id_transaksi_penitipan . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error mencetak nota', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mencetak nota: ' . $e->getMessage());
        }
    }

    public function itemList(Request $request)
    {
        $this->ensureGudang();
        $query = Barang::with(['transaksiPenitipan.penitip', 'kategori', 'gambar']);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_barang', 'like', "%{$keyword}%")
                  ->orWhere('nama_barang', 'like', "%{$keyword}%")
                  ->orWhere('status_barang', 'like', "%{$keyword}%")
                  ->orWhere('harga_barang', 'like', "%{$keyword}%")
                  ->orWhere('berat_barang', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi_barang', 'like', "%{$keyword}%")
                  ->orWhere('status_garansi', 'like', "%{$keyword}%")
                  ->orWhere('perpanjangan', 'like', "%{$keyword}%")
                  ->orWhere('tanggal_garansi', 'like', "%{$keyword}%")
                  ->orWhere('tanggal_berakhir', 'like', "%{$keyword}%")
                  ->orWhereHas('kategori', function ($q) use ($keyword) {
                      $q->where('nama_kategori', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('transaksiPenitipan', function ($q) use ($keyword) {
                      $q->where('tanggal_penitipan', 'like', "%{$keyword}%")
                        ->orWhere('id_qc', 'like', "%{$keyword}%")
                        ->orWhere('id_hunter', 'like', "%{$keyword}%")
                        ->orWhereHas('penitip', function ($q) use ($keyword) {
                            $q->where('nama_penitip', 'like', "%{$keyword}%");
                        });
                  });
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $tanggal_mulai = $request->input('tanggal_mulai');
            $query->whereHas('transaksiPenitipan', function ($q) use ($tanggal_mulai) {
                $q->where('tanggal_penitipan', '>=', $tanggal_mulai);
            });
        }

        if ($request->filled('tanggal_selesai')) {
            $tanggal_selesai = $request->input('tanggal_selesai');
            $query->where('tanggal_berakhir', '<=', $tanggal_selesai);
        }

        $barangs = $query->get();
        return view('gudang.item_list', compact('barangs'));
    }

    public function itemDetail($id)
    {
        $this->ensureGudang();
        $barang = Barang::with(['transaksiPenitipan.penitip', 'kategori', 'gambar'])->findOrFail($id);
        return view('gudang.item_detail', compact('barang'));
    }
    
    public function pengirimanDanPengambilanList()
    {
        $this->ensureGudang();
        $this->cekTransaksiHangus();
        $transaksi = TransaksiPembelian::with([
            'pembeli',
            'detailKeranjangs.itemKeranjang.barang',
            'detailKeranjangs.itemKeranjang.barang.gambar',
            'detailKeranjangs.itemKeranjang.barang.transaksiPenitipan.penitip',

        ])
        ->whereIn('status_transaksi', ['Ready for Pickup', 'Preparing', 'In Delivery', 'Disiapkan']) 
        ->orderBy('tanggal_pembelian', 'asc')
        ->get();

        $barangReadyForPickup = Barang::with([
            'gambar',
            'transaksiPenitipan.penitip'
        ])
        ->where('status_barang', 'Ready for Pickup')
        ->orderBy('tanggal_konfirmasi_pengambilan', 'asc') 
        ->get();

        return view('gudang.transaksi_pengiriman', compact('transaksi','barangReadyForPickup'));
    }

    public function perbaruiStatusOtomatis()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        TransaksiPembelian::where('status_transaksi', 'Preparing')
            ->where('metode_pengiriman', 'kurir')
            ->whereDate('tanggal_pengiriman', $today)
            ->whereNotNull('id_kurir') // pastikan kurir sudah ditugaskan
            ->update(['status_transaksi' => 'In Delivery']);

        TransaksiPembelian::where('status_transaksi', 'Ready for Pickup')
            ->whereDate('tanggal_pengambilan', '<', Carbon::now()->subDays(2)->toDateString())
            ->update(['status_transaksi' => 'Donated']);

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function showDetail($id)
    {
        $this->ensureGudang();
        $this->cekTransaksiHangus();

        $transaksi = TransaksiPembelian::with([
            'pembeli',
            'detailKeranjangs.itemKeranjang.barang.gambar'
        ])->findOrFail($id);

        return view('gudang.transaksi_detail_pengiriman', compact('transaksi'));
    }

    public function jadwalkan($id)
    {
        $this->ensureGudang();
        $this->cekTransaksiHangus();

        $transaksi = TransaksiPembelian::with([
            'pembeli',
            'detailKeranjangs.itemKeranjang.barang.gambar'
        ])->findOrFail($id);

        $kurirs = Pegawai::whereHas('role', function ($query) {
            $query->where('nama_role', 'kurir');
        })->get();

        return view('gudang.jadwal_pengiriman', compact('transaksi', 'kurirs'));
    }

    public function jadwalkanPengiriman(Request $request, $id)
    {
        $this->ensureGudang();
        $this->cekTransaksiHangus();
        
        $transaksi = TransaksiPembelian::findOrFail($id);

        $isSelfPickup = $transaksi->metode_pengiriman === 'Self Pick-Up';
        $isAmbil = $transaksi->metode_pengiriman === 'ambil';
        
        // Validasi input
        $rules = ['tanggal_pengiriman' => 'required|date|after_or_equal:today'];
        if ($transaksi->metode_pengiriman === 'kurir' || $transaksi->metode_pengiriman === 'Courier') {
            $rules['id_kurir'] = 'required|exists:pegawai,id_pegawai';
        }
        $request->validate($rules);

        // Cek apakah transaksi dilakukan setelah jam 4 sore dan apakah tanggal pengiriman adalah hari ini
        $jamPembelian = Carbon::parse($transaksi->tanggal_pembelian)->format('H');
        $tanggalPembelian = Carbon::parse($transaksi->tanggal_pembelian)->toDateString();
        $tanggalRequest = Carbon::parse($request->tanggal_pengiriman)->toDateString();

        if ($jamPembelian >= 16 && $tanggalRequest == $tanggalPembelian) {
            return back()->with('error', 'Pembelian setelah pukul 16.00 tidak bisa dijadwalkan di hari yang sama.');
        }

        // Logika Update berdasarkan status transaksi
        if ($transaksi->status_transaksi === 'Preparing' || $transaksi->status_transaksi === 'Disiapkan') {
            if ($isSelfPickup || $isAmbil) {
                // Self Pick-Up
                $transaksi->update([
                    'tanggal_pengambilan' => $request->tanggal_pengiriman,
                    'status_transaksi' => 'Ready for Pickup'
                ]);
            } else {
                // Courier
                $transaksi->update([
                    'tanggal_pengiriman' => $request->tanggal_pengiriman,
                    'id_kurir' => $request->id_kurir,
                    'status_transaksi' => 'In Delivery'
                ]);

                KelolaTransaksi::updateOrCreate(
                    [
                        'id_pembelian' => $transaksi->id_pembelian,
                    ],
                    [
                        'id_pegawai'   => $request->id_kurir,
                    ]
                );

                $transaksiPembelianController = new TransaksiPembelianController();
                $transaksiPembelianController->processTransactionCompletion($id);
            }
        }

        // Kirim notifikasi ke pembeli, penitip, dan kurir
        $this->kirimNotifikasiJadwal($transaksi);

        // Redirect dengan pesan sukses
        return back()->with('success', 'Pengiriman berhasil dijadwalkan.');
    }

    private function kirimNotifikasiJadwal(TransaksiPembelian $transaksi)
    {
        $pembeli = $transaksi->pembeli;
        $kurir = Pegawai::find($transaksi->id_kurir);

        foreach ([$pembeli, $kurir] as $penerima) {
            if ($penerima && $penerima->fcm_token) {
                // misal pakai helper atau service untuk kirim FCM
                KirimNotifikasi::send(
                    $penerima->fcm_token,
                    'Pengiriman Dijadwalkan',
                    'Pesanan #'.$transaksi->no_nota.' akan dikirim pada '.$transaksi->tanggal_pengiriman->format('d M Y H:i')
                );
            }
        }

        // kirim ke penitip (dari barang dalam transaksi)
        foreach ($transaksi->detailKeranjangs as $detail) {
            $barang = $detail->itemKeranjang->barang ?? null;
            $penitip = $barang->transaksiPenitipan->penitip ?? null;
            if ($penitip && $penitip->fcm_token) {
                KirimNotifikasi::send(
                    $penitip->fcm_token,
                    'Barang Anda Akan Dikirim',
                    'Barang "'.$barang->nama_barang.'" akan dikirim pada '.$transaksi->tanggal_pengiriman->format('d M Y H:i')
                );
            }
        }
    }

    public function confirmPickup($id)
    {
        $this->ensureGudang();
        $this->cekTransaksiHangus();

        $transaksi = TransaksiPembelian::findOrFail($id);

        if ($transaksi->status_transaksi != 'Ready for Pickup') {
            return back()->with('error', 'Status transaksi tidak sesuai untuk konfirmasi pengambilan.');
        }

        $transaksi->update([
            'status_transaksi' => 'Done',
            'tanggal_ambil' => now(),
        ]);

        $transaksiPembelianController = new TransaksiPembelianController;
        $transaksiPembelianController->processTransactionCompletion($id); 

        $this->kirimNotifikasiPengambilan($transaksi);

        return back()->with('success', 'Barang telah berhasil dikonfirmasi dan status diperbarui.');
    }

    private function kirimNotifikasiPengambilan(TransaksiPembelian $transaksi)
    {
        $pembeli = $transaksi->pembeli;
        $kurir = Pegawai::find($transaksi->id_kurir);

        foreach ([$pembeli, $kurir] as $penerima) {
            if ($penerima && $penerima->fcm_token) {
                KirimNotifikasi::send(
                    $penerima->fcm_token,
                    'Pengambilan Dikukuhkan',
                    'Pesanan #'.$transaksi->no_nota.' telah dikonfirmasi untuk pengambilan pada '.$transaksi->tanggal_ambil->format('d M Y H:i')
                );
            }
        }

        foreach ($transaksi->detailKeranjangs as $detail) {
            $barang = $detail->itemKeranjang->barang ?? null;
            $penitip = $barang->transaksiPenitipan->penitip ?? null;
            if ($penitip && $penitip->fcm_token) {
                KirimNotifikasi::send(
                    $penitip->fcm_token,
                    'Barang Anda Telah Dikonfirmasi',
                    'Barang "'.$barang->nama_barang.'" telah dikonfirmasi dan siap diambil pada '.$transaksi->tanggal_ambil->format('d M Y H:i')
                );
            }
        }
    }

    public function printInvoice($id)
    {
        $transaksi = TransaksiPembelian::with([
            'pembeli',
            'detailKeranjangs.itemKeranjang.barang',
            'kurir' 
        ])->findOrFail($id);

        $kurirName = 'N/A'; 
        if ($transaksi->id_kurir && $transaksi->kurir) {
            $kurirName = $transaksi->kurir->nama_pegawai;
        }

        $tanggal = \Carbon\Carbon::parse($transaksi->waktu_pembayaran);
        $noNota = $tanggal->format('y.m') . '.' . $transaksi->id_pembelian;
        $firstDetail = $transaksi->detailKeranjangs->first();
        $pembeli = $firstDetail->itemKeranjang->pembeli;

        $subtotal = 0;
        foreach ($transaksi->detailKeranjangs as $detail) {
            $barang = $detail->itemKeranjang->barang;
            $subtotal += $barang->harga_barang;
        }

        $ongkir = $subtotal >= 1500000 ? 0 : 100000; 

        $total = $subtotal + $ongkir;

        $invoiceData = [
            'no_nota' => $noNota, 
            'tanggal_pesan' => \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->format('d F Y, H:i'),
            'tanggal_kirim' => \Carbon\Carbon::parse($transaksi->tanggal_pengiriman)->format('d F Y'), 
            'pembeli' => $pembeli->nama_pembeli,
            'kurir' => $kurirName, 
            'items' => $transaksi->detailKeranjangs->map(function ($detail) {
                $barang = $detail->itemKeranjang->barang;
                return [
                    'nama_barang' => $barang->nama_barang,
                    'harga' => number_format($barang->harga_barang, 0, ',', '.'),
                    'berat' => $barang->berat_barang,
                    'status' => $barang->status_barang,
                ];
            }),
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'total' => $total,
            'transaksi' => $transaksi, 
        ];

        // Generate PDF
        $pdf = PDF::loadView('gudang.invoice', $invoiceData);
        return $pdf->download('invoice-' . $transaksi->id_pembelian . '.pdf'); 
    }

    public function printInvoicePickup($id)
    {
        $transaksi = TransaksiPembelian::with([
            'pembeli',
            'detailKeranjangs.itemKeranjang.barang',
        ])->findOrFail($id);

        $tanggal = \Carbon\Carbon::parse($transaksi->waktu_pembayaran);
        $noNota = $tanggal->format('y.m') . '.' . $transaksi->id_pembelian;
        $firstDetail = $transaksi->detailKeranjangs->first();
        $pembeli = $firstDetail->itemKeranjang->pembeli;

        $total = 0;
        foreach ($transaksi->detailKeranjangs as $detail) {
            $barang = $detail->itemKeranjang->barang;
            $total += $barang->harga_barang;
        }

        $invoiceData = [
            'no_nota' => $noNota, 
            'tanggal_pesan' => \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->format('d F Y, H:i'),
            'tanggal_kirim' => \Carbon\Carbon::parse($transaksi->tanggal_pengiriman)->format('d F Y'), 
            'pembeli' => $pembeli->nama_pembeli,
            'items' => $transaksi->detailKeranjangs->map(function ($detail) {
                $barang = $detail->itemKeranjang->barang;
                return [
                    'nama_barang' => $barang->nama_barang,
                    'harga' => number_format($barang->harga_barang, 0, ',', '.'),
                    'berat' => $barang->berat_barang,
                    'status' => $barang->status_barang,
                ];
            }),
            'total' => $total,
            'transaksi' => $transaksi, 
        ];
        
        // Generate PDF
        $pdf = PDF::loadView('gudang.invoicePickup', $invoiceData);
        return $pdf->download('invoice-' . $transaksi->id_pembelian . '.pdf'); 
    }

    public function transaksiPengambilan()
    {
        $barangPenitip = Barang::with(['transaksiPenitipan.penitip', 'gambar'])
            ->where('status_barang', 'Ready for Pickup')
            ->where('batas_pengambilan', '>', now())
            ->get();


        return view('gudang.transaksi_pengiriman', compact('barang'));
    }

    public function markAsReturned($id)
    {
        $barang = Barang::findOrFail($id);
        if ($barang->status_barang != 'Ready for Pickup') {
            return redirect()->back()->with('error', 'Barang tidak siap untuk diambil.');
        }

        $barang->update([
            'status_barang' => 'Returned',
            'updated_at' => now(),
        ]);

        return redirect()->route('gudang.transaksi.index')->with('success', 'Barang telah berhasil diambil dan status diperbarui.');
    }

    public function detailTransaksi($id)
    {
        $transaksi = TransaksiPembelian::with('detailKeranjangs.itemKeranjang.barang')->findOrFail($id);

        foreach ($transaksi->detailKeranjangs as $detail) {
            $barang = $detail->itemKeranjang->barang;
            
            if ($barang->batas_pengambilan && $barang->batas_pengambilan < now()) {
                $barang->update([
                    'status_barang' => 'Donated',
                    'updated_at' => now(),
                ]);
            }
        }

        return view('gudang.transaksi_detail', compact('transaksi'));
    }

    public function confirmPickupBarang(Request $request, $id) 
    {
        $this->ensureGudang(); 
        $this->cekTransaksiHangus(); 

        $barang = Barang::find($id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.'); 
        }

        if ($barang->status_barang == 'Ready for Pickup' && $barang->batas_pengambilan > Carbon::now()) { 
            $barang->status_barang = 'Returned'; 
            $barang->tanggal_konfirmasi_pengambilan = Carbon::now(); 
            $barang->save(); 

            return redirect()->back()->with('success', 'Pengambilan barang titipan oleh pemilik berhasil dikonfirmasi!'); //
        } else {
            return redirect()->back()->with('error', 'Barang tidak dalam status siap untuk diambil atau batas pengambilan sudah terlewati.'); //
        }
    }

    public function detailBarang($id) 
    {
        $this->ensureGudang(); 
        $this->cekTransaksiHangus(); 

        $barang = Barang::with([
            'kategori', 
            'gambar', 
            'transaksiPenitipan.penitip' 
        ])->find($id); 

        if (!$barang) {
            return redirect()->route('gudang.transaksi.pengiriman')->with('error', 'Barang tidak ditemukan.'); 
        }

        return view('gudang.detail_barang_titipan', compact('barang')); 
    }
}