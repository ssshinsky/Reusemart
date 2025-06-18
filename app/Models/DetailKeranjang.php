<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKeranjang extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'detail_keranjang';
    protected $primaryKey = 'id_detail_keranjang'; 
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_keranjang', 
        'id_item_keranjang',
    ];

    // Relasi dengan model Keranjang
    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang');
    }

    // Relasi dengan model ItemKeranjang
    public function itemKeranjang()
    {
        return $this->belongsTo(ItemKeranjang::class, 'id_item_keranjang', 'id_item_keranjang');
    }
}
