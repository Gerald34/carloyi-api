<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseUser extends Model
{
    //
     protected $table ="vfq0g_users";
     
     public static function getUderById($id)
     {
         return self::find($id);
         
     }
}
