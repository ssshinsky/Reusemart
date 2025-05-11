<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiPenitipan;

class Barang extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'barang';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_kategori', 
        'id_transaksi_penitipan',
        'kode_barang', 
        'nama_barang', 
        'harga_barang', 
        'berat_barang', 
        'deskripsi_barang', 
        'status_garansi', 
        'status_barang', 
        'tanggal_garansi'
    ];

    // Relasi dengan model Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    // Relasi dengan model TransaksiPenitipan
    public function transaksiPenitipan()
    {
        return $this->belongsTo(TransaksiPenitipan::class, 'id_transaksi_penitipan', 'id_transaksi_penitipan');
    }

    // Relasi ke DiskusiProduk
    public function diskusiProduk()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_barang', 'id_barang');
    }

    // Relasi ke Donasi
    public function donasi()
    {
        return $this->hasOne(Donasi::class, 'id_barang', 'id_barang');
    }

    // Relasi ke Gambar
    public function gambar()
    {
        return $this->hasMany(Gambar::class, 'id_barang', 'id_barang');
    }

    // Relasi ke ItemKeranjang
    public function itemKeranjangs()
    {
        return $this->hasMany(ItemKeranjang::class, 'id_barang', 'id_barang');
    }
}
