<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'role';

    // Primary key kustom
    protected $primaryKey = 'id_role';

    // Nonaktifkan timestamps
    public $timestamps = false;

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = ['nama_role'];

    // Relasi ke model Pegawai
    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'id_role', 'id_role');
    }
}
