<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmNotificationService
{
    /**
     * Kirim notifikasi FCM
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $message
     * @return array
     */
    public function sendNotification($fcmToken, $title, $message)
    {
        // Firebase API URL
        $url = 'https://fcm.googleapis.com/fcm/send';

        // FCM payload
        $data = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $message,
                'sound' => 'default'
            ],
        ];

        // Firebase Server Key (replace with your server key from Firebase Console)
        $serverKey = env('FCM_SERVER_KEY');

        // Send the POST request to FCM
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        return $response->json();  // Optionally return the response for debugging
    }
}
