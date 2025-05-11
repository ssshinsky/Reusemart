<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Alamat;

class Pembeli extends Model
{
    use HasApiTokens, Notifiable, HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'pembeli';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'nama_pembeli',
        'email_pembeli',
        'tanggal_lahir',
        'nomor_telepon',
        'password',
        'poin_pembeli',
        'profil_pict',
    ];

    // Kolom yang harus disembunyikan dalam array / response
    protected $hidden = [
        'password', // Menyembunyikan password dari JSON output
    ];

    // Kolom yang akan di-cast ke tipe tertentu
    protected $casts = [
        'tanggal_lahir' => 'date', // Mengonversi kolom tanggal_lahir menjadi objek Date
        'poin_pembeli' => 'integer', // Mengonversi poin_pembeli menjadi tipe integer
    ];

    // public function keranjangs() {
    //     return $this->hasMany(Keranjang::class, 'id_pembeli');
    // }

    // Relasi ke Alamat
    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'id_pembeli', 'id_pembeli');
    }

    // Alamat Default
    public function alamatDefault()
    {
        return $this->hasOne(Alamat::class, 'id_pembeli', 'id_pembeli')
                    ->where('is_default', 1);
    }


    // Relasi ke DiskusiProduk
    public function diskusiProduk()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_pembeli', 'id_pembeli');
    }

    // Relasi ke ItemKeranjang
    public function itemKeranjangs()
    {
        return $this->hasMany(ItemKeranjang::class, 'id_pembeli', 'id_pembeli');
    }

    // relasi sama transaksi merch
    public function transaksiMerch()
    {
        return $this->hasMany(TransaksiMerchandise::class, 'id_pembeli', 'id_pembeli');
    }
}
