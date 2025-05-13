<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Organisasi extends Model
{
    use HasApiTokens, Notifiable, HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'organisasi';
    protected $primaryKey = 'id_organisasi';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'nama_organisasi',
        'alamat',
        'kontak',
        'email_organisasi',
        'password',
        'status_organisasi',
    ];

    // Kolom yang harus disembunyikan dalam array / response
    protected $hidden = [
        'password', // Menyembunyikan password dari JSON output
    ];

    // Kolom yang akan di-cast ke tipe tertentu
    protected $casts = [
        'email_organisasi' => 'string',
        'password' => 'string',
    ];

    // Relasi ke model Req Donasi
    public function requests()
    {
        return $this->hasMany(RequestDonasi::class, 'id_organisasi', 'id_organisasi');
    }
}
