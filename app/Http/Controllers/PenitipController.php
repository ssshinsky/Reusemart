<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiPenitipan;
use App\Models\Barang;
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
        $penitip = auth()->guard('penitip')->user();

        if (!$penitip) {
            return redirect('/login')->with('error', 'Unauthorized');
        }


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
        $penitip = auth()->guard('penitip')->user();

        if (!$penitip) {
            abort(404, 'Penitip tidak ditemukan');
        }

        $transaksiIds = TransaksiPenitipan::where('id_penitip', $penitip->id_penitip)->pluck('id_transaksi_penitipan');
        $products = Barang::with('transaksiPenitipan')->whereIn('id_transaksi_penitipan', $transaksiIds)->get();

        // Otomatis ubah status jika masa penitipan sudah habis
        foreach ($products as $product) {
            $transaksi = $product->transaksiPenitipan;
            if ($product->status_barang === 'Available' && $transaksi && now()->gt($transaksi->tanggal_berakhir)) {
                $product->update([
                    'status_barang' => 'Awaiting Owner Pickup'
                ]);
            }
        }

        return view('penitip.myproduct', compact('products'));
    }


    // Tampilkan saldo dan reward penitip
    public function rewards()
    {
        $penitip = auth()->guard('penitip')->user();

        if (!$penitip) {
            abort(403, 'User tidak ditemukan atau belum login.');
        }

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
                ->orWhere('deskripsi_barang', 'like', "%$query%")
                ->orWhere('status_garansi', 'like', "%$query%");
                
                if (!is_null($perpanjanganSearch)) {
                    $q->orWhere('perpanjangan', $perpanjanganSearch);
                }
            })
            ->get();

        return view('penitip.partials.product_grid', compact('products'));
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

    public function perpanjang($id)
    {
        $barang = Barang::findOrFail($id);

        if (strtolower($barang->status_barang) !== 'available' || $barang->perpanjangan == 1) {
            return back()->with('error', 'Barang tidak dapat diperpanjang.');
        }

        $transaksi = $barang->transaksiPenitipan;

        if (!$transaksi || !$transaksi->tanggal_berakhir) {
            return back()->with('error', 'Transaksi atau tanggal berakhir tidak valid.');
        }

        $tanggalBaru = Carbon::parse($transaksi->tanggal_berakhir)->addDays(30);
        $transaksi->update(['tanggal_berakhir' => $tanggalBaru]);

        $barang->update(['perpanjangan' => 1]);

        return back()->with('success', 'Penitipan berhasil diperpanjang 30 hari.');
    }

    public function confirmPickup($id)
    {
        $barang = Barang::findOrFail($id);

        if (!in_array($barang->status_barang, ['Available', 'Awaiting Owner Pickup'])) {
            return response()->json([
                'message' => 'This item is not eligible for pickup.'
            ], 422);
        }

        $now = Carbon::now();
        $tanggalBerakhir = optional($barang->transaksiPenitipan)->tanggal_berakhir;

        if (!$tanggalBerakhir) {
            return response()->json([
                'message' => 'Cannot determine end of storage period.'
            ], 422);
        }

        if ($barang->status_barang === 'Available' && $now->lt($tanggalBerakhir)) {
            // Picked up *before* end of storage
            $batasAmbil = $now->copy()->addDays(7);
        } else {
            // Picked up *after* end of storage
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
        $tanggalBerakhir = Carbon::parse($barang->transaksiPenitipan->tanggal_berakhir);
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

}