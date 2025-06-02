<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penitipan';
    protected $primaryKey = 'id_transaksi_penitipan';

    protected $fillable = [
        'id_qc',
        'id_hunter',
        'id_penitip',
        'tanggal_penitipan',
    ];

    public function qc()
    {
        return $this->belongsTo(Pegawai::class, 'id_qc');
    }

    public function hunter()
    {
        return $this->belongsTo(Pegawai::class, 'id_hunter');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_transaksi_penitipan', 'id_transaksi_penitipan');
    }
}