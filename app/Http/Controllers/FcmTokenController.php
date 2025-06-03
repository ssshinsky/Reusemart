<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'role' => 'required|in:pembeli,penitip,pegawai,organisasi',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tentukan model berdasarkan role
        $modelMap = [
            'pembeli' => 'App\Models\Pembeli',
            'penitip' => 'App\Models\Penitip',
            'pegawai' => 'App\Models\Pegawai',
            'organisasi' => 'App\Models\Organisasi',
        ];

        $modelClass = $modelMap[$request->role] ?? null;
        if (!$modelClass) {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        // Cari pengguna berdasarkan role dan user_id
        $userModel = $modelClass::find($user->id);
        if (!$userModel) {
            return response()->json(['message' => 'User not found for the specified role'], 404);
        }

        // Simpan token FCM
        FcmToken::updateOrCreate(
            [
                'tokenable_id' => $user->id,
                'tokenable_type' => $modelClass,
                'token' => $request->token,
            ],
            ['token' => $request->token]
        );

        return response()->json(['message' => 'Token saved successfully'], 200);
    }
}