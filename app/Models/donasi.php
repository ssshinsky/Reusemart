<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'donasi';
    protected $primaryKey = 'id_donasi';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_request',
        'id_barang',
        'tanggal_donasi',
        'nama_penerima',
    ];

    protected static function booted()
    {
        static::created(function ($donasi) {
            // Perbarui status_request di RequestDonasi
            $requestDonasi = RequestDonasi::where('id_request', $donasi->id_request)->first();
            if ($requestDonasi) {
                $requestDonasi->update(['status_request' => 'sudah di donasikan']);
            }

            // Perbarui status_barang di Barang
            $barang = Barang::where('id_barang', $donasi->id_barang)->first();
            if ($barang) {
                $barang->update(['status_barang' => 'didonasikan']);
            }
        });
    }

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
