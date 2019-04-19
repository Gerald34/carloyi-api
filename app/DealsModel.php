<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealsModel extends Model
{

    public $table = "vfq0g_dealer_user_posts";
    public $fillable = [
        'user_id',
        'dealer_id',
        'offer_id',
        'car_id',
        'request_id',
        'status',
        'created_at',
        'updated_at'
    ];

}