<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDonasi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'request_donasi';
    protected $primaryKey = 'id_request';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_organisasi',
        'id_pegawai',
        'request',
        'status_request',
    ];

    // Relasi ke model Organisasi
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi');
    }

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    // Relasi ke Donasi
    public function donasi()
    {
        return $this->hasOne(Donasi::class, 'id_request', 'id_request');
    }
}
