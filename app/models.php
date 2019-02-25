<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class models extends Model
{
    public $table = "vfq0g_models";
    public $fillable = [
        'id',
        'model',
        'brand_id',
        'updated_at',
        'created_at'
    ];
}

