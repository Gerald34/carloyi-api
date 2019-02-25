<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PushNotificationModel extends Model
{
    protected $table ="app_notifications";
    private $now;
    protected $fillable = [
        'id' => '',
        'userid',
        'endpoint',
        'p256dh',
        'auth',
        'created_at',
        'updated_at',
    ];
}

