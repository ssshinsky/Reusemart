<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OrganisasiController extends Controller
{
    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    // Menampilkan daftar semua organisasi
    public function index()
    {
        $this->ensureAdmin();
        
        $organisasis = Organisasi::all();
        return view('Admin.Organisasi.organisasi', compact('organisasis'));
    }
    
    public function create()
    {
        $this->ensureAdmin();
        
        return view('Admin.Organisasi.add_organisasi');
    }

    // Menampilkan organisasi berdasarkan ID
    public function show($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi not found'], 404);
        }
        return response()->json($organisasi);
    }

    // Menambahkan organisasi baru
    public function store(Request $request)
    {
        // Admin
        $isAdmin = Auth::guard('pegawai')->check() && Auth::guard('pegawai')->user()->id_role == 2;

        $validated = $request->validate([
            'nama_organisasi' => 'required|string',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
            'email_organisasi' => 'required|email|unique:organisasi,email_organisasi',
            'password' => 'required|string|min:8',
        ]);

        $organisasi = Organisasi::create([
            'nama_organisasi' => $validated['nama_organisasi'],
            'alamat' => $validated['alamat'],
            'kontak' => $validated['kontak'],
            'email_organisasi' => $validated['email_organisasi'],
            'password' => Hash::make($validated['password']),
            'status_organisasi' => 'Active',
            'profil_pict' => $request->profil_pict ?? 'default.png',
        ]);

        if ($isAdmin) {
            return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil ditambahkan.');
        }

        // UMUM
        Auth::guard('organisasi')->login($organisasi);
        session([
            'user' => ['id' => $organisasi->id_organisasi, 'nama' => $organisasi->nama_organisasi],
            'role' => 'organisasi'
        ]);

        return redirect()->route('organisasi.store');
    }


    public function edit($id)
    {
        $this->ensureAdmin();

        $organisasi = Organisasi::findOrFail($id);
        return view('Admin.Organisasi.edit_organisasi', compact('organisasi'));
    }

    // Mengupdate organisasi berdasarkan ID
    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
       
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi not found'], 404);
        }

        $request->validate([
            'nama_organisasi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string',
            'email_organisasi' => 'nullable|email|unique:organisasi,email_organisasi,' . $organisasi->id_organisasi . ',id_organisasi',
            'password' => 'nullable|string|min:6',
        ]);

        $organisasi->update([
            'nama_organisasi' => $request->nama_organisasi ?? $organisasi->nama_organisasi,
            'alamat' => $request->alamat ?? $organisasi->alamat,
            'kontak' => $request->kontak ?? $organisasi->kontak,
            'email_organisasi' => $request->email_organisasi ?? $organisasi->email_organisasi,
            'password' => $request->password ? Hash::make($request->password) : $organisasi->password,
        ]);

        return redirect()->route('admin.organisasi.index');

    }

    public function destroy($id)
    {
        $this->ensureAdmin();
       
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->delete();

        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $this->ensureAdmin();
       
        if (!$request->ajax()) {
            return response('', 204);
        }

        $query = $request->query('q');

        $organisasis = Organisasi::where('nama_organisasi', 'like', "%$query%")
            ->orWhere('email_organisasi', 'like', "%$query%")
            ->orWhere('kontak', 'like', "%$query%")
            ->orWhere('alamat', 'like', "%$query%")
            ->get();

        $html = '';

        foreach ($organisasis as $organisasi) {
            $isNonActive = $organisasi->status_organisasi !== 'Active';
            $html .= '
            <tr>
                <td class="center">'.$organisasi->id_organisasi.'</td>
                <td'.($isNonActive ? ' style="color: #E53E3E; font-weight: bold;"' : '').'>'.$organisasi->nama_organisasi.'</td>
                <td>'.$organisasi->email_organisasi.'</td>
                <td>'.$organisasi->kontak.'</td>
                <td>'.$organisasi->alamat.'</td>
                <td class="center">'.ucwords($organisasi->status_organisasi).'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route('admin.organisasi.edit', $organisasi->id_organisasi).'" class="edit-btn">✏️</a>';

            if ($organisasi->status_organisasi === 'Active') {
                $html .= '
                    <form action="'.route('admin.organisasi.deactivate', $organisasi->id_organisasi).'" method="POST" class="form-nonaktif" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Nonaktifkan">🛑</button>
                    </form>';
            } else {
                $html .= '
                    <form action="'.route('admin.organisasi.reactivate', $organisasi->id_organisasi).'" method="POST" class="form-reactivate" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Aktifkan kembali">♻️</button>
                    </form>';
            }

            $formDelete = '
                <form action="'.route('admin.organisasi.destroy', $organisasi->id_organisasi).'" method="POST" class="form-delete" style="display:inline;">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="delete-btn" title="Hapus">🗑️</button>
                </form>';
            $html .= $formDelete;

            $html .= '</td></tr>';
        }

        if ($organisasis->isEmpty()) {
            $html = '<tr><td colspan="7" class="center">Organization not found.</td></tr>';
        }

        return response($html);
    }

    public function deactivate($id)
    {
        $this->ensureAdmin();
       
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->update(['status_organisasi' => 'Non Active']);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi dinonaktifkan.');
    }

    public function reactivate($id)
    {
        $this->ensureAdmin();
       
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->update(['status_organisasi' => 'Active']);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi diaktifkan kembali.');
    }
}
