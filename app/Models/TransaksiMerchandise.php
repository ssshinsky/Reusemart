<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMerchandise extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'transaksi_merchandise';

    // Primary key kustom
    protected $primaryKey = 'id_transaksi_merchandise';


    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_merchandise',
        'id_pembeli',
        'jumlah',
        'total_poin_penukaran',
        'tanggal_klaim',
        'tanggal_ambil_merch',
        'status_transaksi', 
    ];

    // Relasi ke model Merchandise
    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class, 'id_merchandise');
    }

    // Relasi ke model Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }
}