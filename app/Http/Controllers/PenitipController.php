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
        $prefix = $pegawai->id_role == 3 ? 'admin' : 'cs';

        return redirect()->route($prefix . '.penitip.index')->with('success', 'Penitip berhasil ditambahkan.');
    }

    // Tampilkan profil penitip yang sedang login
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


    // Tampilkan halaman produk yang tersedia
    public function product()
    {
        $id = Auth::guard('penitip')->id();
        $produk = \App\Models\Barang::with('kategori')->where('id_penitip', $id)->get();
        return view('Penitip.product', compact('produk'));
    }

    // Tampilkan riwayat transaksi penitip
    public function transaction()
    {
        $transaksis = \App\Models\TransaksiPenitipan::with('barang')->where('id_penitip', Auth::guard('penitip')->id());
        return view('Penitip.transaction', compact('transaksis'));
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

        // Kembalikan view partial untuk AJAX
        return view('Penitip.partials.table', compact('transaksis'));
    }

    // Tampilkan produk milik penitip sendiri
    public function myproduct()
    {
        $id_user = session('user.id');

        $penitip = \App\Models\Penitip::where('id_penitip', $id_user)->first();

        if (!$penitip) {
            abort(404, 'Penitip tidak ditemukan');
        }

        $transaksiIds = \App\Models\TransaksiPenitipan::where('id_penitip', $penitip->id_penitip)->pluck('id_transaksi_penitipan');
        $produkSaya = \App\Models\Barang::whereIn('id_transaksi_penitipan', $transaksiIds)->get();
        $products = $produkSaya;
        return view('penitip.myproduct', compact('products'));
    }

    // Tampilkan saldo dan reward penitip
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

    // Menampilkan halaman daftar penitip (item owners)
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if ($pegawai->id_role != 3) {
            abort(403, 'Hanya CS yang boleh mengakses.');
        }

        $penitips = Penitip::all();
        return view('CS.penitip', compact('penitips'));
    }


    // Menampilkan form edit penitip
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

        return redirect()->route('penitip.profile')->with('success', 'Profil berhasil diperbarui.');
    }


    // Update data penitip
    public function update(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);

        // Setelah di Add, seharusnya KTP tidak bisa dihapus atau diedit
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

    // Menonaktifkan penitip
    public function deactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Non Active']);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip dinonaktifkan.');
    }

    // Mengaktifkan kembali penitip
    public function reactivate($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update(['status_penitip' => 'Active']);

        return redirect()->route('cs.penitip.index')->with('success', 'Penitip diaktifkan kembali.');
    }

    // Reset password penitip (ke tanggal lahir atau default tertentu, misalnya "123456")
    public function resetPassword($id)
    {
        $penitip = Penitip::findOrFail($id);
        $penitip->update([
            'password' => Hash::make('123456') // ganti dengan password default sesuai kebutuhan
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

    public function getProfile()
{
    $user = Auth::guard('penitip')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    return response()->json([
        'id' => $user->id_penitip,
        'nama' => $user->nama_penitip,
        'email' => $user->email_penitip,
        'saldo' => $user->saldo_penitip,
        'poin' => $user->poin_penitip,
        'profil_pict' => $user->profil_pict ? asset('storage/' . $user->profil_pict) : null,
        'rata_rating' => $user->rata_rating,
        'banyak_rating' => $user->banyak_rating,
    ]);
}

public function getConsignmentHistory()
{
    $user = Auth::guard('penitip')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $transactions = TransaksiPenitipan::with(['barang.gambar', 'penitip'])
        ->where('id_penitip', $user->id_penitip)
        ->get()
        ->map(function ($transaction) {
            return [
                'id_transaksi' => $transaction->id_transaksi_penitipan,
                'tanggal_penitipan' => $transaction->tanggal_penitipan,
                'status' => $transaction->barang->pluck('status_barang')->first() ?? 'N/A',
                'barang' => $transaction->barang->map(function ($barang) {
                    return [
                        'id_barang' => $barang->id_barang,
                        'nama_barang' => $barang->nama_barang,
                        'harga_barang' => $barang->harga_barang,
                        'status_barang' => $barang->status_barang,
                        'gambar' => $barang->gambar->map(function ($gambar) {
                            return asset('storage/gambar/' . $gambar->gambar_barang);
                        })->first(),
                    ];
                })->values(),
            ];
        });
    return response()->json($transactions);
}

public function getTopSeller()
    {
        try {
            $lastMonth = Carbon::now()->subMonth();
            $startOfMonth = $lastMonth->startOfMonth();
            $endOfMonth = $lastMonth->endOfMonth();

            DB::transaction(function () use ($startOfMonth, $endOfMonth) {
                // 1. Hitung Top Seller
                $topSellerData = Barang::select('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                    ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                    ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                    ->where('barang.status_barang', 'selesai')
                    ->whereBetween('barang.updated_at', [$startOfMonth, $endOfMonth])
                    ->groupBy('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                    ->selectRaw('COUNT(*) as jumlah_barang')
                    ->selectRaw('SUM(barang.harga_barang) as total_penjualan')
                    ->orderByDesc('jumlah_barang')
                    ->orderByDesc('total_penjualan')
                    ->first();

                if ($topSellerData) {
                    // Reset badge semua penitip
                    Penitip::query()->update(['badge' => false]);

                    // Set badge Top Seller
                    Penitip::where('id_penitip', $topSellerData->id_penitip)->update(['badge' => true]);

                    // Beri bonus poin (1% dari total penjualan)
                    $bonusPoin = $topSellerData->total_penjualan * 0.01;
                    Penitip::where('id_penitip', $topSellerData->id_penitip)->increment('poin_penitip', $bonusPoin);

                    Log::info('Top Seller calculated', [
                        'id_penitip' => $topSellerData->id_penitip,
                        'jumlah_barang' => $topSellerData->jumlah_barang,
                        'total_penjualan' => $topSellerData->total_penjualan,
                        'bonus_poin' => $bonusPoin,
                    ]);
                } else {
                    Log::info('No Top Seller for last month');
                }

                // 2. Update status barang tidak diambil > 7 hari
                $expiredItems = Barang::where('status_barang', 'menunggu pengambilan')
                    ->where('tanggal_berakhir', '<', Carbon::now()->subDays(7))
                    ->get();

                foreach ($expiredItems as $item) {
                    $item->update(['status_barang' => 'barang untuk donasi']);
                    Log::info('Item updated to donation', ['id_barang' => $item->id_barang]);
                }
            });

            // 3. Ambil data Top Seller untuk response
            $topSeller = Barang::select('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                ->where('barang.status_barang', 'selesai')
                ->whereBetween('barang.updated_at', [$startOfMonth, $endOfMonth])
                ->groupBy('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                ->selectRaw('COUNT(*) as jumlah_barang')
                ->selectRaw('SUM(barang.harga_barang) as total_penjualan')
                ->orderByDesc('jumlah_barang')
                ->orderByDesc('total_penjualan')
                ->first();

            if (!$topSeller) {
                // Fallback: Cari Top Seller dari semua waktu
                $topSeller = Barang::select('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                    ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                    ->join('penitip', 'transaksi_penitipan.id_penitip', '=', 'penitip.id_penitip')
                    ->where('barang.status_barang', 'selesai')
                    ->groupBy('penitip.id_penitip', 'penitip.nama_penitip', 'penitip.profil_pict')
                    ->selectRaw('COUNT(*) as jumlah_barang')
                    ->selectRaw('SUM(barang.harga_barang) as total_penjualan')
                    ->orderByDesc('jumlah_barang')
                    ->orderByDesc('total_penjualan')
                    ->first();

                if (!$topSeller) {
                    return response()->json(['message' => 'No Top Seller available'], 404);
                }

                return response()->json([
                    'id_penitip' => $topSeller->id_penitip,
                    'nama_penitip' => $topSeller->nama_penitip,
                    'profil_pict' => $topSeller->profil_pict ? asset('storage/' . $topSeller->profil_pict) : null,
                    'jumlah_barang' => $topSeller->jumlah_barang,
                    'total_penjualan' => $topSeller->total_penjualan,
                    'bulan' => 'All Time',
                ]);
            }

            return response()->json([
                'id_penitip' => $topSeller->id_penitip,
                'nama_penitip' => $topSeller->nama_penitip,
                'profil_pict' => $topSeller->profil_pict ? asset('storage/' . $topSeller->profil_pict) : null,
                'jumlah_barang' => $topSeller->jumlah_barang,
                'total_penjualan' => $topSeller->total_penjualan,
                'bulan' => $lastMonth->format('F Y'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating Top Seller', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error processing request'], 500);
        }
    }

}