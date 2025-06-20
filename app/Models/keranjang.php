<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'banyak_barang',
    ];

    // Relasi ke DetailKeranjang
    public function detailKeranjang()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_keranjang', 'id_keranjang');
    }

    // Relasi ke Transaksi Pembelian
    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_keranjang');
    }
}
