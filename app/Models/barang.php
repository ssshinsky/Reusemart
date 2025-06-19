<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiPenitipan;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $casts = [
        'tanggal_garansi' => 'date',
        'tanggal_berakhir' => 'date',
        'tanggal_konfirmasi_pengambilan' => 'datetime', 
        'batas_pengambilan' => 'datetime',             
        'created_at' => 'datetime',                    
        'updated_at' => 'datetime',  
        'rating' => 'integer',
    ];

    protected $fillable = [
        'id_kategori',
        'id_transaksi_penitipan',
        'kode_barang',
        'nama_barang',
        'harga_barang',
        'berat_barang',
        'deskripsi_barang',
        'status_garansi',
        'status_barang',
        'rating',
        'tanggal_garansi',
        'tanggal_berakhir',
        'perpanjangan',
    ];

    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function transaksiPenitipan()
    {
        return $this->belongsTo(TransaksiPenitipan::class, 'id_transaksi_penitipan', 'id_transaksi_penitipan');
    }

    public function diskusiProduk()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_barang', 'id_barang');
    }

    public function donasi()
    {
        return $this->hasOne(Donasi::class, 'id_barang', 'id_barang');
    }

    public function gambar()
    {
        return $this->hasMany(Gambar::class, 'id_barang', 'id_barang');
    }

    public function itemKeranjangs()
    {
        return $this->hasMany(ItemKeranjang::class, 'id_barang', 'id_barang');
    }

    public function penitip()
    {
        return $this->hasOneThrough(
            \App\Models\Penitip::class,
            \App\Models\TransaksiPenitipan::class,
            'id_transaksi_penitipan', // Foreign key di TransaksiPenitipan
            'id_penitip',             // Foreign key di Penitip
            'id_transaksi_penitipan', // Local key di Barang
            'id_transaksi_penitipan'  // Local key di TransaksiPenitipan
        );
    }
}