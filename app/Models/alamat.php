<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'alamat';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_pembeli', 
        'nama_orang', 
        'label_alamat', 
        'alamat_lengkap', 
        'nomor_telepon', 
        'kode_pos', 
        'is_default',
    ];

    // Relasi dengan model Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    // Relasi ke Transaksi Pembelian
    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_alamat');
    }
}
