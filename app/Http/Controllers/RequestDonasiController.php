<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestDonasiController extends Controller
{
    // Menampilkan semua request milik organisasi yang sedang login
    public function index()
    {
        $idOrganisasi = Auth::guard('organisasi')->id();

        $requests = RequestDonasi::where('id_organisasi', $idOrganisasi)
            ->orderByDesc('created_at')
            ->get();

        return view('Organisasi.organisasi', compact('requests'));
    }

    // Menyimpan request donasi baru (khusus organisasi login)
    public function store(Request $request)
    {
        $request->validate([
            'request' => 'required|string|max:255',
        ]);

        $idOrganisasi = Auth::guard('organisasi')->id();

        RequestDonasi::create([
            'id_organisasi' => $idOrganisasi,
            'id_pegawai' => null, // belum diproses oleh pegawai
            'request' => $request->input('request'),
            'status_request' => 'Pending',
        ]);
        return redirect()->route('organisasi.index')->with('success', 'Donation request submitted.');
    }
    
    public function create()
    {
        return view('Organisasi.add_request');
    }


    // Menampilkan form edit
    public function edit($id)
    {
        $idOrganisasi = Auth::guard('organisasi')->id();
        $requestDonasi = RequestDonasi::where('id_organisasi', $idOrganisasi)->findOrFail($id);

        return view('Organisasi.edit_request', compact('requestDonasi'));
    }

    // Mengubah request donasi (hanya deskripsi oleh organisasi)
    public function update(Request $request, $id)
    {
        $request->validate([
            'request' => 'required|string|max:255',
        ]);

        $idOrganisasi = Auth::guard('organisasi')->id();
        $requestDonasi = RequestDonasi::where('id_organisasi', $idOrganisasi)->findOrFail($id);

        // Status tidak boleh diubah oleh organisasi
        $requestDonasi->update([
            'request' => $request->input('request'),
        ]);

        return redirect()->route('organisasi.index')->with('success', 'Request updated successfully.');
    }

    // Menghapus request donasi (hanya milik sendiri)
    public function destroy($id)
    {
        $idOrganisasi = Auth::guard('organisasi')->id();
        $requestDonasi = RequestDonasi::where('id_organisasi', $idOrganisasi)->findOrFail($id);

        $requestDonasi->delete();
        return redirect()->back()->with('success', 'Request deleted.');
    }

    public function search(Request $request)
    {
        $organisasi = Auth::guard('organisasi')->user();
        if (!$organisasi || !$request->ajax()) {
            return response('', 204);
        }

        $query = strtolower(trim($request->query('q')));

        $requests = RequestDonasi::where('id_organisasi', $organisasi->id_organisasi)
            ->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(request) LIKE ?', ["%{$query}%"])
                ->orWhereRaw('LOWER(status_request) LIKE ?', ["%{$query}%"]);
            })
            ->orderByDesc('created_at')
            ->get();

        if ($requests->isEmpty()) {
            return response('<tr><td colspan="4" class="center">No request found.</td></tr>');
        }

        $html = '';
        foreach ($requests as $key => $request) {
            $html .= '
                <tr>
                    <td class="center">'.($key + 1).'</td>
                    <td>'.$request->request.'</td>
                    <td class="center">'.$request->status_request.'</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="'.route('organisasi.request.edit', $request->id_request).'" class="edit-btn">✏️</a>
                        <form action="'.route('organisasi.request.destroy', $request->id_request).'" method="POST" class="form-nonaktif" style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="delete-request-btn" title="Delete">🗑️</button>
                        </form>
                    </td>
                </tr>';
        }
        return response($html);
    }
}
