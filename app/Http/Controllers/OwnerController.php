<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\RequestDonasi;
use App\Models\Donasi;
use App\Models\Barang;
use App\Models\Organisasi;
use App\Models\Penitip;
use App\Models\Kategori;
use App\Models\TransaksiPembelian;
use App\Models\ItemKeranjang;
use App\Models\DetailKeranjang;
use App\Models\Keranjang;
use App\Models\TransaksiPenitipan;
// use Barryvdh\DomPDF\Facade\Pdf as PDF;
// use Carbon\Carbon;
use ChartJs\Chart;

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

    public function downloadPdf()
    {
        $this->ensureOwner();

        $requests = RequestDonasi::where('status_request', 'belum di proses')
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
                $bonus = $item->tanggal_terjual->diffInDays($item->tanggal_masuk) < 7 ? ($komisiReusemart * 0.1) : 0;
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