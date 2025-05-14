<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksiPenitipan extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'transaksi_penitipan';
    protected $primaryKey = 'id_transaksi_penitipan';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_qc',
        'id_hunter',
        'id_penitip',
        'tanggal_penitipan',
        'tanggal_berakhir',
        'perpanjangan',
    ];

    // Relasi ke model Pegawai (QC)
    public function qc()
    {
        return $this->belongsTo(Pegawai::class, 'id_qc');
    }

    // Relasi ke model Pegawai (Hunter)
    public function hunter()
    {
        return $this->belongsTo(Pegawai::class, 'id_hunter');
    }

    // Relasi ke model Penitip
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_transaksi_penitipan', 'id_transaksi_penitipan');
    }
}
