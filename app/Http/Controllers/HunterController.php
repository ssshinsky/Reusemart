<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Komisi;
use App\Models\TransaksiPenitipan;
use App\Models\Barang;   
use App\Models\TransaksiPembelian; 
use App\Models\ItemKeranjang;
use App\Models\Penitip; 
use Carbon\Carbon; 

class HunterController extends Controller
{
    public function getHunterProfileAndTotalCommission(Request $request)
    {
        $hunter = $request->user(); 

        if (!$hunter || $hunter->id_role !== 6) {
            return response()->json(['message' => 'Unauthorized or not a hunter.'], 403);
        }

        $totalKomisi = $hunter->komisiHunter()->sum('komisi_hunter');

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
            'message' => 'Hunter profile and total commission retrieved successfully.'
        ]);
    }

    public function getCommissionHistory(Request $request)
    {
        $hunter = $request->user();

        if (!$hunter || $hunter->id_role !== 6) {
            return response()->json(['message' => 'Unauthorized or not a hunter.'], 403);
        }

        $commissions = Komisi::where('id_hunter', $hunter->id_pegawai)
            ->with([
                'pembelian.itemKeranjangs.barang.gambar',
                'pembelian.itemKeranjangs.barang.transaksiPenitipan.penitip',
            ])
            ->get()
            ->map(function ($commission) {
                $barangData = null;
                $tanggalPenitipan = null;
                $tanggalPembelian = null;
                $namaPenitip = 'N/A';
                $statusBarangTerjual = 'N/A';
                $hargaBarang = 0.0;
                $gambarBarang = null;

                if ($commission->pembelian && $commission->pembelian->itemKeranjangs->isNotEmpty()) {
                    $firstItem = $commission->pembelian->itemKeranjangs->first(); 
                    if ($firstItem->barang) {
                        $barangData = $firstItem->barang;
                        
                        $tanggalPenitipan = $barangData->transaksiPenitipan->tanggal_penitipan ?? null;
                        $namaPenitip = $barangData->transaksiPenitipan->penitip->nama_penitip ?? 'N/A';
                        $statusBarangTerjual = $barangData->status_barang;
                        $hargaBarang = $barangData->harga_barang;
                        $gambarBarang = $barangData->gambar->first()->gambar_barang ?? null;
                    }
                }
                
                $tanggalPembelian = $commission->pembelian->tanggal_pembelian ?? null;

                if ($barangData && in_array($statusBarangTerjual, ['Donated', 'Sold'])) {
                    return [
                        'id_komisi' => $commission->id_komisi,
                        'komisi_didapatkan' => (double) $commission->komisi_hunter,
                        'tanggal_penitipan' => $tanggalPenitipan ? Carbon::parse($tanggalPenitipan)->format('Y-m-d') : null,
                        'tanggal_pembelian' => $tanggalPembelian ? Carbon::parse($tanggalPembelian)->format('Y-m-d') : null,
                        'nama_barang' => $barangData->nama_barang ?? 'N/A', 
                        'harga_barang' => (double) $hargaBarang,
                        'gambar_barang' => $gambarBarang,
                        'nama_penitip' => $namaPenitip,
                        'status_barang_terjual' => $statusBarangTerjual,
                    ];
                }
                return null; 
            })
            ->filter() 
            ->values(); 

        return response()->json([
            'success' => true,
            'history' => $commissions,
            'message' => 'Commission history retrieved successfully.'
        ]);
    }

    public function getCommissionDetail(Request $request, $commissionId)
    {
        $hunter = $request->user();

        if (!$hunter || $hunter->id_role !== 6) {
            return response()->json(['message' => 'Unauthorized or not a hunter.'], 403);
        }

        $commission = Komisi::where('id_komisi', $commissionId)
            ->where('id_hunter', $hunter->id_pegawai)
            ->with([
                'pembelian.itemKeranjangs.barang.gambar',
                'pembelian.itemKeranjangs.barang.transaksiPenitipan.penitip',
            ])
            ->first();

        if (!$commission) {
            return response()->json(['message' => 'Commission detail not found or not accessible by this hunter.'], 404);
        }

        $barangData = null;
        $tanggalPenitipan = null;
        $tanggalPembelian = null;
        $namaPenitip = 'N/A';
        $statusBarangTerjual = 'N/A';
        $hargaBarang = 0.0;
        $gambarBarang = null;

        // Mencari barang pertama yang valid dari item keranjang yang terkait dengan pembelian komisi ini
        if ($commission->pembelian && $commission->pembelian->itemKeranjangs->isNotEmpty()) {
            $firstItem = $commission->pembelian->itemKeranjangs->first(); // Ambil item pertama
            if ($firstItem->barang) {
                $barangData = $firstItem->barang;
                
                // Akses relasi dengan null-aware operator
                $tanggalPenitipan = $barangData->transaksiPenitipan->tanggal_penitipan ?? null;
                $namaPenitip = $barangData->transaksiPenitipan->penitip->nama_penitip ?? 'N/A';
                $statusBarangTerjual = $barangData->status_barang;
                $hargaBarang = $barangData->harga_barang;
                $gambarBarang = $barangData->gambar->first()->gambar_barang ?? null;
            }
        }

        $tanggalPembelian = $commission->pembelian->tanggal_pembelian ?? null; // Tanggal pembelian dari transaksi

        // Filter validasi untuk detail, pastikan ada barang dan statusnya relevan
        if (!$barangData || !in_array($statusBarangTerjual, ['Donated', 'Sold'])) {
             return response()->json(['message' => 'Associated item not found or not a valid status for commission detail.'], 404);
        }

        return response()->json([
            'success' => true,
            'detail' => [
                'id_komisi' => $commission->id_komisi,
                'komisi_didapatkan' => (double) $commission->komisi_hunter,
                'nama_barang' => $barangData->nama_barang,
                'harga_barang' => (double) $hargaBarang,
                'gambar_barang' => $gambarBarang,
                'nama_penitip' => $namaPenitip,
                'tanggal_penitipan' => $tanggalPenitipan ? Carbon::parse($tanggalPenitipan)->format('Y-m-d') : null,
                'tanggal_pembelian' => $tanggalPembelian ? Carbon::parse($tanggalPembelian)->format('Y-m-d') : null,
                'status_barang_terjual' => $statusBarangTerjual,
            ],
            'message' => 'Commission detail retrieved successfully.'
        ]);
    }
}