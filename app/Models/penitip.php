<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Penitip extends Model
{
    use HasApiTokens, Notifiable, HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'penitip';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'nik_penitip',
        'nama_penitip',
        'email_penitip',
        'password',
        'poin_penitip',
        'no_telp',
        'alamat',
        'rata_rating',
        'status_penitip',
        'saldo_penitip',
        'profil_pict',
        'badge',
    ];

    // Kolom yang harus disembunyikan dalam array / response
    protected $hidden = [
        'password', // Menyembunyikan password dari JSON output
    ];

    // Kolom yang akan di-cast ke tipe tertentu
    protected $casts = [
        'rata_rating' => 'float', // Mengonversi rata_rating menjadi tipe float
        'saldo_penitip' => 'double', // Mengonversi saldo_penitip menjadi tipe double
        'badge' => 'boolean', // Mengonversi badge menjadi tipe boolean
    ];

    // public function transaksi() {
    //     return $this->hasMany(Transaksi::class, 'id_penitip');
    // }

    // Relasi ke Komisi
    public function komisi()
    {
        return $this->hasMany(Komisi::class, 'id_penitip', 'id_penitip');
    }

    // Relasi ke Transaksi Penitipan
    public function penitipan()
    {
        return $this->hasMany(TransaksiPenitipan::class, 'id_penitip');
    }
}
