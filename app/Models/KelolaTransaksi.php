<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelolaTransaksi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'kelola_transaksi';
    protected $primaryKey = 'id_kelola';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_pembelian', 
        'id_pegawai',
    ];

    // Relasi ke model TransaksiPembelian
    public function transaksiPembelian()
    {
        return $this->belongsTo(TransaksiPembelian::class, 'id_pembelian', 'id_pembelian');
    }

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
