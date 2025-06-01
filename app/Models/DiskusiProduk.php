<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskusiProduk extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'diskusi_produk';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_barang',
        'id_pembeli',
        'id_pegawai',
        'diskusi',
        'create_at',
        'update_at',
    ];

    // Relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    // Relasi dengan model Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    // Relasi dengan model Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
