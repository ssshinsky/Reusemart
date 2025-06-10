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
        $produkSaya = \App\Models\Barang::whereIn('id_transaksi_penitipan', $transaksiIds)->get();
        $products = $produkSaya;
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
}