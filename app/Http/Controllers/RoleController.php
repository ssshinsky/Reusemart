<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    private function ensureAdmin()
    {
        if (!Auth::guard('pegawai')->check() || Auth::guard('pegawai')->user()->id_role != 2) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function search(Request $request)
    {
        $this->ensureAdmin();

        if (!$request->ajax()) {
            return response('', 204);
        }

        $search = $request->query('q');

        $roles = Role::where('nama_role', 'like', '%' . $search . '%')->get();

        $html = '';
        foreach ($roles as $role) {
            $html .= '
                <tr>
                    <td class="center">' . $role->id_role . '</td>
                    <td style="' . (!$role->is_active ? 'color: #E53E3E; font-weight: bold;' : '') . '">' . $role->nama_role . '</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="' . route('admin.roles.edit', $role->id_role) . '" class="edit-btn">✏️</a>';

            if ($role->is_active) {
                $html .= '
                        <form action="' . route('admin.roles.deactivate', $role->id_role) . '" method="POST" class="form-nonaktif" style="display:inline;">' . csrf_field() . method_field('PUT') . '
                            <button type="submit" class="redeactivate-btn" title="Deactivate Role">🛑</button>
                        </form>';
            } else {
                $html .= '
                        <form action="' . route('admin.roles.reactivate', $role->id_role) . '" method="POST" class="form-reactivate" style="display:inline;">' . csrf_field() . method_field('PUT') . '
                            <button type="submit" class="redeactivate-btn" title="Reactivate Role">♻️</button>
                        </form>';
            }

            $html .= '</td></tr>';
        }

        if ($roles->isEmpty()) {
            $html = '<tr><td colspan="3" class="center">No roles found.</td></tr>';
        }

        return response($html);
    }

    // Tampilkan semua role (View)
    public function index()
    {
        $this->ensureAdmin();

        $roles = Role::all();
        return view('Admin.Roles.roles', compact('roles'));
    }

    // Tampilkan form tambah role
    public function create()
    {
        $this->ensureAdmin();

        return view('Admin.Roles.add_role');
    }

    // Simpan role baru
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'nama_role' => 'required|string|max:255',
        ]);

        Role::create([
            'nama_role' => $request->nama_role,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    // Tampilkan form edit role
    public function edit($id)
    {
        $this->ensureAdmin();

        $role = Role::findOrFail($id);
        return view('Admin.Roles.edit_role', compact('role'));
    }

    // Update role
    public function update(Request $request, $id)
    {
        $this->ensureAdmin();

        $role = Role::findOrFail($id);

        $request->validate([
            'nama_role' => 'required|string|max:255',
        ]);

        $role->update([
            'nama_role' => $request->nama_role,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diupdate.');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();

        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }

    public function deactivate($id)
    {
        $this->ensureAdmin();

        $role = Role::findOrFail($id);
        $role->is_active = false;
        $role->save();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dinonaktifkan.');
    }

    public function reactivate($id)
    {
        $this->ensureAdmin();

        $role = Role::findOrFail($id);
        $role->is_active = true;
        $role->save();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diaktifkan kembali.');
    }
}
