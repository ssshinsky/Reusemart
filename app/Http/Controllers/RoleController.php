<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Menampilkan daftar semua role
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // Menampilkan role berdasarkan ID
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json($role);
    }

    // Menambahkan role baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:255',
        ]);

        $role = Role::create([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json($role, 201);
    }

    // Mengupdate role berdasarkan ID
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $request->validate([
            'nama_role' => 'nullable|string|max:255',
        ]);

        $role->update([
            'nama_role' => $request->nama_role ?? $role->nama_role,
        ]);

        return response()->json($role);
    }

    // Menghapus role berdasarkan ID
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
