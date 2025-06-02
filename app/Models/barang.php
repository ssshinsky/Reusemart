<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiPenitipan;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $timestamps = false;

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
        'tanggal_garansi',
        'tanggal_berakhir',
        'perpanjangan',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function transaksiPenitipan()
    {
        return $this->belongsTo(TransaksiPenitipan::class, 'id_transaksi_penitipan', 'id_transaksi_penitipan');
    }

    public function diskusiProduk()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_barang', 'id_barang');
    }

    public function donasi()
    {
        return $this->hasOne(Donasi::class, 'id_barang', 'id_barang');
    }

    public function gambar()
    {
        return $this->hasMany(Gambar::class, 'id_barang', 'id_barang');
    }

    public function itemKeranjangs()
    {
        return $this->hasMany(ItemKeranjang::class, 'id_barang', 'id_barang');
    }
}