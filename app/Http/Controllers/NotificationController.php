<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'nullable|integer',
            'role' => 'nullable|in:pembeli,penitip,pegawai',
            'type' => 'required|in:barang_laku,jadwal_pengiriman,jadwal_pengambilan,barang_dikirim', // Tambahkan tipe notifikasi
            'id' => 'nullable|string', // ID terkait (barang, transaksi, atau jadwal)
        ]);

        $title = $request->title;
        $body = $request->body;
        $userId = $request->user_id;
        $role = $request->role;
        $type = $request->type;
        $id = $request->id;

        // Mapping role ke model
        $modelMap = [
            'pembeli' => 'App\Models\Pembeli',
            'penitip' => 'App\Models\Penitip',
            'pegawai' => 'App\Models\Pegawai',
        ];

        // Ambil token berdasarkan user_id atau role
        $query = FcmToken::query();
        if ($userId && $role) {
            $query->where('tokenable_id', $userId)->where('tokenable_type', $modelMap[$role]);
        } elseif ($role) {
            $query->where('tokenable_type', $modelMap[$role]);
        } else {
            return response()->json(['message' => 'Either user_id with role or role is required'], 400);
        }

        $tokens = $query->pluck('token')->toArray();

        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY');

        $data = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => [
                'type' => $type,
                'id' => $id ?? '', // ID terkait (barang, transaksi, atau jadwal)
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post($fcmUrl, $data);

            // Tangani token yang tidak valid
            if ($response->successful()) {
                return response()->json(['message' => 'Notification sent successfully'], 200);
            } else {
                $responseData = $response->json();
                if (isset($responseData['results'])) {
                    foreach ($responseData['results'] as $index => $result) {
                        if (isset($result['error']) && in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                            // Hapus token yang tidak valid
                            FcmToken::where('token', $tokens[$index])->delete();
                        }
                    }
                }
                return response()->json(['message' => 'Failed to send notification: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}