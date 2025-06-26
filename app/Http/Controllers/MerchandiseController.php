<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use App\Models\Merchandise;
use App\Models\TransaksiMerchandise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class MerchandiseController extends Controller
{
    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Menampilkan daftar semua merchandise
    public function index()
    {
        $this->ensureAdmin();
       
        $merches = Merchandise::all();
        return view('Admin.Merchandise.merchandise', compact('merches'));
    }

    // Menampilkan merchandise berdasarkan ID
    public function show($id)
    {
        $merchandise = Merchandise::find($id);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }
        return response()->json($merchandise);
    }

    // Menambahkan merchandise baru
    public function store(Request $request)
    {
        $this->ensureAdmin();
       
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'nama_merch' => 'required|string',
            'poin' => 'required|integer',
            'stok' => 'required|integer',
            'gambar_merch' => 'required|string', // Asumsi gambar adalah URL atau path
        ]);

        Merchandise::create([
            'id_pegawai' => $request->id_pegawai,
            'nama_merch' => $request->nama_merch,
            'poin' => $request->poin,
            'stok' => $request->stok,
            'gambar_merch' => $request->gambar_merch,
        ]);
        return response()->json($merchandise, 201);
    }

    // Mengupdate merchandise berdasarkan ID
    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
       
        $merchandise = Merchandise::find($id);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }

        $request->validate([
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
            'nama_merch' => 'nullable|string',
            'poin' => 'nullable|integer',
            'stok' => 'nullable|integer',
            'gambar_merch' => 'nullable|string',
        ]);

        $merchandise->update([
            'id_pegawai'   => $request->id_pegawai ?? $merchandise->id_pegawai,
            'nama_merch'   => $request->nama_merch ?? $merchandise->nama_merch,
            'poin'         => $request->poin ?? $merchandise->poin,
            'stok'         => $request->stok ?? $merchandise->stok,
            'gambar_merch' => $request->gambar_merch ?? $merchandise->gambar_merch,
        ]);

        return response()->json($merchandise);
    }
    
    public function search(Request $request)
    {
        $this->ensureAdmin();
       
        if (!$request->ajax()) {
            return response('', 204);
        }

        $keyword = $request->query('q');

        $merches = Merchandise::with(['addedBy', 'modifiedBy'])
            ->where('nama_merch', 'like', "%$keyword%")
            ->orWhere('poin', 'like', "%$keyword%")
            ->orWhere('stok', 'like', "%$keyword%")
            ->get();

        $html = '';

        foreach ($merches as $merch) {
            $statusText = $merch->stok > 0
                ? '<span style="color: green; font-weight: bold;">🟢 Available</span>'
                : '<span style="color: #E53E3E; font-weight: bold;">🔴 Out of Stock</span>';

            $html .= '
            <tr>
                <td class="center">'.$merch->id_merchandise.'</td>
                <td>'.$merch->nama_merch.'</td>
                <td class="center">'.$merch->poin.'</td>
                <td class="center">'.$merch->stok.'</td>
                <td class="center">'.$statusText.'</td>
                <td>'.($merch->addedBy->nama_pegawai ?? '-').'</td>
                <td>'.($merch->modifiedBy->nama_pegawai ?? '-').'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220); border: none;">
                    <a href="'.route('admin.merchandise.edit', $merch->id_merchandise).'" class="edit-btn">✏️</a>
                </td>
            </tr>';
        }

        if ($merches->isEmpty()) {
            $html = '<tr><td colspan="8" class="center">Merchandise not found.</td></tr>';
        }

        return response($html);
    }
    
    public function indexApi() 
    {
        $merchandise = Merchandise::where('stok', '>', 0)->get();

        $formattedMerchandise = $merchandise->map(function($merch) {
            // >>> Perubahan di sini: Hanya kembalikan nama file gambar <<<
            // Flutter akan menggabungkan dengan base URL storage
            return [
                'id_merchandise' => $merch->id_merchandise,
                'id_pegawai' => $merch->id_pegawai, // Pastikan id_pegawai juga dikirim
                'nama_merch' => $merch->nama_merch,
                'poin' => $merch->poin,
                'stok' => $merch->stok,
                'gambar_merch' => $merch->gambar_merch, // Mengirimkan nama file gambar saja
            ];
        });

        return response()->json([
            'success' => true, // Tambahkan 'success' key
            'message' => 'Daftar merchandise berhasil diambil.',
            'data' => $formattedMerchandise
        ], 200);
    }

    public function showApi($id)
    {
        $merchandise = Merchandise::find($id);

        if (!$merchandise || $merchandise->stok <= 0) {
            return response()->json(['success' => false, 'message' => 'Merchandise not found or out of stock.'], 404); // Tambahkan 'success' key
        }

        // >>> Perubahan di sini: Hanya kembalikan nama file gambar <<<
        $formattedMerch = [
            'id_merchandise' => $merchandise->id_merchandise,
            'id_pegawai' => $merchandise->id_pegawai, // Pastikan id_pegawai juga dikirim
            'nama_merch' => $merchandise->nama_merch,
            'poin' => $merchandise->poin,
            'stok' => $merchandise->stok,
            'gambar_merch' => $merchandise->gambar_merch, // Mengirimkan nama file gambar saja
        ];

        return response()->json([
            'success' => true, // Tambahkan 'success' key
            'message' => 'Detail merchandise berhasil diambil.',
            'data' => $formattedMerch
        ], 200);
    }

    public function claimMerchandise(Request $request)
    {
        $request->validate([
            'merchandise_id' => 'required|exists:merchandise,id_merchandise',
            'pembeli_id' => 'required|exists:pembeli,id_pembeli',
        ]);

        $merchandiseId = $request->merchandise_id;
        $pembeliId = $request->pembeli_id;
        $jumlahKlaim = 1;

        $pembeli = Pembeli::find($pembeliId);
        if (!$pembeli) {
            return response()->json(['success' => false, 'message' => 'Data pembeli tidak ditemukan.'], 404);
        }

        $merchandise = Merchandise::find($merchandiseId);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found.'], 404);
        }

        // Fungsi ini membutuhkan "use Illuminate\Support\Facades\DB;"
        DB::beginTransaction();

        try {
            if ($merchandise->stok < $jumlahKlaim) {
                DB::rollBack();
                return response()->json(['message' => 'Stok merchandise tidak mencukupi.'], 400);
            }
            
            // ================== PERUBAHAN FINAL DI SINI ==================
            // Menggunakan nama kolom 'poin_pembeli' sesuai dengan struktur database Anda
            if ($pembeli->poin_pembeli < ($merchandise->poin * $jumlahKlaim)) {
                DB::rollBack();
                return response()->json(['message' => 'Poin tidak mencukupi untuk klaim merchandise ini.'], 400);
            }

            // Menggunakan nama kolom 'poin_pembeli'
            $pembeli->poin_pembeli -= ($merchandise->poin * $jumlahKlaim);
            // =============================================================
            $pembeli->save();

            $merchandise->stok -= $jumlahKlaim;
            $merchandise->save();
            
            // Fungsi ini membutuhkan "use App\Models\TransaksiMerchandise;"
            // dan "use Carbon\Carbon;"
            TransaksiMerchandise::create([
                'id_merchandise' => $merchandise->id_merchandise,
                'id_pembeli' => $pembeli->id_pembeli,
                'jumlah' => $jumlahKlaim,
                'total_poin_penukaran' => ($merchandise->poin * $jumlahKlaim),
                'tanggal_klaim' => Carbon::now(),
                'status_transaksi' => 'belum diambil',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchandise berhasil diklaim!',
                // Mengirimkan nilai poin terbaru dari kolom yang benar
                'current_poin_pembeli' => $pembeli->poin_pembeli,
                'merchandise_claimed' => $merchandise->nama_merch,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal klaim merchandise: ' . $e->getMessage()], 500);
        }
    }
}
