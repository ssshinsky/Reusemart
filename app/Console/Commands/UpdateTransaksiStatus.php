<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransaksiPembelian;
use Carbon\Carbon;

class UpdateTransaksiStatus extends Command
{
    protected $signature = 'transaksi:update-status';
    protected $description = 'Update otomatis status transaksi ke In Delivery atau Donated';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // IN DELIVERY
        TransaksiPembelian::where('status_transaksi', 'Preparing')
            ->where('metode_pengiriman', 'kurir')
            ->whereDate('tanggal_pengiriman', $today)
            ->whereNotNull('id_kurir')
            ->update(['status_transaksi' => 'In Delivery']);

        // DONATED
        TransaksiPembelian::where('status_transaksi', 'Ready for Pickup')
            ->whereDate('tanggal_pengambilan', '<', Carbon::now()->subDays(2)->toDateString())
            ->update(['status_transaksi' => 'Donated']);

        $this->info('Status transaksi berhasil diperbarui.');
    }
}
