<?php

namespace App\viewmodels;

use Illuminate\Database\Eloquent\Model;

class UserDealerPostViewModel extends Model
{
    //
     public $table = "vfq0g_dealer_user_posts";
     protected $fillable = ['request_id','parent_id', 'dealer_id','user_id','car_id','offer','comment'];
     
    public static function getPost($id)
    {
        return self::find($id);
    }
}
