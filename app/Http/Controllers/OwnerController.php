<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\RequestDonasi;
use App\Models\Donasi;
use App\Models\Barang;
use App\Models\Organisasi;
use App\Models\Penitip;
use App\Models\Kategori;
use App\Models\Keranjang;
use App\Models\TransaksiPembelian;
use App\Models\ItemKeranjang;
use App\Models\DetailKeranjang;
use App\Models\TransaksiPenitipan;
use Carbon\Carbon;
// use PDF;
// use App\Charts\ChartJSNodeCanvas;
use ChartJs\Chart;

class OwnerController extends Controller
{
    private function ensureOwner()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 1) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function reports()
    {
        $this->ensureOwner();
        return view('owner.reports');
    }

    public function dashboard()
    {
        $this->ensureOwner();

        $totalRequests = RequestDonasi::where('status_request', 'belum di proses')
                                    ->orwhere('status_request', 'Pending')
                                    ->count();
        $totalDonations = Donasi::count();

        return view('owner.dashboard', compact('totalRequests', 'totalDonations'));
    }

    public function donationRequests()
    {
        $this->ensureOwner();

        $requests = RequestDonasi::where('status_request', 'belum di proses')
            ->orwhere('status_request', 'Pending')
            ->with(['organisasi', 'pegawai'])
            ->get();

        return view('owner.donation_requests', compact('requests'));
    }

    public function allocateItems(Request $request)
    {
        $this->ensureOwner();

        $organisasi = Organisasi::all();
        $items = Barang::whereIn('status_barang', ['barang untuk donasi', 'For Donation'])->get();
        $requestId = $request->query('id_request');

        $selectedOrganisasi = null;
        $requests = collect([]);

        if ($requestId) {
            $requestDonasi = RequestDonasi::where('id_request', $requestId)
                ->where('status_request', 'belum di proses')
                ->orwhere('status_request', 'Pending') // baru tambah
                ->whereNotNull('id_organisasi')
                ->with('organisasi')
                ->first();

            if ($requestDonasi) {
                $selectedOrganisasi = $requestDonasi->id_organisasi;
                $requests = RequestDonasi::where('id_organisasi', $selectedOrganisasi)
                    ->where('status_request', 'belum di proses')
                    ->orwhere('status_request', 'Pending') // baru tambah
                    ->whereNotNull('id_organisasi')
                    ->with('organisasi')
                    ->get();
            }
        }

        return view('owner.allocate_items', compact('organisasi', 'items', 'requestId', 'requests', 'selectedOrganisasi'));
    }

    public function getRequestsByOrganisasi(Request $request)
    {
        $this->ensureOwner();

        $requests = RequestDonasi::where('id_organisasi', $request->id_organisasi)
            ->where(function ($query) {
                $query->where('status_request', 'belum di proses')
                      ->orWhere('status_request', 'Pending');
            })
            ->with('organisasi')
            ->get()
            ->map(function ($req) {
                return [
                    'id_request' => $req->id_request,
                    'deskripsi_request' => $req->request . ' (' . ($req->organisasi->nama_organisasi ?? 'N/A') . ')'
                ];
            });

        return response()->json($requests);
    }

    public function updateDonation(Request $request)
    {
        $this->ensureOwner();

        $donations = Donasi::with('barang')->get();
        $selectedDonasi = $request->has('id_donasi') ? Donasi::with('barang')->find($request->id_donasi) : null;

        return view('owner.update_donation', compact('donations', 'selectedDonasi'));
    }

    public function rewards()
    {
        $this->ensureOwner();
        return view('owner.rewards');
    }

    public function deleteRequest($id)
    {
        $this->ensureOwner();

        $request = RequestDonasi::find($id);
        if ($request) {
            $request->delete();
            return response()->json(['message' => 'Request berhasil dihapus.']);
        }

        return response()->json(['message' => 'Request tidak ditemukan.'], 404);
    }

    public function storeAllocation(Request $request)
    {
        $this->ensureOwner();

        $request->validate([
            'id_request' => 'required|exists:request_donasi,id_request',
            'id_barang' => 'required|exists:barang,id_barang',
            'nama_penerima' => 'required|string',
            'tanggal_donasi' => 'required|date',
            'id_organisasi' => 'required|exists:organisasi,id_organisasi',
        ]);

        $barang = Barang::where('id_barang', $request->id_barang)
            ->where('status_barang', 'barang untuk donasi')
            ->orWhere('status_barang', 'For Donation')
            ->with('transaksiPenitipan.penitip')
            ->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak tersedia untuk donasi.');
        }

        \DB::beginTransaction();
        try {
            $donasi = Donasi::create([
                'id_request' => $request->id_request,
                'id_barang' => $request->id_barang,
                'nama_penerima' => $request->nama_penerima,
                'tanggal_donasi' => $request->tanggal_donasi,
                'status' => 'sudah di donasikan',
            ]);

            $barang->update(['status_barang' => 'didonasikan']);

            $poin_penitip = 0;
            if ($barang->transaksiPenitipan && $barang->transaksiPenitipan->penitip) {
                $hargaBarang = $barang->harga_barang ?? 0;
                $poin_penitip = floor($hargaBarang / 10000);
                $penitip = $barang->transaksiPenitipan->penitip;
                \Log::info('Calculating poin for penitip', [
                    'id_barang' => $barang->id_barang,
                    'id_penitip' => $penitip->id_penitip,
                    'harga_barang' => $hargaBarang,
                    'poin_penitip' => $poin_penitip,
                ]);
                $penitip->increment('poin_penitip', $poin_penitip);
                $penitip->save();
                \Log::info('Poin updated for penitip', [
                    'id_penitip' => $penitip->id_penitip,
                    'new_poin' => $penitip->poin_penitip,
                ]);
            } else {
                \Log::warning('Failed to calculate poin: transaksiPenitipan or penitip not found', [
                    'id_barang' => $barang->id_barang,
                    'transaksiPenitipan' => $barang->transaksiPenitipan ? 'exists' : 'null',
                    'penitip' => $barang->transaksiPenitipan && $barang->transaksiPenitipan->penitip ? 'exists' : 'null',
                ]);
                throw new \Exception('Gagal menghitung poin: Data penitip tidak ditemukan.');
            }

            \DB::commit();
            return redirect()->back()->with('success', "Barang berhasil dialokasikan! Poin reward: $poin_penitip");
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to allocate item', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengalokasikan barang: ' . $e->getMessage());
        }
    }

    public function donationHistory(Request $request)
    {
        $this->ensureOwner();

        $organisasi = Organisasi::all();
        $query = Donasi::with(['requestDonasi.organisasi', 'barang']);

        if ($request->filled('id_organisasi')) {
            $query->whereHas('requestDonasi', function ($q) use ($request) {
                $q->where('id_organisasi', $request->id_organisasi);
            });
        }

        $donations = $query->get();

        return view('owner.donation_history', compact('donations', 'organisasi'));
    }

    public function updateDonasiStore(Request $request)
    {
        $this->ensureOwner();

        $request->validate([
            'id_donasi' => 'required|exists:donasi,id_donasi',
            'tanggal_donasi' => 'required|date',
            'nama_penerima' => 'required|string',
            'status_barang' => 'required|in:barang untuk donasi,didonasikan,For Donation',
        ]);

        $donasi = Donasi::with('barang')->find($request->id_donasi);
        if (!$donasi) return redirect()->back()->with('error', 'Donasi tidak ditemukan.');

        $donasi->update([
            'tanggal_donasi' => $request->tanggal_donasi,
            'nama_penerima' => $request->nama_penerima,
        ]);

        $donasi->barang->update(['status_barang' => $request->status_barang]);

        return redirect()->back()->with('success', 'Donasi berhasil diperbarui.');
    }

    public function getDonasi(Request $request)
    {
        $this->ensureOwner();

        $donasi = Donasi::with('barang')->find($request->query('id_donasi'));
        if (!$donasi) return response()->json(['error' => 'Donasi tidak ditemukan.'], 404);

        return response()->json([
            'tanggal_donasi' => $donasi->tanggal_donasi,
            'nama_penerima' => $donasi->nama_penerima,
            'barang' => ['status_barang' => $donasi->barang->status_barang],
        ]);
    }

    public function monthlySalesReport(Request $request)
    {
        $this->ensureOwner();

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $tanggalCetak = Carbon::now()->format('d F Y');

        $soldItems = Barang::whereHas('itemKeranjangs.detailKeranjangs.keranjang.transaksiPembelian', function ($query) use ($month, $year) {
            $query->where('status_transaksi', 'selesai')
                ->whereMonth('tanggal_pembelian', $month)
                ->whereYear('tanggal_pembelian', $year);
        })->with(['transaksiPenitipan' => function ($query) {
            $query->with('penitip', 'hunter');
        }, 'itemKeranjangs.detailKeranjangs.keranjang.transaksiPembelian'])->get();

        $formattedData = $soldItems->map(function ($item) use ($month, $year) {
            $transaksiPenitipan = $item->transaksiPenitipan()->first();
            if (!$transaksiPenitipan) return null;

            $transaksiPembelian = $item->itemKeranjangs
                ->flatMap(function ($itemKeranjang) {
                    return $itemKeranjang->detailKeranjangs->map(function ($detail) {
                        return $detail->keranjang->transaksiPembelian;
                    })->filter();
                })
                ->unique('id_keranjang')
                ->first();

            if (!$transaksiPembelian) return null;

            $tanggalMasuk = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->format('d/m/Y');
            $tanggalLaku = Carbon::parse($transaksiPembelian->tanggal_pembelian)->format('d/m/Y');
            $hargaJual = $item->harga_barang ?? 0;
            $daysDiff = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->diffInDays(Carbon::parse($transaksiPembelian->tanggal_pembelian));

            $komisiReUseMartBase = $hargaJual * 0.20;
            $komisiHunter = $transaksiPenitipan->hunter ? $hargaJual * 0.05 : 0;
            $komisiReUseMartFinal = $komisiReUseMartBase - $komisiHunter;
            $bonusPenitip = ($daysDiff < 7) ? ($komisiReUseMartBase * 0.10) : 0;
            $komisiReUseMartFinal -= $bonusPenitip;

            // Kondisi khusus untuk perpanjangan dengan hunter
            if ($daysDiff > 30) {
                if ($transaksiPenitipan->hunter) {
                    $komisiReUseMartFinal = $hargaJual * 0.25; // 25% untuk ReUseMart
                    $komisiHunter = $hargaJual * 0.05; // 5% untuk hunter
                } else {
                    $komisiReUseMartFinal = $hargaJual * 0.30; // 30% kalau tanpa hunter
                }
            }

            return [
                'kode_produk' => $item->kode_barang,
                'nama_produk' => $item->nama_barang,
                'harga_jual' => number_format($hargaJual, 0, ',', '.'),
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_laku' => $tanggalLaku,
                'komisi_hunter' => number_format($komisiHunter, 0, ',', '.'),
                'komisi_reuse_mart' => number_format($komisiReUseMartFinal, 0, ',', '.'),
                'bonus_penitip' => number_format($bonusPenitip, 0, ',', '.'),
            ];
        })->filter()->values();

        $totalKomisiHunter = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['komisi_hunter'] ?? '0');
        });
        $totalKomisiReUseMart = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['komisi_reuse_mart'] ?? '0');
        });
        $totalBonusPenitip = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['bonus_penitip'] ?? '0');
        });
        $totalHargaJual = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['harga_jual'] ?? '0');
        }); // Tambah total Harga Jual

        return view('owner.monthly_sales_report', compact('formattedData', 'month', 'year', 'totalKomisiHunter', 'totalKomisiReUseMart', 'totalBonusPenitip', 'tanggalCetak', 'totalHargaJual')); // Tambahkan 'totalHargaJual'
    }

        public function downloadMonthlySalesReport(Request $request)
    {
        $this->ensureOwner();

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $tanggalCetak = Carbon::now()->format('d F Y');

        $soldItems = Barang::whereHas('itemKeranjangs', function ($query) use ($month, $year) {
            $query->whereHas('keranjang.transaksiPembelian', function ($q) use ($month, $year) {
                $q->where('status_transaksi', 'selesai')
                ->whereMonth('tanggal_pembelian', $month)
                ->whereYear('tanggal_pembelian', $year);
            });
        })->with(['transaksiPenitipan' => function ($query) {
            $query->with('penitip', 'hunter');
        }])->get();

        $formattedData = $soldItems->map(function ($item) use ($month, $year) {
            $transaksiPenitipan = $item->transaksiPenitipan()->first();
            if (!$transaksiPenitipan) return null;

            $itemKeranjang = $item->itemKeranjangs->first();
            if (!$itemKeranjang) return null;

            $keranjang = $itemKeranjang->keranjang->first();
            if (!$keranjang) return null;

            $transaksiPembelian = $keranjang->transaksiPembelian ?? null;
            if (!$transaksiPembelian) return null;

            $tanggalMasuk = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->format('d/m/Y');
            $tanggalLaku = Carbon::parse($transaksiPembelian->tanggal_pembelian)->format('d/m/Y');
            $hargaJual = $item->harga_barang ?? 0;
            $daysDiff = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->diffInDays(Carbon::parse($transaksiPembelian->tanggal_pembelian));

            $komisiReUseMartBase = $hargaJual * 0.20;
            $komisiHunter = $transaksiPenitipan->hunter ? $hargaJual * 0.05 : 0;
            $komisiReUseMartFinal = $komisiReUseMartBase - $komisiHunter;
            $bonusPenitip = ($daysDiff < 7) ? ($komisiReUseMartBase * 0.10) : 0;
            $komisiReUseMartFinal -= $bonusPenitip;

            if ($daysDiff > 30) {
                if ($transaksiPenitipan->hunter) {
                    $komisiReUseMartFinal = $hargaJual * 0.25;
                    $komisiHunter = $hargaJual * 0.05;
                } else {
                    $komisiReUseMartFinal = $hargaJual * 0.30;
                }
            }

            return [
                'kode_produk' => $item->kode_barang,
                'nama_produk' => $item->nama_barang,
                'harga_jual' => number_format($hargaJual, 0, ',', '.'),
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_laku' => $tanggalLaku,
                'komisi_hunter' => number_format($komisiHunter, 0, ',', '.'),
                'komisi_reuse_mart' => number_format($komisiReUseMartFinal, 0, ',', '.'),
                'bonus_penitip' => number_format($bonusPenitip, 0, ',', '.'),
            ];
        })->filter()->values();

        $totalKomisiHunter = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['komisi_hunter'] ?? '0');
        });
        $totalKomisiReUseMart = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['komisi_reuse_mart'] ?? '0');
        });
        $totalBonusPenitip = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['bonus_penitip'] ?? '0');
        });
        $totalHargaJual = $formattedData->sum(function ($item) {
            return str_replace('.', '', $item['harga_jual'] ?? '0');
        }); // Tambah total Harga Jual

        $pdf = PDF::loadView('owner.monthly_sales_report_pdf', compact('formattedData', 'month', 'year', 'totalKomisiHunter', 'totalKomisiReUseMart', 'totalBonusPenitip', 'tanggalCetak', 'totalHargaJual')); // Tambahkan 'totalHargaJual'
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_komisi_bulanan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function warehouseStockReport(Request $request)
    {
        $this->ensureOwner();

        $date = $request->input('date', Carbon::now()->format('d/m/Y'));
        $parsedDate = Carbon::createFromFormat('d/m/Y', $date)->startOfDay();

        $items = Barang::where('status_barang', 'tersedia')
            ->with(['transaksiPenitipan' => function ($query) {
                $query->with(['penitip', 'hunter']);
            }])
            ->get();

        $formattedData = $items->map(function ($item) use ($parsedDate) {
            $transaksiPenitipan = $item->transaksiPenitipan()->first();
            if (!$transaksiPenitipan) return null;

            $tanggalMasuk = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->format('d/m/Y');
            $perpanjangan = $item->perpanjangan; // Ambil dari Barang
            Log::info('Perpanjangan Debug', ['id_barang' => $item->id_barang, 'perpanjangan' => $perpanjangan]); // Debug

            return [
                'kode_produk' => $item->kode_barang,
                'nama_produk' => $item->nama_barang,
                'id_penitip' => $transaksiPenitipan->penitip->id_penitip ? 'T' . $transaksiPenitipan->penitip->id_penitip : '-',
                'nama_penitip' => $transaksiPenitipan->penitip->nama_penitip ?? '-',
                'tanggal_masuk' => $tanggalMasuk,
                'perpanjangan' => $perpanjangan == 1 ? 'Ya' : ($perpanjangan == 0 ? 'Tidak' : '-'),
                'id_hunter' => $transaksiPenitipan->hunter ? 'P' . $transaksiPenitipan->hunter->id_pegawai : '-',
                'nama_hunter' => $transaksiPenitipan->hunter ? $transaksiPenitipan->hunter->nama_pegawai ?? '-' : '-',
                'harga' => number_format($item->harga_barang ?? 0, 0, ',', '.'),
            ];
        })->filter()->values();

        return view('owner.warehouse_stock_report', compact('formattedData', 'date'));
    }

    public function downloadWarehouseStockReport(Request $request)
    {
        $this->ensureOwner();

        $date = $request->input('date', Carbon::now()->format('d/m/Y'));
        $parsedDate = Carbon::createFromFormat('d/m/Y', $date)->startOfDay();

        $items = Barang::where('status_barang', 'tersedia')
            ->with(['transaksiPenitipan' => function ($query) {
                $query->with(['penitip', 'hunter']);
            }])
            ->get();

        $formattedData = $items->map(function ($item) use ($parsedDate) {
            $transaksiPenitipan = $item->transaksiPenitipan()->first();
            if (!$transaksiPenitipan) return null;

            $tanggalMasuk = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->format('d/m/Y');
            $perpanjangan = $item->perpanjangan;

            return [
                'kode_produk' => $item->kode_barang,
                'nama_produk' => $item->nama_barang,
                'id_penitip' => $transaksiPenitipan->penitip->id_penitip ? 'T' . $transaksiPenitipan->penitip->id_penitip : '-',
                'nama_penitip' => $transaksiPenitipan->penitip->nama_penitip ?? '-',
                'tanggal_masuk' => $tanggalMasuk,
                'perpanjangan' => $perpanjangan == 1 ? 'Ya' : ($perpanjangan == 0 ? 'Tidak' : '-'),
                'id_hunter' => $transaksiPenitipan->hunter ? 'P' . $transaksiPenitipan->hunter->id_pegawai : '-',
                'nama_hunter' => $transaksiPenitipan->hunter ? $transaksiPenitipan->hunter->nama_pegawai ?? '-' : '-',
                'harga' => number_format($item->harga_barang ?? 0, 0, ',', '.'),
            ];
        })->filter()->values();

        $pdf = PDF::loadView('owner.warehouse_stock_report_pdf', compact('formattedData', 'date'));
        $pdf->setPaper('A4', 'landscape'); // Ubah ke landscape

        return $pdf->download('laporan_stok_gudang_' . str_replace('/', '-', $date) . '.pdf');
    }


    public function monthlySalesOverview(Request $request)
{
    $this->ensureOwner();

    $date = $request->input('date', Carbon::now()->format('Y'));
    $year = Carbon::createFromFormat('Y', $date)->year;

    // Ambil data penjualan bulanan dari transaksi_pembelian dengan join ke keranjang
    $salesData = TransaksiPembelian::selectRaw('MONTH(transaksi_pembelian.tanggal_pembelian) as month, SUM(keranjang.banyak_barang) as barang_terjual, SUM(transaksi_pembelian.total_harga) as total_penjualan')
        ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
        ->whereYear('transaksi_pembelian.tanggal_pembelian', $year)
        ->where('transaksi_pembelian.status_transaksi', 'selesai')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->map(function ($item) {
            return [
                'bulan' => Carbon::create()->month($item->month)->format('F'),
                'barang_terjual' => $item->barang_terjual ?? 0,
                'penjualan_kotor' => number_format($item->total_penjualan ?? 0, 0, ',', '.'),
                'penjualan_kotor_raw' => $item->total_penjualan ?? 0,
            ];
        });

    // Tambahkan semua bulan (Jan-Dec) dengan default 0 kalau nggak ada data
    $allMonths = collect(range(1, 12))->map(function ($month) use ($salesData) {
        $monthName = Carbon::create()->month($month)->format('F');
        $data = $salesData->firstWhere('bulan', $monthName);
        return [
            'bulan' => $monthName,
            'barang_terjual' => $data['barang_terjual'] ?? 0,
            'penjualan_kotor' => $data['penjualan_kotor'] ?? '0',
            'penjualan_kotor_raw' => $data['penjualan_kotor_raw'] ?? 0,
        ];
    });

    $totalBarang = $allMonths->sum('barang_terjual');
    $totalPenjualan = $allMonths->sum('penjualan_kotor_raw');

    return view('owner.monthly_sales_overview', compact('allMonths', 'date', 'totalBarang', 'totalPenjualan'));
}


public function downloadMonthlySalesOverview(\Illuminate\Http\Request $request)
    {
        $this->ensureOwner();

        $date = $request->input('date', Carbon::now()->format('Y'));
        $year = Carbon::createFromFormat('Y', $date)->year;

        $salesData = TransaksiPembelian::selectRaw('MONTH(transaksi_pembelian.tanggal_pembelian) as month, SUM(keranjang.banyak_barang) as barang_terjual, SUM(transaksi_pembelian.total_harga) as total_penjualan')
            ->join('keranjang', 'transaksi_pembelian.id_keranjang', '=', 'keranjang.id_keranjang')
            ->whereYear('transaksi_pembelian.tanggal_pembelian', $year)
            ->where('transaksi_pembelian.status_transaksi', 'selesai')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => Carbon::create()->month($item->month)->format('F'),
                    'barang_terjual' => $item->barang_terjual ?? 0,
                    'penjualan_kotor' => number_format($item->total_penjualan ?? 0, 0, ',', '.'),
                    'penjualan_kotor_raw' => $item->total_penjualan ?? 0,
                ];
            });

        $allMonths = collect(range(1, 12))->map(function ($month) use ($salesData) {
            $monthName = Carbon::create()->month($month)->format('F');
            $data = $salesData->firstWhere('bulan', $monthName);
            return [
                'bulan' => $monthName,
                'barang_terjual' => $data['barang_terjual'] ?? 0,
                'penjualan_kotor' => $data['penjualan_kotor'] ?? '0',
                'penjualan_kotor_raw' => $data['penjualan_kotor_raw'] ?? 0,
            ];
        });

        \Log::info('Sales Data: ' . $salesData->toJson());
        \Log::info('All Months: ' . $allMonths->toJson());

        $totalBarang = $allMonths->sum('barang_terjual');
        $totalPenjualan = $allMonths->sum('penjualan_kotor_raw');

        // Generate chart image using GD
        $chartImage = $this->generateBarChart($allMonths);

        \Log::info('Chart Image Base64 Length: ' . strlen($chartImage));

        $pdf = PDF::loadView('owner.monthly_sales_overview_pdf', compact('allMonths', 'date', 'totalBarang', 'totalPenjualan', 'chartImage'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan_penjualan_bulanan_' . $date . '.pdf');
    }

    protected function generateBarChart($allMonths)
    {
        // Set ukuran gambar
        $width = 800;
        $height = 400;
        $image = imagecreatetruecolor($width, $height);

        // Warna
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        $gray = imagecolorallocate($image, 200, 200, 200);

        // Isi background
        imagefilledrectangle($image, 0, 0, $width, $height, $white);

        // Data dan label
        $labels = $allMonths->pluck('bulan')->toArray();
        $data = $allMonths->pluck('penjualan_kotor_raw')->toArray();
        $maxValue = max($data) ?: 1; // Hindari pembagian nol
        $barWidth = ($width - 100) / count($labels) - 10;
        $barHeightScale = ($height - 100) / $maxValue;

        // Gambar sumbu
        imageline($image, 50, $height - 50, $width - 50, $height - 50, $black); // Sumbu X
        imageline($image, 50, 50, 50, $height - 50, $black); // Sumbu Y

        // Gambar batang
        for ($i = 0; $i < count($labels); $i++) {
            $barHeight = $data[$i] * $barHeightScale;
            $x = 60 + ($i * ($barWidth + 10));
            $y = $height - 60 - $barHeight;
            imagefilledrectangle($image, $x, $y, $x + $barWidth, $height - 60, $blue);

            // Label bulan
            imagestring($image, 2, $x, $height - 40, $labels[$i], $black);

            // Nilai di atas batang
            $value = number_format($data[$i], 0, ',', '.');
            imagestring($image, 2, $x, $y - 15, $value, $black);
        }

        // Simpan gambar ke string (base64)
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Simpan gambar ke file untuk debug
        $filePath = storage_path('app/public/test_chart.png');
        imagepng($image, $filePath);

        // Simpan ke base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Log panjang base64 untuk cek
        \Log::info('Base64 Length: ' . strlen(base64_encode($imageData)));
        \Log::info('Chart Saved to: ' . $filePath);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    public function downloadDonationPdf(Request $request)
    {
        $this->ensureOwner();

        $organisasi = Organisasi::all();
        $query = Donasi::with(['requestDonasi.organisasi', 'barang', 'transaksiPenitipan.penitip']);

        if ($request->filled('id_organisasi')) {
            $query->whereHas('requestDonasi', function ($q) use ($request) {
                $q->where('id_organisasi', $request->id_organisasi);
            });
        }

        $donations = $query->get();

        $data = [
            'donations' => $donations,
            'organisasi' => $organisasi,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = PDF::loadView('owner.donation_history_pdf', $data);

        return $pdf->stream('laporan_donasi_barang.pdf');
    }

    // public function downloadDonationPdf(Request $request)
    // {
    //     $this->ensureOwner();

    //     $organisasi = Organisasi::all();

    //     $query = Donasi::with(['requestDonasi.organisasi', 'barang','transaksiPenitipan.penitip'
    //     ])
    //     ->whereHas('barang', function ($q) {
    //         $q->where('id_kategori', 3);
    //     });

    //     if ($request->filled('id_organisasi')) {
    //         $query->whereHas('requestDonasi', function ($q) use ($request) {
    //             $q->where('id_organisasi', $request->id_organisasi);
    //         });
    //     }

    //     $donations = $query->get();

    //     $data = [
    //         'donations' => $donations,
    //         'organisasi' => $organisasi,
    //         'tanggal_cetak' => now()->format('d F Y'),
    //     ];

    //     $pdf = PDF::loadView('owner.donation_history_pdf', $data);

    //     return $pdf->stream('laporan_donasi_barang.pdf');
    // }

    public function downloadPdf()
    {
        $this->ensureOwner();

        $requests = RequestDonasi::where('status_request', 'belum di proses')
            ->orwhere('status_request', 'Pending')
            ->with(['organisasi', 'pegawai'])
            ->get();

        $pdf = PDF::loadView('owner.donation_requests_pdf', compact('requests'));

        return $pdf->stream('laporan_request_donasi.pdf');
    }

    public function consignmentReport()
    {
        $penitips = Penitip::all();
        return view('owner.consignment_report', compact('penitips'));
    }

    public function downloadConsignmentReport($id)
    {
        $penitip = Penitip::findOrFail($id);
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        $penjualan = DB::table('transaksi_pembelian as tp')
            ->join('keranjang as k', 'tp.id_keranjang', '=', 'k.id_keranjang')
            ->join('detail_keranjang as dk', 'dk.id_keranjang', '=', 'k.id_keranjang')
            ->join('item_keranjang as ik', 'ik.id_item_keranjang', '=', 'dk.id_item_keranjang')
            ->join('barang as b', 'b.id_barang', '=', 'ik.id_barang')
            ->join('transaksi_penitipan as tpen', 'b.id_transaksi_penitipan', '=', 'tpen.id_transaksi_penitipan')
            ->where('tpen.id_penitip', $id)
            ->whereMonth('tp.created_at', $bulanSekarang)
            ->whereYear('tp.created_at', $tahunSekarang)
            ->where('b.status_barang', 'sold')
            ->where('tp.status_transaksi', 'selesai')
            ->orwhere('tp.status_transaksi', 'done')
            ->select(
                'b.kode_barang',
                'b.nama_barang',
                'b.harga_barang',
                DB::raw("DATE(tpen.tanggal_penitipan) as tanggal_masuk"),
                DB::raw("DATE(tp.created_at) as tanggal_terjual")
            )
            ->get()
            ->map(function ($item) {
                $item->tanggal_masuk = Carbon::parse($item->tanggal_masuk);
                $item->tanggal_terjual = Carbon::parse($item->tanggal_terjual);
                $komisiReusemart = $item->harga_barang * 0.2;
                $bonus = $item->tanggal_terjual->diffInDays($item->tanggal_masuk) < 7 ? 0 : 0;
                $item->harga_bersih = $item->harga_barang - $komisiReusemart;
                $item->bonus = $bonus;
                $item->pendapatan = $item->harga_bersih + $bonus;
                return $item;
            });

        // if ($penjualan->isEmpty()) {
        //     return redirect()->back()->with('error', 'Tidak ada transaksi penjualan selesai untuk penitip ini pada bulan ini.');
        // }

        $pdf = Pdf::loadView('owner.consignment_report_pdf', [
            'penitip' => $penitip,
            'penjualan' => $penjualan,
            'bulanSekarang' => $bulanSekarang,
            'tahun' => $tahunSekarang
        ]);

        $namaBulan = Carbon::createFromDate(null, $bulanSekarang, 1)->format('F');
        return $pdf->stream("Consignment_Report_{$penitip->nama_penitip}_{$namaBulan}_{$tahunSekarang}.pdf");
    }

    public function penjualanPerKategori(Request $request)
    {
        $this->ensureOwner();

        $year = $request->input('year', Carbon::now()->year);
        $reportDate = Carbon::now()->format('d F Y');

        $categories = Kategori::all();
        $salesData = [];

        foreach ($categories as $category) {
            $soldItems = Barang::where('id_kategori', $category->id_kategori)
                ->whereHas('itemKeranjangs', function ($queryItemKeranjang) use ($year) {
                    $queryItemKeranjang->whereHas('detailKeranjang', function ($queryDetailKeranjang) use ($year) {
                        $queryDetailKeranjang->whereHas('keranjang', function ($queryKeranjang) use ($year) {
                            $queryKeranjang->whereHas('transaksiPembelian', function ($queryTransaksiPembelian) use ($year) {
                                $queryTransaksiPembelian->whereYear('tanggal_pembelian', $year)
                                    ->where('status_transaksi', 'Done');
                            });
                        });
                    });
                })
                ->count();

            $failedItems = Barang::where('id_kategori', $category->id_kategori)
                ->whereIn('status_barang', ['Returned', 'Donated'])
                ->count();

            $salesData[] = [
                'kategori' => $category->nama_kategori,
                'jumlah_terjual' => $soldItems,
                'jumlah_gagal_terjual' => $failedItems,
            ];
        }

        return view('owner.Laporan.penjualanPerKategori', compact('salesData', 'year', 'reportDate'));
    }

    public function downloadPenjualanPerKategori(Request $request)
    {
        $this->ensureOwner();

        $year = $request->input('year', Carbon::now()->year);
        $reportDate = Carbon::now()->format('d F Y');

        $categories = Kategori::all();
        $salesData = [];

        foreach ($categories as $category) {
            $soldItems = Barang::where('id_kategori', $category->id_kategori)
                ->whereHas('itemKeranjangs', function ($queryItemKeranjang) use ($year) {
                    $queryItemKeranjang->whereHas('detailKeranjang', function ($queryDetailKeranjang) use ($year) {
                        $queryDetailKeranjang->whereHas('keranjang', function ($queryKeranjang) use ($year) {
                            $queryKeranjang->whereHas('transaksiPembelian', function ($queryTransaksiPembelian) use ($year) {
                                $queryTransaksiPembelian->whereYear('tanggal_pembelian', $year)
                                    ->where('status_transaksi', 'Done');
                            });
                        });
                    });
                })
                ->count();

            $failedItems = Barang::where('id_kategori', $category->id_kategori)
                ->whereIn('status_barang', ['Returned', 'Donated'])
                ->count();

            $salesData[] = [
                'kategori' => $category->nama_kategori,
                'jumlah_terjual' => $soldItems,
                'jumlah_gagal_terjual' => $failedItems,
            ];
        }

        $pdf = PDF::loadView('owner.Laporan.DownloadPenjualanPerKategori', compact('salesData', 'year', 'reportDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan Penjualan per Kategori ' . $year . '.pdf');
    }

    public function expiredItems(Request $request)
    {
        $this->ensureOwner();

        $reportDate = Carbon::now()->format('d F Y');
        $today = Carbon::now();

        $expiredItems = Barang::whereHas('transaksiPenitipan', function ($query) use ($today) {
                $query->whereDate('tanggal_berakhir', '<', $today);
            })
            ->whereNotIn('status_barang', ['Sold', 'Donated'])
            ->with(['transaksiPenitipan.penitip'])
            ->get();

        return view('owner.Laporan.expiredItem', compact('expiredItems', 'reportDate'));
    }

    public function downloadExpiredItems(Request $request)
    {
        $this->ensureOwner();

        $reportDate = Carbon::now()->format('d F Y');
        $today = Carbon::now();

        $expiredItems = Barang::whereHas('transaksiPenitipan', function ($query) use ($today) {
                $query->whereDate('tanggal_berakhir', '<', $today);
            })
            ->whereNotIn('status_barang', ['Sold', 'Donated'])
            ->with(['transaksiPenitipan.penitip'])
            ->get();

        $pdf = PDF::loadView('owner.Laporan.DownloadExpiredItem', compact('expiredItems', 'reportDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan Barang Perlu Dikembalikan ' . $reportDate . '.pdf');
    }
}