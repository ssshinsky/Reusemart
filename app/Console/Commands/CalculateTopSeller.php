```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penitip;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateTopSeller extends Command
{
    protected $signature = 'topseller:calculate';
    protected $description = 'Calculate Top Seller, assign bonus points, and update expired items';

    public function handle()
    {
        try {
            $lastMonth = Carbon::now()->subMonth();
            $startOfMonth = $lastMonth->startOfMonth();
            $endOfMonth = $lastMonth->endOfMonth();

            // 1. Hitung Top Seller
            $topSellerData = Barang::select('transaksi_penitipan.id_penitip')
                ->join('transaksi_penitipan', 'barang.id_transaksi_penitipan', '=', 'transaksi_penitipan.id_transaksi_penitipan')
                ->where('barang.status_barang', 'selesai')
                ->whereBetween('barang.updated_at', [$startOfMonth, $endOfMonth])
                ->groupBy('transaksi_penitipan.id_penitip')
                ->selectRaw('COUNT(*) as jumlah_barang')
                ->selectRaw('SUM(barang.harga_barang) as total_penjualan')
                ->orderByDesc('jumlah_barang')
                ->orderByDesc('total_penjualan')
                ->first();

            if ($topSellerData) {
                DB::transaction(function () use ($topSellerData) {
                    // Reset badge semua penitip
                    Penitip::query()->update(['badge' => false]);

                    // Update badge Top Seller
                    Penitip::where('id_penitip', $topSellerData->id_penitip)->update(['badge' => true]);

                    // 2. Beri bonus poin (1% dari total penjualan)
                    $bonusPoin = $topSellerData->total_penjualan * 0.01;
                    Penitip::where('id_penitip', $topSellerData->id_penitip)->increment('poin_penitip', $bonusPoin);

                    Log::info('Top Seller calculated', [
                        'id_penitip' => $topSellerData->id_penitip,
                        'jumlah_barang' => $topSellerData->jumlah_barang,
                        'total_penjualan' => $topSellerData->total_penjualan,
                        'bonus_poin' => $bonusPoin,
                    ]);
                });
            } else {
                Log::info('No Top Seller for last month');
            }

            // 3. Update status barang tidak diambil > 7 hari
            $expiredItems = Barang::where('status_barang', 'menunggu pengambilan')
                ->where('tanggal_berakhir', '<', Carbon::now()->subDays(7))
                ->get();

            foreach ($expiredItems as $item) {
                $item->update(['status_barang' => 'barang untuk donasi']);
                Log::info('Item updated to donation', ['id_barang' => $item->id_barang]);
            }

            $this->info('Top Seller calculation and item status update completed.');
        } catch (\Exception $e) {
            Log::error('Error calculating Top Seller', ['error' => $e->getMessage()]);
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
```