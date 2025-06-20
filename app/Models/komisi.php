<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komisi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'komisi';
    protected $primaryKey = 'id_komisi';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_pembelian',
        'id_penitip',
        'id_hunter',
        'id_owner',
        'komisi_hunter',
        'komisi_penitip',
        'komisi_reusemart',
        'bonus_penitip_terjual_cepat',
    ];

    // Relasi ke Pembelian
    public function pembelian()
    {
        return $this->belongsTo(TransaksiPembelian::class, 'id_pembelian', 'id_pembelian');
    }

    // Relasi ke Penitip
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }

    // Relasi ke Hunter (Pegawai)
    public function hunter()
    {
        return $this->belongsTo(Pegawai::class, 'id_hunter', 'id_pegawai');
    }

    // Relasi ke Owner (Pegawai)
    public function owner()
    {
        return $this->belongsTo(Pegawai::class, 'id_owner', 'id_pegawai');
    }
}
