<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'kategori';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi ke model Barang
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }
}
