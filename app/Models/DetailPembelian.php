<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelian'; // atau sesuai nama tabel

    protected $primaryKey = 'id_detail'; // sesuaikan dengan field primary key

    public $timestamps = false; // kalau tidak ada created_at dan updated_at
}
