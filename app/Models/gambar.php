<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gambar extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'gambar';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_barang',
        'gambar_barang',
    ];

    // Relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
