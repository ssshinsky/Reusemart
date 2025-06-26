<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Komisi;
use Carbon\Carbon;

class HunterController extends Controller
{
    /**
     * Mengambil profil Hunter dan total komisi berdasarkan ID dari URL.
     */
    public function getHunterProfileAndTotalCommission(Request $request, $id) // <-- PERBAIKAN: Tambahkan parameter $id
    {
        // Ganti $request->user() dengan Pegawai::find($id)
        $hunter = Pegawai::find($id);

        // Pengecekan jika hunter tidak ditemukan atau bukan hunter
        if (!$hunter || $hunter->id_role != 6) {
            return response()->json(['message' => 'Hunter with ID ' . $id . ' not found or is not a hunter.'], 404);
        }
        
        // Sekarang $hunter dijamin bukan null
        $totalKomisi = Komisi::where('id_hunter', $hunter->id_pegawai)->sum('komisi_hunter');

        return response()->json([
            'success' => true,
            'profile' => [
                'id_pegawai' => $hunter->id_pegawai,
                'nama_pegawai' => $hunter->nama_pegawai,
                'email_pegawai' => $hunter->email_pegawai,
                'alamat_pegawai' => $hunter->alamat_pegawai,
                'tanggal_lahir' => $hunter->tanggal_lahir,
                'nomor_telepon' => $hunter->nomor_telepon,
                'profil_pict' => $hunter->profil_pict,
            ],
            'total_komisi' => (double) $totalKomisi,
            'message' => 'Hunter profile retrieved successfully.'
        ]);
    }

    /**
     * Mengambil riwayat komisi berdasarkan ID dari URL.
     */
    public function getCommissionHistory(Request $request, $id) // <-- PERBAIKAN: Tambahkan parameter $id
    {
        // Ganti $request->user() dengan Pegawai::find($id)
        $hunter = Pegawai::find($id);

        if (!$hunter || $hunter->id_role != 6) {
            return response()->json(['message' => 'Hunter with ID ' . $id . ' not found or is not a hunter.'], 404);
        }
        
        $commissions = Komisi::where('id_hunter', $hunter->id_pegawai)
            ->with([
                'pembelian.keranjang.itemKeranjangs.barang.gambar',
                'pembelian.keranjang.itemKeranjangs.barang.transaksiPenitipan.penitip'
            ])
            ->latest('created_at')
            ->get();

        $history = $commissions->map(function ($commission) {
            $pembelian = optional($commission->pembelian);
            $keranjang = optional($pembelian->keranjang);
            $item = optional($keranjang->itemKeranjangs)->first();
            $barang = optional($item)->barang;

            if (is_null($barang)) return null;

            $penitip = optional($barang->transaksiPenitipan)->penitip;
            $gambar = optional($barang->gambar)->first();

            return [
                'id_komisi'             => $commission->id_komisi,
                'komisi_didapatkan'     => (double) $commission->komisi_hunter,
                'nama_barang'           => $barang->nama_barang,
                'harga_barang'          => (double) $barang->harga_barang,
                'gambar_barang'         => optional($gambar)->gambar_barang,
                'nama_penitip'          => optional($penitip)->nama_penitip ?? 'N/A',
                'tanggal_penitipan'     => optional($barang->transaksiPenitipan)->tanggal_penitipan ? Carbon::parse($barang->transaksiPenitipan->tanggal_penitipan)->toDateString() : null,
                'tanggal_pembelian'     => $pembelian->tanggal_pembelian ? Carbon::parse($pembelian->tanggal_pembelian)->toDateString() : null,
                'status_barang_terjual' => $barang->status_barang,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'history' => $history,
            'message' => 'Commission history retrieved successfully.'
        ]);
    }

    /**
     * Mengambil detail komisi berdasarkan ID dari URL.
     */
    public function getCommissionDetail(Request $request, $hunterId, $commissionId)
    {
        // Ganti $request->user() dengan Pegawai::find($id)
        $hunter = Pegawai::find($hunterId);

        if (!$hunter || $hunter->id_role != 6) {
            return response()->json(['message' => 'Unauthorized or not a hunter.'], 403);
        }

        $commission = Komisi::where('id_komisi', $commissionId)
            ->where('id_hunter', $hunter->id_pegawai) // Pastikan komisi ini milik hunter tsb
            ->with([
                'pembelian.keranjang.itemKeranjangs.barang.gambar',
                'pembelian.keranjang.itemKeranjangs.barang.transaksiPenitipan.penitip'
            ])
            ->first();

        if (!$commission) {
            return response()->json(['message' => 'Commission detail not found.'], 404);
        }

        // Logika untuk mem-format detail (sama seperti di history)
        $pembelian = optional($commission->pembelian);
        $keranjang = optional($pembelian->keranjang);
        $item = optional($keranjang->itemKeranjangs)->first();
        $barang = optional($item)->barang;

        if (is_null($barang)) {
            return response()->json(['message' => 'Commission item detail not found.'], 404);
        }

        $penitip = optional($barang->transaksiPenitipan)->penitip;
        $gambar = optional($barang->gambar)->first();

        $detail = [
            'id_komisi'             => $commission->id_komisi,
            'komisi_didapatkan'     => (double) $commission->komisi_hunter,
            'nama_barang'           => $barang->nama_barang,
            'harga_barang'          => (double) $barang->harga_barang,
            'gambar_barang'         => optional($gambar)->gambar_barang,
            'nama_penitip'          => optional($penitip)->nama_penitip ?? 'N/A',
            'tanggal_penitipan'     => optional($barang->transaksiPenitipan)->tanggal_penitipan ? Carbon::parse($barang->transaksiPenitipan->tanggal_penitipan)->toDateString() : null,
            'tanggal_pembelian'     => $pembelian->tanggal_pembelian ? Carbon::parse($pembelian->tanggal_pembelian)->toDateString() : null,
            'status_barang_terjual' => $barang->status_barang,
        ];
        
        return response()->json([
            'success' => true,
            'detail' => $detail,
            'message' => 'Commission detail retrieved successfully.'
        ]);
    }
}
