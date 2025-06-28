<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KelolaTransaksi;
use App\Models\TransaksiPembelian;
use App\Models\Pegawai;

class KurirController extends Controller
{
    public function getDeliveries(Request $request, $idPegawai)
    {
        try {
            // Validasi pegawai
            $pegawai = Pegawai::where('id_pegawai', $idPegawai)->where('id_role', 5)->first();
            if (!$pegawai) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized Pegawai'], 403);
            }

            // Ambil data tanpa paginasi
            $data = DB::table('kelola_transaksi as kt')
                ->join('transaksi_pembelian as tp', 'kt.id_pembelian', '=', 'tp.id_pembelian')
                ->where('kt.id_pegawai', $idPegawai)
                ->where('tp.status_transaksi', 'Selesai')
                ->select(
                    'tp.id_pembelian',
                    'tp.status_transaksi',
                    'tp.total_harga',
                    'tp.tanggal_pembelian',
                    'tp.metode_pengiriman',
                    'tp.no_resi'
                )
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data, // TANPA ->items()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDeliveries: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    public function getActiveDeliveries(Request $request, $idPegawai)
    {
        try {
            // Validasi pegawai
            $pegawai = Pegawai::where('id_pegawai', $idPegawai)->where('id_role', 5)->first();
            if (!$pegawai) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized Pegawai'], 403);
            }

            // Ambil data tanpa paginasi
            $data = DB::table('kelola_transaksi as kt')
                ->join('transaksi_pembelian as tp', 'kt.id_pembelian', '=', 'tp.id_pembelian')
                ->where('kt.id_pegawai', $idPegawai)
                ->where('tp.status_transaksi', 'Sedang Dikirim')
                ->orwhere('tp.status_transaksi', 'In Delivery')
                ->select(
                    'tp.id_pembelian',
                    'tp.status_transaksi',
                    'tp.total_harga',
                    'tp.tanggal_pembelian',
                    'tp.metode_pengiriman',
                    'tp.no_resi'
                )
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ], 200, [], JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            \Log::error('Error in getActiveDeliveries: ' . $e->getMessage(), [
                'id_pegawai' => $idPegawai,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    public function updateStatusTransaksi(Request $request, $idPembelian)
    {
        try {
            // Validasi input
            $request->validate([
                'status_transaksi' => 'required|in:Menunggu Pengiriman,Sedang Dikirim,Selesai,In Delivery',
            ]);

            // Validasi pegawai
            $idPegawai = $request->input('id_pegawai'); // Ambil id_pegawai dari body request
            if (!$idPegawai) {
                return response()->json(['status' => 'error', 'message' => 'ID Pegawai diperlukan'], 400);
            }

            $pegawai = Pegawai::where('id_pegawai', $idPegawai)->where('id_role', 5)->first();
            if (!$pegawai) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized Pegawai'], 403);
            }

            // Cek keberadaan transaksi
            $transaksi = TransaksiPembelian::find($idPembelian);
            if (!$transaksi) {
                return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Cek otorisasi di kelola_transaksi
            $kelola = KelolaTransaksi::where('id_pembelian', $idPembelian)
                ->where('id_pegawai', $idPegawai)
                ->exists();

            if (!$kelola) {
                return response()->json(['status' => 'error', 'message' => 'Tidak berwenang untuk transaksi ini'], 403);
            }

            // Perbarui status transaksi
            $transaksi->status_transaksi = $request->status_transaksi;
            $transaksi->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status transaksi diperbarui'
            ], 200, [], JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            \Log::error('Error in updateStatusTransaksi: ' . $e->getMessage(), [
                'id_pembelian' => $idPembelian,
                'id_pegawai' => $idPegawai,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }
}