<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['tokenable_id', 'tokenable_type', 'token'];

    public function tokenable()
    {
        return $this->morphTo();
    }
}