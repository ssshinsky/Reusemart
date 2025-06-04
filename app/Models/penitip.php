<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penitip extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $table = 'penitip';
    protected $primaryKey = 'id_penitip';
    
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
        'poin_penitip',
        'profil_pict',
        'badge',
        'foto_ktp',
    ];

    protected $hidden = [
        'password', 'foto_ktp',
    ];

    protected $casts = [
        'rata_rating' => 'float',
        'saldo_penitip' => 'double',
        'badge' => 'boolean',
    ];

    // Relasi ke Komisi
    public function komisi()
    {
        return $this->hasMany(Komisi::class, 'id_penitip', 'id_penitip');
    }

    // Relasi ke Transaksi Penitipan
    public function penitipan()
    {
        return $this->hasMany(transaksiPenitipan::class, 'id_penitip', 'id_penitip');
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'transaksi_penitipan', 'id_penitip', 'id_barang');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class, 'id_penitip');
    }

}