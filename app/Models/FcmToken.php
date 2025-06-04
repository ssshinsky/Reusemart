<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'id_pembeli', 'id_penitip', 'id_hunter', 'id_kurir', 'fcm_token', 'device_type'
    ];
}