<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\TransaksiPenitipan;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenitipController extends Controller
{
    public function create()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if ($pegawai->id_role != 3) {
            abort(403, 'Hanya CS yang boleh mengakses.');
        }

        return view('CS.add_penitip');
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
        ]);

        $pathKTP = null;
        if ($request->hasFile('foto_ktp')) {
            $pathKTP = $request->file('foto_ktp')->store('ktp_penitip', 'public');
        }

        Penitip::create([
            'foto_ktp' => $pathKTP,
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
            'status_penitip' => 'Active',
            'saldo_penitip' => 0,
            'rata_rating'     => 0,
        ]);
        $pegawai = Auth::guard('pegawai')->user();
        $prefix = $pegawai->id_role == 3 ? 'cs' : 'admin';

        return redirect()->route($prefix . '.penitip.index')->with('success', 'Penitip berhasil ditambahkan.');
    }

    public function profile()
    {
        if (session('role') !== 'penitip') {
            return redirect('/login')->with('error', 'Unauthorized');
        }

        $id = session('user.id');
        $penitip = Penitip::find($id);

        if (!$penitip) {
            abort(404, 'Data penitip tidak ditemukan.');
        }

        return view('penitip.profile', compact('penitip'));
    }

    public function product()
    {
        $id = Auth::guard('penitip')->id();
        $produk = \App\Models\Barang::with('kategori')->where('id_penitip', $id)->get();
        return view('Penitip.product', compact('produk'));
    }

    public function transaction(Request $request)
    {
        $id_penitip = Auth::guard('penitip')->id();

        // Pastikan user sudah login
        if (!$id_penitip) {
            return redirect()->back()->with('error', 'Anda belum login sebagai penitip.');
        }

        // Ambil parameter search
        $search = $request->query('search');

        // Query untuk transaksi penjualan
        $query = DB::table('transaksi_pembelian as tp')
            ->join('keranjang as k', 'tp.id_keranjang', '=', 'k.id_keranjang')
            ->join('detail_keranjang as dk', 'dk.id_keranjang', '=', 'k.id_keranjang')
            ->join('item_keranjang as ik', 'ik.id_item_keranjang', '=', 'dk.id_item_keranjang')
            ->join('barang as b', 'b.id_barang', '=', 'ik.id_barang')
            ->join('transaksi_penitipan as tpen', 'b.id_transaksi_penitipan', '=', 'tpen.id_transaksi_penitipan')
            ->where('tpen.id_penitip', $id_penitip)
            ->where('b.status_barang', 'sold')
            ->where('tp.status_transaksi', 'selesai')
            ->select(
                'tp.id_pembelian as id_transaksi',
                'b.kode_barang',
                'b.nama_barang',
                'b.harga_barang',
                DB::raw("DATE(tpen.tanggal_penitipan) as tanggal_masuk"),
                DB::raw("DATE(tp.created_at) as tanggal_terjual")
            );

        // Terapkan pencarian jika ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('b.nama_barang', 'like', "%{$search}%")
                ->orWhere('b.kode_barang', 'like', "%{$search}%")
                ->orWhere('tp.id_transaksi_pembelian', 'like', "%{$search}%");
            });
        }

        // Eksekusi query dan format data
        $penjualan = $query->get()->map(function ($item) {
            $item->tanggal_masuk = Carbon::parse($item->tanggal_masuk);
            $item->tanggal_terjual = Carbon::parse($item->tanggal_terjual);
            $komisiReusemart = $item->harga_barang * 0.2; // Komisi 20%
            $item->harga_bersih = $item->harga_barang - $komisiReusemart;
            $item->pendapatan = $item->harga_bersih;
            return $item;
        });

        // Kirim data ke view
        return view('penitip.transaction', compact('penjualan'));
    }

    public function filterTransaction($type)
    {
        $query = \App\Models\TransaksiPenitipan::with('barang')
            ->where('id_penitip', Auth::guard('penitip')->id());

        switch ($type) {
            case 'sold':
                $query->where('status_transaksi', 'COMPLETED');
                break;
            case 'expired':
                $query->where('status_transaksi', 'EXPIRED');
                break;
            case 'donated':
                $query->where('status_transaksi', 'DONATED');
                break;
            case 'all':
            default:
                break;
        }

        $transaksis = $query->get();

        return view('Penitip.partials.table', compact('transaksis'));
    }

    public function myproduct()
    {
        $id_user = session('user.id');
        $penitip = \App\Models\Penitip::where('id_penitip', $id_user)->first();

        if (!$penitip) {
            abort(404, 'Penitip tidak ditemukan');
        }

        $transaksiIds = \App\Models\TransaksiPenitipan::where('id_penitip', $penitip->id_penitip)->pluck('id_transaksi_penitipan');
        $products = Barang::with('transaksiPenitipan')->whereIn('id_transaksi_penitipan', $transaksiIds)->get();

        // Otomatis ubah status jika masa penitipan sudah habis
        foreach ($products as $product) {
            $transaksi = $product->transaksiPenitipan;
            if (($product->status_barang === 'Available' || $product->status_barang === 'tersedia') && $transaksi && now()->gt($product->tanggal_berakhir)) {
                $product->update([
                    'status_barang' => 'Awaiting Owner Pickup',
                    'batas_pengambilan' => $product->tanggal_berakhir->copy()->addDays(7),
                ]);
            }
        }

        return view('penitip.myproduct', compact('products'));
    }

    public function rewards()
    {
        $sessionUser = session('user');

        if (!$sessionUser || !isset($sessionUser['id'])) {
            abort(403, 'User tidak ditemukan dalam sesi.');
        }

        $penitip = \App\Models\Penitip::where('id_penitip', $sessionUser['id'])->first();

        if (!$penitip) {
            abort(404, 'Data penitip tidak ditemukan.');
        }

        return view('penitip.rewards', compact('penitip'));
    }

    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if ($pegawai->id_role != 3) {
            abort(403, 'Hanya CS yang boleh mengakses.');
        }

        $penitips = Penitip::all();
        return view('CS.penitip', compact('penitips'));
    }

    public function edit($id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if ($pegawai->id_role != 3) {
            abort(403, 'Hanya CS yang boleh mengakses.');
        }

        $penitip = Penitip::findOrFail($id);
        return view('CS.edit_penitip', compact('penitip'));
    }

    public function editProfile($id)
    {
        $penitip = Penitip::findOrFail($id);
        return view('penitip.edit', compact('penitip'));
    }

    public function updateProfile(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);

        $request->validate([
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip,' . $id . ',id_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip,' . $id . ',id_penitip',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
            'profil_pict' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('profil_pict')) {
            $file = $request->file('profil_pict');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan file baru
            Storage::disk('public')->putFileAs('foto_penitip', $file, $filename);

            // Hapus foto lama (jika bukan default)
            if ($penitip->profil_pict && $penitip->profil_pict !== 'default.png') {
                Storage::disk('public')->delete('foto_penitip/' . $penitip->profil_pict);
            }

            $penitip->profil_pict = $filename; // set foto baru
        }

        // Update semua field termasuk yang mungkin baru diset di atas
        $penitip->update([
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'profil_pict' => $penitip->profil_pict, // yang ini sekarang sudah pasti berisi nilai baru atau lama
        ]);

        return redirect()->route('penitip.profile')->with('success', 'Profil berhasil diperbarui.');
    }


    public function update(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);

        if ($request->hasFile('foto_ktp')) {
            abort(403, 'Mengubah foto KTP tidak diperbolehkan.');
        }

        $request->validate([
            'nik_penitip' => 'required|string|unique:penitip,nik_penitip,' . $id . ',id_penitip',
            'nama_penitip' => 'required|string',
            'email_penitip' => 'required|email|unique:penitip,email_penitip,' . $id . ',id_penitip',
            'no_telp' => 'required|string',
            'alamat' => 'required|string',
            'profil_pict' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profil_pict')) {
            $file = $request->file('profil_pict');
            $filename = time() . '_' . $file->getClientOriginalName();

            Storage::disk('public')->putFileAs('foto_penitip', $file, $filename);

            $penitip->profil_pict = $filename;
        }

        $penitip->update([
            'nik_penitip' => $request->nik_penitip,
            'nama_penitip' => $request->nama_penitip,
            'email_penitip' => $request->email_penitip,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'profil_pict' => $penitip->profil_pict,
        ]);

        return redirect()->route('cs.penitip.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function deactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Non Active']);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip dinonaktifkan.');
    }

    public function reactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Active']);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip diaktifkan kembali.');
    }

    public function resetPassword($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update([
            'password' => Hash::make('123456')
        ]);

        return redirect()->route('cs.penitip.index')->with('success', 'Password berhasil direset.');
    }

    public function search(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai || !in_array($pegawai->id_role, [3])) {
            abort(403, 'Akses ditolak.');
        }

        if (!$request->ajax()) {
            return response('', 204);
        }

        $query = $request->query('q');

        $penitips = Penitip::where('nama_penitip', 'LIKE', "%$query%")
            ->orWhere('email_penitip', 'LIKE', "%$query%")
            ->orWhere('nik_penitip', 'LIKE', "%$query%")
            ->get();

        $prefix = 'cs';
        $html = '';

        foreach ($penitips as $penitip) {
            $status = strtolower(trim($penitip->status_penitip));
            $html .= '
            <tr>
                <td class="center">'.$penitip->id_penitip.'</td>
                <td'.($status !== 'active' ? ' style="color: #E53E3E; font-weight: bold;"' : '').'>'.$penitip->nama_penitip.'</td>
                <td>'.$penitip->nik_penitip.'</td>
                <td>'.$penitip->email_penitip.'</td>
                <td>'.$penitip->no_telp.'</td>
                <td>'.$penitip->alamat.'</td>
                <td class="center">Rp '.number_format($penitip->saldo_penitip, 0, ',', '.').'</td>
                <td class="center">'.number_format($penitip->rata_rating, 1).'</td>
                <td class="center">'.ucwords($status).'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route($prefix.'.penitip.edit', $penitip->id_penitip).'" class="edit-btn">✏️</a>';

            if ($status === 'active') {
                $html .= '
                    <form action="'.route($prefix.'.penitip.deactivate', $penitip->id_penitip).'" method="POST" class="form-nonaktif" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Deactivate">🛑</button>
                    </form>';
            } else {
                $html .= '
                    <form action="'.route($prefix.'.penitip.reactivate', $penitip->id_penitip).'" method="POST" class="form-reactivate" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Reactivate">♻️</button>
                    </form>';
            }

            $html .= '</td></tr>';
        }

        if ($penitips->isEmpty()) {
            $html = '<tr><td colspan="9" class="center">No item owner found.</td></tr>';
        }

        return response($html);
    }

    public function getPenitipById($id)
    {
        try {
            Log::debug('Fetching penitip by ID', ['id_penitip' => $id]);

            $penitip = Penitip::find($id);
            if (!$penitip) {
                Log::warning('Penitip not found', ['id_penitip' => $id]);
                return response()->json(['message' => 'Penitip not found'], 404);
            }

            return response()->json([
                'id_penitip' => $penitip->id_penitip,
                'nama' => $penitip->nama_penitip,
                'email' => $penitip->email_penitip,
                'saldo' => $penitip->saldo_penitip ?? 0,
                'poin' => $penitip->poin_penitip ?? 0,
                'rata_rating' => $penitip->rata_rating ?? 0,
                'banyak_rating' => $penitip->banyak_rating ?? 0,
                'profil_pict' => $penitip->profil_pict && file_exists(storage_path('app/public/' . $penitip->profil_pict))
                    ? asset('storage/' . $penitip->profil_pict)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching penitip by ID', [
                'id_penitip' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching penitip'], 500);
        }
    }

    public function getProfile()
    {
        try {
            Log::debug('Starting getProfile', ['user_id' => Auth::id()]);
            $penitip = Auth::user();

            return response()->json([
                'id_penitip' => $penitip->id_penitip,
                'nama' => $penitip->nama_penitip,
                'email' => $penitip->email_penitip,
                'saldo' => $penitip->saldo_penitip ?? 0,
                'poin' => $penitip->poin_penitip ?? 0,
                'rata_rating' => $penitip->rata_rating ?? 0,
                'banyak_rating' => $penitip->banyak_rating ?? 0,
                'profil_pict' => $penitip->profil_pict && file_exists(storage_path('app/public/' . $penitip->profil_pict))
                    ? asset('storage/' . $penitip->profil_pict)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getProfile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching profile'], 500);
        }
    }

    public function getConsignmentHistoryById($id)
    {
        try {
            Log::debug('Fetching consignment history by ID', ['id_penitip' => $id]);

            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['message' => 'Invalid ID'], 400);
            }

            $penitip = Penitip::find($id);
            if (!$penitip) {
                Log::warning('Penitip not found', ['id_penitip' => $id]);
                return response()->json(['message' => 'Penitip not found'], 404);
            }

            $history = TransaksiPenitipan::with(['barang.gambar'])
                ->where('id_penitip', $id)
                ->take(50)
                ->get()
                ->map(function ($transaksi) {
                    Log::debug('Processing transaction', ['id_transaksi' => $transaksi->id_transaksi_penitipan]);
                    return [
                        'id_transaksi' => $transaksi->id_transaksi_penitipan,
                        'tanggal_penitipan' => $transaksi->tanggal_penitipan ? $transaksi->tanggal_penitipan->format('Y-m-d') : 'Unknown',
                        'status' => $transaksi->status_transaksi ?? 'Unknown',
                        'barang' => $transaksi->barang->map(function ($barang) {
                            return [
                                'nama_barang' => $barang->nama_barang,
                                'harga_barang' => $barang->harga_barang ?? 0,
                                'status_barang' => $barang->status_barang ?? 'Unknown',
                                'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                    ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                    : null,
                                'tanggal_berakhir' => $barang->tanggal_berakhir ? $barang->tanggal_berakhir->format('Y-m-d') : 'Unknown',
                                'perpanjangan' => $barang->perpanjangan ?? 0,
                            ];
                        })->take(1)->toArray(),
                    ];
                });

            Log::info('History fetched', ['id_penitip' => $id, 'count' => count($history)]);
            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error fetching consignment history by ID', [
                'id_penitip' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching history'], 500);
        }
    }

    public function getConsignmentHistory()
    {
        try {
            Log::debug('Starting getConsignmentHistory', ['user_id' => Auth::id()]);
            $penitip = Auth::user();

            $history = TransaksiPenitipan::with(['barang.gambar'])
                ->where('id_penitip', $penitip->id_penitip)
                ->take(50)
                ->get()
                ->map(function ($transaksi) {
                    Log::debug('Processing transaction', ['id_transaksi' => $transaksi->id_transaksi_penitipan]);
                    return [
                        'id_transaksi' => $transaksi->id_transaksi_penitipan,
                        'tanggal_penitipan' => $transaksi->tanggal_penitipan ? $transaksi->tanggal_penitipan->format('Y-m-d') : 'Unknown',
                        'status' => $transaksi->status_transaksi ?? 'Unknown',
                        'barang' => $transaksi->barang->map(function ($barang) {
                            return [
                                'nama_barang' => $barang->nama_barang,
                                'harga_barang' => $barang->harga_barang ?? 0,
                                'status_barang' => $barang->status_barang ?? 'Unknown',
                                'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                    ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                    : null,
                                'tanggal_berakhir' => $barang->tanggal_berakhir ? $barang->tanggal_berakhir->format('Y-m-d') : 'Unknown',
                                'perpanjangan' => $barang->perpanjangan ?? 0,
                            ];
                        })->take(1)->toArray(),
                    ];
                });

            Log::info('History fetched', ['count' => count($history)]);
            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error in getConsignmentHistory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching history'], 500);
        }
    }

    public function perpanjang($id)
    {
        $barang = Barang::findOrFail($id);

        if (strtolower($barang->status_barang) === 'available' || strtolower($barang->status_barang) === 'tersedia' && $barang->perpanjangan == 0) {
            
            
            $transaksi = $barang->transaksiPenitipan;
            
            if (!$transaksi || !$barang->tanggal_berakhir) {
                return back()->with('error', 'Transaksi atau tanggal berakhir tidak valid.');
            }
            
            $tanggalBaru = Carbon::parse($barang->tanggal_berakhir)->addDays(30);
            $barang->update(['tanggal_berakhir' => $tanggalBaru]);
            
            $barang->update(['perpanjangan' => 1]);
            
            return back()->with('success', 'Penitipan berhasil diperpanjang 30 hari.');
            
        } else {
            return back()->with('error', 'Barang tidak dapat diperpanjang.');
        }
    }

    public function confirmPickup($id)
    {
        $barang = Barang::findOrFail($id);

        if (!in_array($barang->status_barang, ['Available', 'Awaiting Owner Pickup', 'tersedia'])) {
            return response()->json([
                'message' => 'This item is not eligible for pickup.'
            ], 422);
        }

        $now = Carbon::now();
        $tanggalBerakhir = optional($barang)->tanggal_berakhir;

        if (!$tanggalBerakhir) {
            return response()->json([
                'message' => 'Cannot determine end of storage period.'
            ], 422);
        }

        if ($barang->status_barang === 'Available' && $now->lt($tanggalBerakhir)) {
            // Ambil sebelum waktu penitipan habis
            $batasAmbil = $now->copy()->addDays(7);
        } else {
            // Ambil Setelah waktu penitipan habis
            $batasAmbil = Carbon::parse($tanggalBerakhir)->addDays(7);

            if ($now->gt($batasAmbil)) {
                return response()->json([
                    'message' => 'Pickup period has already expired. This item will be donated.'
                ], 422);
            }
        }

        $barang->update([
            'status_barang' => 'Ready for Pickup',
            'tanggal_konfirmasi_pengambilan' => $now,
            'batas_pengambilan' => $batasAmbil,
        ]);

        return response()->json([
            'message' => 'Pickup confirmed.',
            'pickup_deadline' => $batasAmbil->toDateTimeString()
        ]);
    }

    public function getPickupDeadline($id)
    {
        $barang = Barang::with('transaksiPenitipan')->findOrFail($id);
        $penitip = auth()->guard('penitip')->user();

        // Validasi apakah ini barang penitip yang login
        if ($barang->transaksiPenitipan->id_penitip !== $penitip->id_penitip) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], 403);
        }

        $now = Carbon::now();
        $tanggalBerakhir = Carbon::parse($barang->tanggal_berakhir);
        $batasAmbil = $barang->status_barang === 'Available' && $now->lt($tanggalBerakhir)
            ? $now->copy()->addDays(7)
            : $tanggalBerakhir->copy()->addDays(7);

        if ($now->gt($batasAmbil)) {
            return response()->json(['error' => true, 'message' => 'Pickup deadline already passed.']);
        }

        return response()->json([
            'pickup_deadline' => $batasAmbil->format('d F Y H:i:s'),
            'status_barang' => $barang->status_barang,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $penitip = auth()->guard('penitip')->user();
        if (!$penitip) abort(403, 'Unauthorized');

        $query = strtolower($request->input('q'));

        // Konversi pencarian ke nilai perpanjangan
        $perpanjanganSearch = null;
        if (in_array($query, ['extended', 'perpanjang'])) {
            $perpanjanganSearch = 1;
        } elseif (in_array($query, ['not extended', 'belum diperpanjang'])) {
            $perpanjanganSearch = 0;
        }

        $transaksiIds = TransaksiPenitipan::where('id_penitip', $penitip->id_penitip)
            ->pluck('id_transaksi_penitipan');

        $products = Barang::with(['gambar', 'kategori'])
            ->whereIn('id_transaksi_penitipan', $transaksiIds)
            ->where(function ($q) use ($query, $perpanjanganSearch) {
                $q->where('kode_barang', 'like', "%$query%")
                ->orWhere('nama_barang', 'like', "%$query%")
                ->orWhere('harga_barang', 'like', "%$query%")
                ->orWhere('berat_barang', 'like', "%$query%")
                ->orWhere('deskripsi_barang', 'like', "%$query%");
                
                if (!is_null($perpanjanganSearch)) {
                    $q->orWhere('perpanjangan', $perpanjanganSearch);
                }
            })
            ->get();

        return view('penitip.partials.product_grid', compact('products'));
    }
}