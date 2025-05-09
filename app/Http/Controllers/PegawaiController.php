<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    // ============ VIEW SECTION ============

    // Halaman daftar pegawai
    public function indexView(Request $request)
    {
        $search = $request->query('search');

        $pegawais = Pegawai::with('role')
            ->when($search, function ($query, $search) {
                $query->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('email_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nomor_telepon', 'like', '%' . $search . '%');
            })
            ->get();

        return view('Admin.Employees.employees', compact('pegawais'));
    }

    // Halaman form tambah pegawai
    public function create()
    {
        $roles = Role::all();
        return view('Admin.Employees.add_employee', compact('roles'));
    }

    // ============ API SECTION ============

    // Ambil semua pegawai (JSON)
    public function index()
    {
        $pegawais = Pegawai::with('role')->get();
        return response()->json($pegawais);
    }

    // Ambil satu pegawai by ID
    public function show($id)
    {
        $pegawai = Pegawai::with('role')->find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found'], 404);
        }
        return response()->json($pegawai);
    }

    public function search(Request $request)
    {
        if (!$request->ajax()) {
            return response('', 204);
        }
    
        $search = $request->query('q');
    
        $pegawais = Pegawai::with('role')
            ->when($search, function ($query, $search) {
                $query->where('nama_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('email_pegawai', 'like', '%' . $search . '%')
                    ->orWhere('nomor_telepon', 'like', '%' . $search . '%');
            })
            ->get();
    
        $html = '';
    
        foreach ($pegawais as $pegawai) {
            $html .= '
            <tr>
                <td class="center">'.$pegawai->id_pegawai.'</td>
                <td>'.($pegawai->id_role == 3 ? 'CS' : ($pegawai->role->nama_role ?? '-')).'</td>
                <td style="'.(!$pegawai->is_active ? 'color: #E53E3E; font-weight: bold;' : '').'">'.$pegawai->nama_pegawai.'</td>
                <td>'.$pegawai->email_pegawai.'</td>
                <td class="nowrap center">Rp '.number_format($pegawai->gaji_pegawai, 0, ',', '.').'</td>
                <td class="nowrap center">'.\Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d-m-Y').'</td>
                <td class="center">'.$pegawai->nomor_telepon.'</td>
                <td>'.$pegawai->alamat_pegawai.'</td>
                <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                    <a href="'.route('admin.employees.edit', $pegawai->id_pegawai).'" class="edit-btn">✏️</a>';
    
            if ($pegawai->is_active) {
                $html .= '
                    <form action="'.route('admin.employees.deactivate', $pegawai->id_pegawai).'" method="POST" class="form-nonaktif" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Deactivate Pegawai">🛑</button>
                    </form>';
            } else {
                $html .= '
                    <form action="'.route('admin.employees.reactivate', $pegawai->id_pegawai).'" method="POST" class="form-reactivate" style="display:inline;">
                        '.csrf_field().method_field('PUT').'
                        <button type="submit" class="redeactivate-btn" title="Reactivate Pegawai">♻️</button>
                    </form>';
            }
    
            $html .= '</td></tr>';
        }
    
        if (count($pegawais) === 0) {
            $html = '<tr><td colspan="9" class="center">Employee Not Found.</td></tr>';
        }
    
        return response($html);
    }

    // Simpan pegawai baru
    public function store(Request $request)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id_role',
            'nama_pegawai' => 'required|string',
            'alamat_pegawai' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'nomor_telepon' => 'required|string',
            'gaji_pegawai' => 'required',
            'email_pegawai' => 'required|email|unique:pegawai,email_pegawai',
            'password' => 'required|string|min:8',
            'profil_pict' => 'nullable|integer',
        ]);

        Pegawai::create([
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'alamat_pegawai' => $request->alamat_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon,
            'gaji_pegawai' => str_replace(['Rp', '.', ' '], '', $request->gaji_pegawai),
            'email_pegawai' => $request->email_pegawai,
            'password' => Hash::make($request->password),
            'profil_pict' => $request->profil_pict,
        ]);

        return redirect()->route('admin.employees.index')->with([
            'success' => 'Employee '.$request->nama_pegawai.' has been successfully added.'
        ]);        
    }

    // Update pegawai
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return redirect()->back()->withErrors(['message' => 'Pegawai not found']);
        }

        // Format salary ke angka
        $request->merge([
            'gaji_pegawai' => str_replace(['Rp', '.', ' '], '', $request->gaji_pegawai)
        ]);

        $request->validate([
            'id_role' => 'nullable|exists:role,id_role',
            'nama_pegawai' => 'nullable|string',
            'alamat_pegawai' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'nomor_telepon' => 'nullable|string',
            'gaji_pegawai' => 'nullable|numeric',
            'email_pegawai' => 'nullable|email|unique:pegawai,email_pegawai,' . $id . ',id_pegawai',
            'password' => 'nullable|string|min:6',
            'profil_pict' => 'nullable|integer',
        ]);

        $pegawai->update([
            'id_role' => $request->id_role ?? $pegawai->id_role,
            'nama_pegawai' => $request->nama_pegawai ?? $pegawai->nama_pegawai,
            'alamat_pegawai' => $request->alamat_pegawai ?? $pegawai->alamat_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir ?? $pegawai->tanggal_lahir,
            'nomor_telepon' => $request->nomor_telepon ?? $pegawai->nomor_telepon,
            'gaji_pegawai' => $request->gaji_pegawai ?? $pegawai->gaji_pegawai,
            'email_pegawai' => $request->email_pegawai ?? $pegawai->email_pegawai,
            'password' => $request->password ? Hash::make($request->password) : $pegawai->password,
            'profil_pict' => $request->profil_pict ?? $pegawai->profil_pict,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employee has been updated successfully.');
    }


    public function edit($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return redirect()->route('admin.employees.index')->with('error', 'Employee not found.');
        }

        $roles = Role::all();
        return view('Admin.Employees.edit_employee', compact('pegawai', 'roles'));
    }

    //reset password
    public function resetPassword($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found.'], 404);
        }

        $tanggal = \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('dmY'); // contoh: 15051990
        $pegawai->update([
            'password' => Hash::make($tanggal)
        ]);

        return response()->json(['message' => "Password di-reset ke tanggal lahir: $tanggal"]);
    }

    public function deactivate($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->is_active = 0;
        $pegawai->save();

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil dinonaktifkan.');
    }

    public function reactivate($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->is_active = 1;
        $pegawai->save();

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil direaktivasi.');
    }


    // Hapus pegawai
    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai not found'], 404);
        }

        $pegawai->delete();
        return response()->json(['message' => 'Pegawai deleted successfully']);
    }

    // Login pegawai (API)
    public function login(Request $request)
    {
        $request->validate([
            'email_pegawai' => 'required|email',
            'password' => 'required|string',
        ]);

        $pegawai = Pegawai::where('email_pegawai', $request->email_pegawai)->first();

        if (!$pegawai || !Hash::check($request->password, $pegawai->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $pegawai->createToken('Pegawai Token')->plainTextToken;

        return response()->json([
            'pegawai' => $pegawai,
            'token' => $token
        ]);
    }

    // Logout pegawai (API)
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json(['message' => 'Not logged in'], 401);
    }

    protected function authorizeCS()
    {
        if (auth()->user()->id_role != 3) {
            throw new \Illuminate\Auth\Access\AuthorizationException();
        }
    }
}
