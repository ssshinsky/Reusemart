<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DiskusiProduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Halaman utama admin (produk.blade.php)
    public function index()
    {
        $this->ensureAdmin();
       
        $barangs = Barang::with(['transaksiPenitipan.penitip', 'kategori'])->get();
        return view('Admin.Produk.produk', compact('barangs'));
    }

    // Ini untuk di halaman utama
    public function indexLanding()
    {
        $barangTerbatas = Barang::with('gambar')->take(12)->get();
        return view('welcome', compact('barangTerbatas'));
    }

    // Ini untuk all product
    public function allProduct()
    {
        $produk = Barang::with('gambar')->get();
        return view('Produk.allproduct', compact('produk'));
    }

    // Daftar barang titipan (termasuk pencarian)
    public function itemList(Request $request)
    {
        $query = Barang::with(['transaksiPenitipan.penitip', 'kategori', 'gambar']);

        // Pencarian berdasarkan keyword
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                // Field di tabel barang
                $q->where('kode_barang', 'like', "%{$keyword}%")
                  ->orWhere('nama_barang', 'like', "%{$keyword}%")
                  ->orWhere('status_barang', 'like', "%{$keyword}%")
                  ->orWhere('harga_barang', 'like', "%{$keyword}%")
                  ->orWhere('berat_barang', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi_barang', 'like', "%{$keyword}%")
                  ->orWhere('status_garansi', 'like', "%{$keyword}%")
                  ->orWhere('perpanjangan', 'like', "%{$keyword}%")
                  ->orWhere('tanggal_garansi', 'like', "%{$keyword}%")
                  ->orWhere('tanggal_berakhir', 'like', "%{$keyword}%")
                  // Relasi kategori
                  ->orWhereHas('kategori', function ($q) use ($keyword) {
                      $q->where('nama_kategori', 'like', "%{$keyword}%");
                  })
                  // Relasi transaksiPenitipan dan penitip
                  ->orWhereHas('transaksiPenitipan', function ($q) use ($keyword) {
                      $q->where('tanggal_penitipan', 'like', "%{$keyword}%")
                        ->orWhere('id_qc', 'like', "%{$keyword}%")
                        ->orWhere('id_hunter', 'like', "%{$keyword}%")
                        ->orWhereHas('penitip', function ($q) use ($keyword) {
                            $q->where('nama_penitip', 'like', "%{$keyword}%")
                              ->orWhere('alamat', 'like', "%{$keyword}%");
                        });
                  });
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $tanggal_mulai = $request->input('tanggal_mulai');
            $query->whereHas('transaksiPenitipan', function ($q) use ($tanggal_mulai) {
                $q->where('tanggal_penitipan', '>=', $tanggal_mulai);
            });
        }

        if ($request->filled('tanggal_selesai')) {
            $tanggal_selesai = $request->input('tanggal_selesai');
            $query->where('tanggal_berakhir', '<=', $tanggal_selesai);
        }

        // Menampilkan barang dari yang baru diinput ke yang terlama
        $query->latest(); 

        $barangs = $query->get();
        return view('gudang.item_list', compact('barangs'));
    }

    // Detail barang
    public function itemDetail($id)
    {
        $barang = Barang::with(['transaksiPenitipan.penitip', 'kategori', 'gambar'])->findOrFail($id);
        return view('gudang.item_detail', compact('barang'));
    }
    
    public function search(Request $request)
    {
        $this->ensureAdmin();
       
        if (!$request->ajax()) {
            return response('', 204);
        }

        $keyword = $request->query('q');

        $barangs = Barang::with(['transaksiPenitipan.penitip', 'kategori'])
            ->where(function ($query) use ($keyword) {
                $query->where('nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('status_barang', 'like', "%{$keyword}%");
            })
            ->get();

        $html = '';

        foreach ($barangs as $barang) {
            $kategori = $barang->kategori->nama_kategori ?? '-';
            $penitip = $barang->transaksiPenitipan->penitip->nama_penitip ?? '-';
            $harga = 'Rp ' . number_format($barang->harga_barang, 0, ',', '.');
            $berat = $barang->berat_barang . ' kg';
            $garansi = $barang->status_garansi === 'garansi' ? 'Valid' : 'No';

            // Status Barang Icon
            switch ($barang->status_barang) {
                case 'Sold': $status = '<span title="Sold">💰 Sold</span>'; break;
                case 'Available': $status = '<span title="Available">🟢 Available</span>'; break;
                case 'Returned': $status = '<span title="Returned">♻️ Returned</span>'; break;
                case 'Donated': $status = '<span title="Donated">🎁 Donated</span>'; break;
                case 'Reserved': $status = '<span title="Reserved">🎁 Donated</span>'; break;
                default: $status = '<span>❓ ' . $barang->status_barang . '</span>';
            }

            $html .= '
            <tr>
                <td class="center">'.$barang->id_barang.'</td>
                <td>'.$barang->kode_barang.'</td>
                <td>'.$barang->nama_barang.'</td>
                <td class="center">'.$status.'</td>
                <td class="center">'.$harga.'</td>
                <td>'.$kategori.'</td>
                <td class="center">'.$berat.'</td>
                <td class="center">'.$garansi.'</td>
                <td>'.$penitip.'</td>
                <td>'.$barang->deskripsi_barang.'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route('admin.produk.edit', $barang->id_barang).'" class="edit-btn">✏️</a>
                </td>
            </tr>';
        }

        if ($barangs->isEmpty()) {
            $html = '<tr><td colspan="11" class="center">Product not found.</td></tr>';
        }

        return response($html);
    }

    // Ubah status jadi Donated
    public function deactivate($id)
    {
        $this->ensureAdmin();
       
        $barang = Barang::findOrFail($id);
        $barang->update(['status_barang' => 'Donated']);

        return redirect()->route('admin.produk.index')->with('success', 'Produk ditandai sebagai Donated');
    }

    // Ubah status jadi Available
    public function reactivate($id)
    {
        $this->ensureAdmin();
       
        $barang = Barang::findOrFail($id);
        $barang->update(['status_barang' => 'Available']);

        return redirect()->route('admin.produk.index')->with('success', 'Produk ditandai sebagai Available');
    }

    // Detail barang untuk frontend (umum.show)
    public function show($id)
    {
        try {
            $barang = Barang::with(['kategori', 'gambar'])->findOrFail($id);

            $diskusi = DiskusiProduk::with(['pembeli', 'pegawai'])
                ->where('id_barang', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            $statusGaransi = $barang->status_garansi;
            $garansiBerlaku = false;
            if ($statusGaransi === 'garansi' && $barang->tanggal_garansi) {
                $tanggalGaransi = Carbon::parse($barang->tanggal_garansi);
                $tanggalSekarang = Carbon::now();
                $garansiBerlaku = $tanggalGaransi->gte($tanggalSekarang);
            }

            $diskusi = $diskusi->isEmpty() ? collect([]) : $diskusi;

            return view('umum.show', compact('barang', 'diskusi', 'statusGaransi', 'garansiBerlaku'));
        } catch (\Exception $e) {
            \Log::error('Error in BarangController@show: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            dd($e->getMessage(), $e->getTraceAsString()); // Tampilkan error di browser
        }
    }

    public function store(Request $request)
    {        
        $this->ensureAdmin();

        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_transaksi_penitipan' => 'required|exists:transaksi_penitipan,id_transaksi_penitipan',
            'kode_barang' => 'required|string|max:10',
            'nama_barang' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'berat_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'status_garansi' => 'required|string|max:255',
            'status_barang' => 'required|string|max:255',
            'tanggal_garansi' => 'nullable|date',
        ]);

        $barang = Barang::create($request->all());

        return response()->json($barang, 201);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();

        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_transaksi_penitipan' => 'required|exists:transaksi_penitipan,id_transaksi_penitipan',
            'kode_barang' => 'required|string|max:10',
            'nama_barang' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'berat_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'status_garansi' => 'required|string|max:255',
            'status_barang' => 'required|string|max:255',
            'tanggal_garansi' => 'nullable|date',
        ]);

        $barang->update($request->all());

        return response()->json($barang);
    }

    // ====================== API ======================

    // API untuk list semua barang
    public function apiIndex()
    {
        $barangs = Barang::with(['kategori', 'gambar', 'transaksiPenitipan.penitip'])->get();
        return response()->json($barangs);
    }

    // API untuk detail barang berdasarkan ID
    public function apiShow($id)
    {
        try {
            $barang = Barang::with(['kategori', 'gambar', 'transaksiPenitipan.penitip'])->findOrFail($id);
            return response()->json($barang);
        } catch (\Exception $e) {
            \Log::error('Error in BarangController@apiShow: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }
    }

    public function getKategoriApi()
    {
        $kategori = Kategori::all();
        return response()->json($kategori);
    }
}