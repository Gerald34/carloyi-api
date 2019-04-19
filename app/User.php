<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public static function getActiveDealers()
    {
        /*SELECT id FROM carnetapi.vfq0g_users u
        inner join vfq0g_user_usergroup_map g 
        on u.id = g.user_id
        where u.block = 0 and  g.group_id = 11  
         *
         */
                
        $car_ids = DB::table('vfq0g_users AS u')
        ->join('vfq0g_user_usergroup_map AS g', 'u.id', '=', 'g.user_id') 
        ->where([
            'u.block' => 0,
            'g.group_id' =>11
        ])->select('u.id')
        ->get();
        
        return [
            'code' => count($car_ids) > 0 ? 1 : -1,
            'data' => $car_ids
        ];
        
    }
}
