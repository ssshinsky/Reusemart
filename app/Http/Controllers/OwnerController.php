<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestDonasi;
use App\Models\Donasi;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Log;
use App\Models\Barang;
use App\Models\Organisasi;
use App\Models\transaksiPenitipan;
use App\Models\Penitip;

class OwnerController extends Controller
{
    public function login()
    {
        return view('owner.login');
    }

    public function doLogin(Request $request)
    {
        $credentials = $request->validate([
            'email_pegawai' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('owner')->attempt(['email_pegawai' => $request->email_pegawai, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('owner.dashboard');
        }

        return back()->withErrors(['email_pegawai' => 'Email pegawai atau password salah.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('owner.login');
    }

    public function dashboard()
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }

        // Ambil jumlah request donasi dengan status "belum di proses"
        $totalRequests = RequestDonasi::where('status_request', 'belum di proses')->count();
        // Ambil jumlah donasi
        $totalDonations = Donasi::count();

        // Kirim data ke view
        return view('owner.dashboard', compact('totalRequests', 'totalDonations'));
    }

    public function donationRequests()
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }

        // Ambil request donasi dengan status "belum di proses"
        $requests = RequestDonasi::where('status_request', 'belum di proses')
            ->with(['organisasi', 'pegawai'])
            ->get();
        Log::info('Fetched requests: ', $requests->toArray());
        return view('owner.donation_requests', compact('requests'));
    }

    public function allocateItems(Request $request)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }

        // Ambil semua organisasi
        $organisasi = Organisasi::all();
        
        // Ambil barang dengan status 'akan didonasikan'
        $items = Barang::where('status_barang', 'barang untuk donasi')->get();
        
        // Pre-fill request_id dari query parameter
        $requestId = $request->query('id_request');

        // Kalau ada request_id, ambil organisasi terkait
        $selectedOrganisasi = null;
        $requests = collect([]);
        if ($requestId) {
            $requestDonasi = RequestDonasi::where('id_request', $requestId)
                ->where('status_request', 'belum di proses') // Filter status
                ->whereNotNull('id_organisasi')
                ->with('organisasi')
                ->first();
            if ($requestDonasi) {
                $selectedOrganisasi = $requestDonasi->id_organisasi;
                $requests = RequestDonasi::where('id_organisasi', $selectedOrganisasi)
                    ->where('status_request', 'belum di proses') // Filter status
                    ->whereNotNull('id_organisasi')
                    ->with('organisasi')
                    ->get();
            }
        }

        return view('owner.allocate_items', compact('organisasi', 'items', 'requestId', 'requests', 'selectedOrganisasi'));
    }

    public function getRequestsByOrganisasi(Request $request)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $id_organisasi = $request->query('id_organisasi');
        
        $requests = RequestDonasi::where('id_organisasi', $id_organisasi)
            ->where('status_request', 'belum di proses') // Filter status
            ->whereNotNull('id_organisasi')
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
        // Periksa autentikasi dan role
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect()->route('owner.login')->with('error', 'Akses ditolak.');
        }

        // Ambil semua donasi dengan relasi barang
        $donations = Donasi::with('barang')->get();

        // Jika ada ID donasi yang dipilih (misalnya dari query atau form sebelumnya), ambil data spesifik
        $selectedDonasi = null;
        if ($request->has('id_donasi') && $request->id_donasi != '') {
            $selectedDonasi = Donasi::with('barang')->find($request->id_donasi);
        }

        // Log untuk debugging
        Log::info('Fetched donations for update: ', $donations->toArray());

        return view('owner.update_donation', compact('donations', 'selectedDonasi'));
    }

    public function rewards()
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }
        return view('owner.rewards');
    }

    public function deleteRequest($id)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $request = RequestDonasi::find($id);
        if ($request) {
            $request->delete();
            return response()->json(['message' => 'Request berhasil dihapus.']);
        }

        return response()->json(['message' => 'Request tidak ditemukan.'], 404);
    }

    public function storeAllocation(Request $request)
    {
        $request->validate([
            'id_request' => 'required|exists:request_donasi,id_request',
            'id_barang' => 'required|exists:barang,id_barang',
            'nama_penerima' => 'required|string',
            'tanggal_donasi' => 'required|date',
            'id_organisasi' => 'required|exists:organisasi,id_organisasi',
        ]);

        // Validasi barang: harus berstatus "barang untuk donasi"
        $barang = Barang::where('id_barang', $request->id_barang)
            ->where('status_barang', 'barang untuk donasi')
            ->first();
        if (!$barang) {
            Log::error('Barang tidak tersedia untuk donasi', [
                'id_barang' => $request->id_barang,
                'status_barang' => $barang ? $barang->status_barang : 'not found',
            ]);
            return redirect()->back()->with('error', 'Barang tidak tersedia untuk donasi.');
        }

        // Simpan donasi
        $donasi = Donasi::create([
            'id_request' => $request->id_request,
            'id_barang' => $request->id_barang,
            'nama_penerima' => $request->nama_penerima,
            'tanggal_donasi' => $request->tanggal_donasi,
            'status' => 'sudah di donasikan',
        ]);

        // Ubah status_barang ke "didonasikan"
        $barang->update(['status_barang' => 'didonasikan']);

        // Berikan poin ke penitip
        $poin = 0;
        if ($barang->transaksiPenitipan) {
            $penitip = $barang->transaksiPenitipan->penitip;
            if ($penitip) {
                $hargaBarang = $barang->harga_barang ?? 0;
                if ($hargaBarang <= 0) {
                    Log::warning('Harga barang tidak valid untuk poin', [
                        'id_barang' => $barang->id_barang,
                        'harga_barang' => $hargaBarang,
                    ]);
                } else {
                    $poin_penitip = floor($hargaBarang / 10000); // 1 poin per Rp10.000
                    $penitip->increment('poin_penitip', $poin_penitip);

                    Log::info('Poin diberikan', [
                        'id_penitip' => $penitip->id_penitip,
                        'poin' => $poin_penitip,
                        'harga_barang' => $hargaBarang,
                        'id_barang' => $barang->id_barang,
                        'id_donasi' => $donasi->id_donasi,
                    ]);
                }
            } else {
                Log::warning('Penitip tidak ditemukan untuk barang', [
                    'id_barang' => $barang->id_barang,
                    'id_transaksi_penitipan' => $barang->id_transaksi_penitipan,
                ]);
            }
        } else {
            Log::warning('Transaksi penitipan tidak ditemukan untuk barang', [
                'id_barang' => $barang->id_barang,
                'id_transaksi_penitipan' => $barang->id_transaksi_penitipan,
            ]);
        }

        Log::info('Donasi dibuat', [
            'id_donasi' => $donasi->id_donasi,
            'id_barang' => $request->id_barang,
            'status_barang' => $barang->status_barang,
            'poin_diberikan' => $poin_penitip,
        ]);

        $message = $poin_penitip > 0 ? "Barang berhasil dialokasikan! Poin reward: $poin_penitip diberikan kepada penitip." : "Barang berhasil dialokasikan!";
        return redirect()->back()->with('success', $message);
    }

    public function donationHistory(Request $request)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }

        // Ambil semua organisasi untuk dropdown filter
        $organisasi = Organisasi::all();

        // Ambil data donasi dengan relasi
        $query = Donasi::with(['requestDonasi.organisasi', 'barang']);

        // Filter berdasarkan organisasi jika ada
        if ($request->has('id_organisasi') && $request->id_organisasi != '') {
            $query->whereHas('requestDonasi', function ($q) use ($request) {
                $q->where('id_organisasi', $request->id_organisasi);
            });
        }

        $donations = $query->get();

        return view('owner.donation_history', compact('donations', 'organisasi'));
    }

    public function updateDonasiStore(Request $request)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return redirect('/owner/login')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'id_donasi' => 'required|exists:donasi,id_donasi',
            'tanggal_donasi' => 'required|date',
            'nama_penerima' => 'required|string',
            'status_barang' => 'required|in:barang untuk donasi,didonasikan',
        ]);

        $donasi = Donasi::with('barang.transaksiPenitipan.penitip')->find($request->id_donasi);
        if (!$donasi) {
            Log::error('Donasi tidak ditemukan', ['id_donasi' => $request->id_donasi]);
            return redirect()->back()->with('error', 'Donasi tidak ditemukan.');
        }

        // Perbarui data donasi
        $donasi->update([
            'tanggal_donasi' => $request->tanggal_donasi,
            'nama_penerima' => $request->nama_penerima,
        ]);

        // Perbarui status_barang
        $barang = Barang::where('id_barang', $donasi->id_barang)->first();
        if (!$barang) {
            Log::error('Barang tidak ditemukan', ['id_barang' => $donasi->id_barang]);
            return redirect()->back()->with('error', 'Barang terkait tidak ditemukan.');
        }

        $oldStatus = $barang->status_barang;
        $barang->update(['status_barang' => $request->status_barang]);


        Log::info('Donasi diupdate', [
            'id_donasi' => $donasi->id_donasi,
            'status_barang' => $request->status_barang,
        ]);

        return redirect()->back()->with('success');
    }

    public function getDonasi(Request $request)
    {
        if (!Auth::guard('owner')->check() || !Auth::guard('owner')->user()->isOwner()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $id_donasi = $request->query('id_donasi');
        $donasi = Donasi::with('barang')->find($id_donasi);

        if (!$donasi) {
            return response()->json(['error' => 'Donasi tidak ditemukan.'], 404);
        }

        return response()->json([
            'tanggal_donasi' => $donasi->tanggal_donasi,
            'nama_penerima' => $donasi->nama_penerima,
            'barang' => [
                'status_barang' => $donasi->barang->status_barang,
            ],
        ]);
    }

}

