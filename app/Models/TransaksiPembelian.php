<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPembelian;
use App\Models\Alamat;

class TransaksiPembelian extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'transaksi_pembelian';
    protected $primaryKey = 'id_pembelian';
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_keranjang',
        'id_alamat',
        'no_resi',
        'tanggal_pembelian',
        'waktu_pembayaran',
        'bukti_tf',
        'total_harga_barang',
        'metode_pengiriman',
        'ongkir',
        'tanggal_ambil',
        'tanggal_pengiriman',
        'total_harga',
        'status_transaksi',
        'poin_terpakai',
        'poin_pembeli',
        'poin_penitip',
        'status_pengiriman',
        'id_kurir',
    ];

    // Relasi ke model Keranjang
    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang');
    }

    // Relasi ke model Alamat
    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat');
    }

    // Relasi ke model Pembeli (melalui Keranjang)
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }

    // Relasi ke KelolaTransaksi
    public function kelolaTransaksi()
    {
        return $this->hasMany(KelolaTransaksi::class, 'id_pembelian', 'id_pembelian');
    }

    // Relasi ke Komisi
    public function komisi()
    {
        return $this->hasMany(Komisi::class, 'id_pembelian', 'id_pembelian');
    }
  
    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'id_transaksi');
    }

    public function detailKeranjangs()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_keranjang', 'id_keranjang');
    }

    public function kurir()
    {
        return $this->belongsTo(Pegawai::class, 'id_kurir', 'id_pegawai');
    }

    public function pengirimanDanPengambilanList()
    {
        $this->ensureGudang();

        $transaksi = TransaksiPembelian::with([
            'keranjang.detailKeranjang.itemKeranjang.pembeli', // relasi berantai
            'detailKeranjangs.itemKeranjang.barang.gambar',
        ])
        ->whereIn('status_transaksi', ['Ready for Pickup', 'In Delivery'])
        ->orderBy('tanggal_pembelian', 'asc')
        ->get();

        return view('gudang.transaksi_pengiriman', compact('transaksi'));
    }
}
