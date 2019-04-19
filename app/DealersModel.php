<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealersModel extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'vfq0g_dealers';
    public $timestamps = false;
    protected $fillable = array(
        'name',
        'email',
        'password',
        'location',
        'status'
    );
}
