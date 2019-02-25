<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveChats extends Model
{
    public $table = "active_chats";
    public $fillable = [
        'user_id',
        'dealer_id',
        'offer_id',
        'car_id',
        'request_id',
        'chat_id',
        'created_at',
        'updated_at'
    ];
}
