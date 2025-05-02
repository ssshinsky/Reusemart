<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembelian extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'transaksi_pembelian';

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
        return $this->hasOneThrough(Pembeli::class, Keranjang::class, 'id_keranjang', 'id_pembeli');
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
}
