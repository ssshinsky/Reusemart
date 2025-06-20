<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransaksiPembelian;
use Carbon\Carbon;

class CekTransaksiHangus extends Command
{
    protected $signature = 'transaksi:cek-hangus';
    protected $description = 'Cek transaksi yang melewati H+2 dari tanggal_pengambilan dan belum diambil';

    public function handle()
    {
        $transaksis = TransaksiPembelian::where('status_transaksi', 'Ready for Pickup')
            ->whereNotNull('tanggal_pengambilan')
            ->whereNull('tanggal_ambil')
            ->whereDate('tanggal_pengambilan', '<', Carbon::now()->subDays(2))
            ->get();

        foreach ($transaksis as $transaksi) {
            $transaksi->update([
                'status_transaksi' => 'Expired'
            ]);
            foreach ($transaksi->detailKeranjangs as $detail) {
                $barang = $detail->itemKeranjang->barang;
                $barang->update(['status_barang' => 'For Donation']);
            }
            $this->info("Transaksi ID {$transaksi->id_pembelian} diubah menjadi Expired.");
        }
        $this->info('Pemeriksaan selesai.');
    }
}