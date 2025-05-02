<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'donasi';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_request',
        'id_barang',
        'tanggal_donasi',
        'nama_penerima',
    ];

    // Relasi dengan model RequestDonasi
    public function requestDonasi()
    {
        return $this->belongsTo(RequestDonasi::class, 'id_request', 'id_request');
    }

    // Relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
