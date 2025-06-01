<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli;

class PembeliController extends Controller
{

    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }
    
    public function profile()
    {
        if (!Auth::guard('pembeli')->check()) {
            return redirect('/')->with('error', 'Anda belum login sebagai pembeli.');
        }

        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.profile', compact('pembeli'));
    }

    // Web: Menampilkan halaman daftar pembeli (admin panel)
    public function index()
    {
        $this->ensureAdmin();
        
        $pembelis = Pembeli::with('alamatDefault')->get();
        return view('Admin.Pembeli.pembeli', compact('pembelis'));
    }

    public function purchase()
    {
        return view('pembeli.purchase');
    }


    // Web: Halaman form tambah
    public function create()
    {
        $this->ensureAdmin();
        
        return view('Admin.Pembeli.add_pembeli');
    }

    // Web: Menyimpan data pembeli baru
    public function store(Request $request)
    {
        // Cek apakah ini request dari admin
        $isAdmin = Auth::guard('pegawai')->check() && Auth::guard('pegawai')->user()->id_role == 2;

        $validated = $request->validate([
            'nama_pembeli' => 'required|string',
            'email_pembeli' => 'required|email|unique:pembeli,email_pembeli',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $pembeli = Pembeli::create([
            'nama_pembeli' => $validated['nama_pembeli'],
            'email_pembeli' => $validated['email_pembeli'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'password' => Hash::make($validated['password']),
            'profil_pict' => $request->profil_pict ?? 'default.png',
            'status_pembeli' => 'Active',
            'poin_pembeli' => 0,
        ]);

        if ($isAdmin) {
            return redirect()->route('admin.pembeli.index')->with('success', 'Pembeli berhasil ditambahkan');
        }

        // Auto login jika dari register umum
        Auth::guard('pembeli')->login($pembeli);
        session([
            'user' => ['id' => $pembeli->id_pembeli, 'nama' => $pembeli->nama_pembeli],
            'role' => 'pembeli'
        ]);

        return redirect()->route('pembeli.profile');
    }


    // Web: Form edit pembeli
    public function edit($id)
    {
        $this->ensureAdmin();
        
        $pembeli = Pembeli::findOrFail($id);
        return view('Admin.Pembeli.edit_pembeli', compact('pembeli'));
    }

    // Web: Update pembeli
    public function updateProfile(Request $request, $id)
    {
        $this->ensureAdmin();
        
        $pembeli = Pembeli::findOrFail($id);

        $request->validate([
            'nama_pembeli' => 'required|string',
            'email_pembeli' => 'required|email|unique:pembeli,email_pembeli,' . $id . ',id_pembeli',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
        ]);

        $pembeli->update($request->only(['nama_pembeli', 'email_pembeli', 'tanggal_lahir', 'nomor_telepon']));

        return redirect()->route('admin.pembeli.index')->with('success', 'Data pembeli diperbarui');
    }

    // Web: Nonaktifkan akun
    public function deactivate($id)
    {
        $this->ensureAdmin();
        
        $pembeli = Pembeli::findOrFail($id);
        $pembeli->update(['status_pembeli' => 'Non Active']);

        return redirect()->route('admin.pembeli.index')->with('success', 'Akun pembeli dinonaktifkan');
    }

    // Web: Aktifkan kembali akun
    public function reactivate($id)
    {
        $this->ensureAdmin();
        
        $pembeli = Pembeli::findOrFail($id);
        $pembeli->update(['status_pembeli' => 'Active']);

        return redirect()->route('admin.pembeli.index')->with('success', 'Akun pembeli diaktifkan kembali');
    }

    public function search(Request $request)
    {
        $this->ensureAdmin();
        
        if (!$request->ajax()) {
            return response('', 204);
        }

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

    // ====== API ======
    public function apiIndex()
    {
        return response()->json(Pembeli::all());
    }

    public function apiShow($id)
    {
        $pembeli = Pembeli::find($id);
        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli not found'], 404);
        }
        return response()->json($pembeli);
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    public function apiUpdate(Request $request, $id)
    {
        return $this->update($request, $id);
    }

}
