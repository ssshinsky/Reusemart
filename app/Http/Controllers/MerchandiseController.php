<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

}
