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
        'no_telp',
        'alamat',
        'password',
        'status_penitip',
        'saldo_penitip',
        'rata_rating'
    ];

    protected $hidden = [
        'password',
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
        return $this->hasMany(TransaksiPenitipan::class, 'id_penitip', 'id_penitip');
    }
}
