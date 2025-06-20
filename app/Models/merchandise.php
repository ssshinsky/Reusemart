<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'merchandise';
    
    // Primary key kustom
    protected $primaryKey = 'id_merchandise';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_pegawai',
        'nama_merch',
        'poin',
        'stok',
        'gambar_merch',
    ];

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    // relasi sama transaksi merch
    public function transaksiMerch()
    {
        return $this->hasMany(TransaksiMerchandise::class, 'id_merchandise', 'id_merchandise');
    }

    public function addedBy()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function modifiedBy()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai_terakhir', 'id_pegawai');
    }

}
