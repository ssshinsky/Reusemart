<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    // Nama tabel yang digunakan oleh model
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $guard = 'owner';

    // Kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'id_role',
        'nama_pegawai',
        'alamat_pegawai',
        'tanggal_lahir',
        'nomor_telepon',
        'gaji_pegawai',
        'email_pegawai',
        'password',
        'profil_pict',
    ];

    public function isOwner()
    {
        return $this->id_role == 1;
    }

    // Override method untuk autentikasi
    public function getAuthIdentifierName()
    {
        return 'id_pegawai'; // Harus mengembalikan kolom primary key
    }

    public function getAuthIdentifier()
    {
        return $this->id_pegawai; // Mengembalikan nilai id_pegawai
    }

    // Kolom yang harus disembunyikan dalam array / response
    protected $hidden = [
        'password', // Menyembunyikan password dari JSON output
    ];

    // Override method untuk autentikasi
    // public function getAuthIdentifierName()
    // {
    //     return 'email_pegawai'; // Sesuaikan dengan kolom email di tabel
    // }

    public function getAuthPassword()
    {
        return $this->password; // Kolom password
    }

    // Kolom yang akan di-cast ke tipe tertentu
    protected $casts = [
        'tanggal_lahir' => 'date', // Mengonversi kolom tanggal_lahir menjadi objek Date
        'gaji_pegawai' => 'double', // Mengonversi gaji_pegawai menjadi tipe double
    ];

    // Relasi dengan model Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    // Relasi ke DiskusiProduk
    public function diskusiProduk()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_pegawai', 'id_pegawai');
    }

    // Relasi ke KelolaTransaksi
    public function kelolaTransaksi()
    {
        return $this->hasMany(KelolaTransaksi::class, 'id_pegawai', 'id_pegawai');
    }

    // Relasi ke Komisi
    public function komisiHunter()
    {
        return $this->hasMany(Komisi::class, 'id_hunter', 'id_pegawai');
    }

    public function komisiOwner()
    {
        return $this->hasMany(Komisi::class, 'id_owner', 'id_pegawai');
    }

    // Relasi ke Merchandise
    public function merchandise()
    {
        return $this->hasMany(Merchandise::class, 'id_pegawai', 'id_pegawai');
    }

    // Relasi ke model Req Donasi
    public function requests()
    {
        return $this->hasMany(RequestDonasi::class, 'id_pegawai', 'id_pegawai');
    }

    // Relasi ke transaksi penitipan
    public function penitipanQC()
    {
        return $this->hasMany(TransaksiPenitipan::class, 'id_qc');
    }

    public function penitipanHunter()
    {
        return $this->hasMany(TransaksiPenitipan::class, 'id_hunter');
    }
}
