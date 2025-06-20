<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\FcmToken;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Carbon\Carbon;

class SendTitipReminderNotifications extends Command
{
    protected $signature = 'titip:send-reminders';
    protected $description = 'Send reminders for expiring titip periods';

    public function handle()
    {
        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        $messaging = app('firebase.messaging');

        // H-3
        $expiringSoon = Barang::whereDate('tanggal_berakhir', $threeDaysFromNow)
            ->whereNotNull('tanggal_berakhir')
            ->get();

        foreach ($expiringSoon as $barang) {
            $transaksi = $barang->transaksiPenitipan;
            if ($transaksi) {
                $tokens = FcmToken::where('id_penitip', $transaksi->id_penitip)->pluck('fcm_token');
                foreach ($tokens as $token) {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(Notification::create(
                            'Masa Titip Segera Berakhir',
                            "Masa titip barang Anda akan berakhir dalam 3 hari pada {$barang->tanggal_berakhir->format('d M Y')}"
                        ));
                    $messaging->send($message);
                }
            }
        }

        // Hari H
        $expiringToday = Barang::whereDate('tanggal_berakhir', $today)
            ->whereNotNull('tanggal_berakhir')
            ->get();

        foreach ($expiringToday as $barang) {
            $transaksi = $barang->transaksiPenitipan;
            if ($transaksi) {
                $tokens = FcmToken::where('id_penitip', $transaksi->id_penitip)->pluck('fcm_token');
                foreach ($tokens as $token) {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(Notification::create(
                            'Masa Titip Berakhir Hari Ini',
                            "Masa titip barang Anda berakhir hari ini, {$barang->tanggal_berakhir->format('d M Y')}"
                        ));
                    $messaging->send($message);
                }
            }
        }

        $this->info('Titip reminders sent successfully!');
    }
}