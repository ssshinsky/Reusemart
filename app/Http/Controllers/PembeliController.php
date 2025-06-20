<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Pembeli;
use App\Models\TransaksiPembelian;
// use App\Http\Controllers\TransaksiPembelianController;

class PembeliController extends Controller
{
    // 📌 Untuk pembeli: Lihat profil sendiri
    public function profile()
    {
        if (!Auth::guard('pembeli')->check()) {
            return redirect('/')->with('error', 'Anda belum login sebagai pembeli.');
        }

        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.profile', compact('pembeli'));
    }

    public function updateProfile(Request $request, $id)
    {
        $pembeli = Pembeli::findOrFail($id);

        $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email',
            'nomor_telepon' => 'required|string',
        ]);

        $pembeli->nama_pembeli = $request->nama;
        $pembeli->email_pembeli = $request->email;
        $pembeli->nomor_telepon = $request->nomor_telepon;

        if ($request->hasFile('profile_pict')) {
            $file = $request->file('profile_pict');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/profil_pembeli');

            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $fileName);
            $pembeli->profil_pict = $fileName;
        }


        $pembeli->save();

        return redirect()->route('pembeli.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // 📌 Untuk admin: Tampilkan semua pembeli
    public function index()
    {
        $pembelis = Pembeli::with('alamatDefault')->get();
        return view('Admin.Pembeli.pembeli', compact('pembelis'));
    }

    public function purchase()
    {
        return view('pembeli.purchase');
    }

    public function create()
    {
        return view('Admin.Pembeli.add_pembeli');
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::guard('pegawai')->check() && Auth::guard('pegawai')->user()->id_role == 2;

        $validated = $request->validate([
            'nama_pembeli'     => 'required|string',
            'email_pembeli'    => 'required|email|unique:pembeli,email_pembeli',
            'tanggal_lahir'    => 'required|date',
            'nomor_telepon'    => 'required|string',
            'password'         => 'required|string|min:6',
        ]);

        $pembeli = Pembeli::create([
            'nama_pembeli'     => $validated['nama_pembeli'],
            'email_pembeli'    => $validated['email_pembeli'],
            'tanggal_lahir'    => $validated['tanggal_lahir'],
            'nomor_telepon'    => $validated['nomor_telepon'],
            'password'         => Hash::make($validated['password']),
            'profil_pict'      => $request->profil_pict ?? 'default.png',
            'status_pembeli'   => 'Active',
            'poin_pembeli'     => 0,
        ]);

        if ($isAdmin) {
            return redirect()->route('admin.pembeli.index')->with('success', 'Pembeli berhasil ditambahkan');
        }

        // Auto login setelah register
        Auth::guard('pembeli')->login($pembeli);
        session([
            'user' => ['id' => $pembeli->id_pembeli, 'nama' => $pembeli->nama_pembeli],
            'role' => 'pembeli'
        ]);

        return redirect()->route('pembeli.profile');
    }

    public function edit($id)
    {
        $pembeli = Pembeli::findOrFail($id);
        return view('Admin.Pembeli.edit_pembeli', compact('pembeli'));
    }

    // Web: Update pembeli
    public function update(Request $request, $id)
    {
        // $this->ensureAdmin();
        $pembeli = Pembeli::findOrFail($id);

        $request->validate([
            'nama_pembeli'   => 'required|string',
            'email_pembeli'  => 'required|email|unique:pembeli,email_pembeli,' . $id . ',id_pembeli',
            'tanggal_lahir'  => 'required|date',
            'nomor_telepon'  => 'required|string',
        ]);

        $pembeli->update($request->only(['nama_pembeli', 'email_pembeli', 'tanggal_lahir', 'nomor_telepon']));

        return redirect()->route('admin.pembeli.index')->with('success', 'Data pembeli diperbarui');
    }

    public function deactivate($id)
    {
        $pembeli = Pembeli::findOrFail($id);
        $pembeli->update(['status_pembeli' => 'Non Active']);

        return redirect()->route('admin.pembeli.index')->with('success', 'Akun pembeli dinonaktifkan');
    }

    public function reactivate($id)
    {
        $pembeli = Pembeli::findOrFail($id);
        $pembeli->update(['status_pembeli' => 'Active']);

        return redirect()->route('admin.pembeli.index')->with('success', 'Akun pembeli diaktifkan kembali');
    }

    public function search(Request $request)
    {
        if (!$request->ajax()) return response('', 204);

        $keyword = $request->query('q');

        $pembelis = Pembeli::with('alamatDefault')
            ->where('nama_pembeli', 'like', '%' . $keyword . '%')
            ->orWhere('email_pembeli', 'like', '%' . $keyword . '%')
            ->get();

        $html = '';
        foreach ($pembelis as $pembeli) {
            $status = strtolower(trim($pembeli->status_pembeli));
            $html .= '
            <tr>
                <td class="center">'.$pembeli->id_pembeli.'</td>
                <td>'.$pembeli->nama_pembeli.'</td>
                <td>'.ucwords($pembeli->status_pembeli).'</td>
                <td>'.$pembeli->email_pembeli.'</td>
                <td>'.$pembeli->nomor_telepon.'</td>
                <td class="center">'.$pembeli->poin_pembeli.'</td>
                <td class="center">'.\Carbon\Carbon::parse($pembeli->tanggal_lahir)->format('Y-m-d').'</td>
                <td>'.($pembeli->alamatDefault->alamat_lengkap ?? '-').'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route('admin.pembeli.edit', $pembeli->id_pembeli).'" class="edit-btn">✏️</a>';
            if ($status === 'active') {
                $html .= '
                    <form action="'.route('admin.pembeli.deactivate', $pembeli->id_pembeli).'" method="POST" class="form-nonaktif" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Nonaktifkan">🛑</button>
                    </form>';
            } else {
                $html .= '
                    <form action="'.route('admin.pembeli.reactivate', $pembeli->id_pembeli).'" method="POST" class="form-reactivate" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Aktifkan kembali">♻️</button>
                    </form>';
            }
            $html .= '</td></tr>';
        }

        if ($pembelis->isEmpty()) {
            $html = '<tr><td colspan="9" class="center">Customer not found.</td></tr>';
        }

        return response($html);
    }

    // ===== API =====
    public function apiIndex() { return response()->json(Pembeli::all()); }
    public function apiShow($id)
    {
        $pembeli = Pembeli::find($id);
        return $pembeli ? response()->json($pembeli) : response()->json(['message' => 'Not found'], 404);
    }

    public function apiUpdate(Request $request, $id)
    {
        return $this->update($request, $id);
    }


    // ====== New API Methods ======
    public function getPembeliById($id)
    {
        try {
            Log::debug('Fetching pembeli by ID', ['id_pembeli' => $id]);

            $pembeli = Pembeli::find($id);
            if (!$pembeli) {
                Log::warning('Pembeli not found', ['id_pembeli' => $id]);
                return response()->json(['message' => 'Pembeli not found'], 404);
            }

            return response()->json([
                'id_pembeli' => $pembeli->id_pembeli,
                'nama' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
                'nomor_telepon' => $pembeli->nomor_telepon,
                'tanggal_lahir' => $pembeli->tanggal_lahir ? $pembeli->tanggal_lahir->format('Y-m-d') : null,
                'poin' => $pembeli->poin_pembeli ?? 0,
                'profil_pict' => $pembeli->profil_pict && file_exists(storage_path('app/public/' . $pembeli->profil_pict))
                    ? asset('storage/' . $pembeli->profil_pict)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pembeli by ID', [
                'id_pembeli' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching pembeli'], 500);
        }
    }

    public function getPembeliProfile()
    {
        try {
            Log::debug('Starting getPembeliProfile', ['user_id' => Auth::guard('pembeli')->id()]);
            $pembeli = Auth::guard('pembeli')->user();

            if (!$pembeli) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return response()->json([
                'id_pembeli' => $pembeli->id_pembeli,
                'nama' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
                'nomor_telepon' => $pembeli->nomor_telepon,
                'tanggal_lahir' => $pembeli->tanggal_lahir ? $pembeli->tanggal_lahir->format('Y-m-d') : null,
                'poin' => $pembeli->poin_pembeli ?? 0,
                'profil_pict' => $pembeli->profil_pict && file_exists(storage_path('app/public/' . $pembeli->profil_pict))
                    ? asset('storage/' . $pembeli->profil_pict)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPembeliProfile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching profile'], 500);
        }
    }
    
    public function getPurchaseHistoryById(int $id)
    {
        try {
            Log::debug('Fetching purchase history by ID', ['id_pembeli' => $id]);

            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['message' => 'Invalid ID'], 400);
            }

            $pembeli = Pembeli::find($id);
            if (!$pembeli) {
                Log::warning('Pembeli not found', ['id_pembeli' => $id]);
                return response()->json(['message' => 'Pembeli not found'], 404);
            }

            $history = TransaksiPembelian::with([
                'keranjang.detailKeranjang.itemKeranjang.barang.gambar',
                'alamat'
            ])
            ->whereHas('keranjang.itemKeranjang', function ($query) use ($id) {
                $query->where('id_pembeli', $id);
            })
            ->take(50)
            ->get()
            ->map(function ($transaksi) {
                Log::debug('Processing transaction', ['id_pembelian' => $transaksi->id_pembelian]);
                return [
                    'id_pembelian' => $transaksi->id_pembelian,
                    'tanggal' => $transaksi->created_at ? $transaksi->created_at->format('Y-m-d') : null,
                    'total_harga' => $transaksi->total_harga ?? 0,
                    'status_transaksi' => $transaksi->status_transaksi ?? 'Unknown',
                    'metode_pengiriman' => $transaksi->metode_pengiriman ?? 'Unknown',
                    'alamat' => $transaksi->alamat ? [
                        'id_alamat' => $transaksi->alamat->id_alamat,
                        'alamat_lengkap' => $transaksi->alamat->alamat_lengkap ?? null
                    ] : null,
                    'items' => $transaksi->keranjang->detailKeranjang->map(function ($detail) {
                        $barang = $detail->itemKeranjang->barang;
                        return [
                            'nama_barang' => $barang->nama_barang ?? 'Unknown',
                            'harga_barang' => $barang->harga_barang ?? 0,
                            'rating' => $barang->rating ?? null,
                            'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                : null,
                        ];
                    })->toArray(),
                ];
            });

            Log::info('History fetched', ['id_pembeli' => $id, 'count' => count($history)]);
            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error fetching purchase history by ID', [
                'id_pembeli' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching history'], 500);
        }
    }

    public function getPurchaseHistory()
    {
        try {
            Log::debug('Starting getPurchaseHistory', ['user_id' => Auth::guard('pembeli')->id()]);
            $pembeli = Auth::guard('pembeli')->user();

            if (!$pembeli) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $history = TransaksiPembelian::with([
                'keranjang.detailKeranjang.itemKeranjang.barang.gambar',
                'alamat'
            ])
            ->whereHas('keranjang.itemKeranjang', function ($query) use ($pembeli) {
                $query->where('id_pembeli', $pembeli->id_pembeli);
            })
            ->take(50)
            ->get()
            ->map(function ($transaksi) {
                Log::debug('Processing transaction', ['id_pembelian' => $transaksi->id_pembelian]);
                return [
                    'id_pembelian' => $transaksi->id_pembelian,
                    'tanggal' => $transaksi->created_at ? $transaksi->created_at->format('Y-m-d') : null,
                    'total_harga' => $transaksi->total_harga ?? 0,
                    'status_transaksi' => $transaksi->status_transaksi ?? 'Unknown',
                    'metode_pengiriman' => $transaksi->metode_pengiriman ?? 'Unknown',
                    'alamat' => $transaksi->alamat ? [
                        'id_alamat' => $transaksi->alamat->id_alamat,
                        'alamat_lengkap' => $transaksi->alamat->alamat_lengkap ?? null
                    ] : null,
                    'items' => $transaksi->keranjang->detailKeranjang->map(function ($detail) {
                        $barang = $detail->itemKeranjang->barang;
                        return [
                            'nama_barang' => $barang->nama_barang ?? 'Unknown',
                            'harga_barang' => $barang->harga_barang ?? 0,
                            'rating' => $barang->rating ?? null,
                            'gambar' => $barang->gambar->isNotEmpty() && file_exists(storage_path('app/public/' . $barang->gambar->first()->gambar_barang))
                                ? asset('storage/' . $barang->gambar->first()->gambar_barang)
                                : null,
                        ];
                    })->toArray(),
                ];
            });

            Log::info('History fetched', ['id_pembeli' => $pembeli->id_pembeli, 'count' => count($history)]);
            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error in getPurchaseHistory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error fetching history'], 500);
        }
    }
}
