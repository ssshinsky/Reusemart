<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemKeranjang extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'item_keranjang';
    protected $primaryKey = 'id_item_keranjang';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_pembeli',
        'id_barang',
        'is_selected',
    ];

    // Relasi dengan model Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    // Relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    // Relasi ke DetailKeranjang
    public function detailKeranjangs()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_item_keranjang', 'id_item_keranjang');
    }

    // Relasi many-to-many ke Keranjang melalui detail_keranjang
    public function keranjang()
    {
        return $this->belongsToMany(Keranjang::class, 'detail_keranjang', 'id_item_keranjang', 'id_keranjang');
    }
}
