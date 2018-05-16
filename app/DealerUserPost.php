<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DealerUserPost extends Model
{
    //
    public $table = "vfq0g_dealer_user_posts";
    
    
    
public static function getPostsByRequestID($id)
{
    
    
    $posts = self::where(['request_id' => $id])->get();
    
    return $posts;
    
    
}
    
}


