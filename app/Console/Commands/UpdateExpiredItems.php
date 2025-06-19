<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use Carbon\Carbon;

class UpdateExpiredItems extends Command
{
    protected $signature = 'barang:update-expired';
    protected $description = 'Update barang yang masa penitipannya sudah habis';
    public function handle()
    {
        $expiredItems = Barang::with('transaksiPenitipan')
            ->where('status_barang', 'Available')
            ->get();

        $now = Carbon::now();

        foreach ($expiredItems as $item) {
            $transaksi = $item->transaksiPenitipan;

            if ($transaksi && $now->gt(Carbon::parse($item->tanggal_berakhir))) {
                $item->update([
                    'status_barang' => 'Awaiting Owner Pickup',
                    'batas_pengambilan' => $now->copy()->addDays(7),
                ]);
            }
        }
        $this->info('Checked and updated expired items with pickup deadlines.');
    }
}
