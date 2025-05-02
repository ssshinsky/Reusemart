<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'role';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'nama_role',
    ];


    // Relasi ke model Pegawai
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_role');
    }
}
