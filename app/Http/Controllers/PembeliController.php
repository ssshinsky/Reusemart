<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli;

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
    public function apiStore(Request $request) { return $this->store($request); }
    public function apiUpdate(Request $request, $id) { return $this->update($request, $id); }
}
