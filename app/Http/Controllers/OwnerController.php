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
}
