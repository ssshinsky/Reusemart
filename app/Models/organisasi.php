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

    protected $table = 'organisasi';
    protected $primaryKey = 'id_organisasi';

    // Tambahkan ini:
    protected $primaryKey = 'id_organisasi';

    protected $fillable = [
        'nama_organisasi',
        'alamat',
        'kontak',
        'email_organisasi',
        'password',
        'status_organisasi',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_organisasi' => 'string',
        'password' => 'string',
    ];

    public function requests()
    {
        return $this->hasMany(RequestDonasi::class, 'id_organisasi', 'id_organisasi');
    }
}
