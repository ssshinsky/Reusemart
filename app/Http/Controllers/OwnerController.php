<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\RequestDonasi;
use App\Models\Donasi;
use App\Models\Barang;
use App\Models\Organisasi;
use App\Models\Penitip;
use App\Models\Keranjang;
use App\Models\TransaksiPembelian;
use App\Models\ItemKeranjang;
use App\Models\DetailKeranjang;
use Carbon\Carbon;
use PDF;

class OwnerController extends Controller
{
    private function ensureOwner()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 1) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function dashboard()
    {
        $this->ensureOwner();

        $totalRequests = RequestDonasi::where('status_request', 'belum di proses')->count();
        $totalDonations = Donasi::count();

        return view('owner.dashboard', compact('totalRequests', 'totalDonations'));
    }

    public function donationRequests()
    {
        $this->ensureOwner();

        $requests = RequestDonasi::where('status_request', 'belum di proses')
            ->with(['organisasi', 'pegawai'])
            ->get();

        return view('owner.donation_requests', compact('requests'));
    }

    public function allocateItems(Request $request)
    {
        $this->ensureOwner();

        $organisasi = Organisasi::all();
        $items = Barang::where('status_barang', 'barang untuk donasi')->get();
        $requestId = $request->query('id_request');

        $selectedOrganisasi = null;
        $requests = collect([]);

        if ($requestId) {
            $requestDonasi = RequestDonasi::where('id_request', $requestId)
                ->where('status_request', 'belum di proses')
                ->whereNotNull('id_organisasi')
                ->with('organisasi')
                ->first();

            if ($requestDonasi) {
                $selectedOrganisasi = $requestDonasi->id_organisasi;
                $requests = RequestDonasi::where('id_organisasi', $selectedOrganisasi)
                    ->where('status_request', 'belum di proses')
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
            ->where('status_request', 'belum di proses')
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
            'status_barang' => 'required|in:barang untuk donasi,didonasikan',
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
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('laporan_komisi_bulanan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
    }
}