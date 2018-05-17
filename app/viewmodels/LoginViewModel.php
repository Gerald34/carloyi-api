<?php

namespace App\viewmodels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class LoginViewModel extends Model
{
    //
    public $table = 'vfq0g_users';
    
    
    
    public static function login($email, $password)
    {
        $entry = self::where(['email' => $email, 'password' => md5($password) ])->first();
        
        if($entry == Null)
        {
            return[
                'code' => -1,
                'error' => 'User not found'
            ];
        }
        
        $roles = DB::table('vfq0g_user_usergroup_map AS g')
            ->join('vfq0g_usergroups AS p', 'g.group_id', '=', 'p.id')
            ->where('g.user_id',"=",$entry->id)            
            ->select('g.group_id', 'p.title')
            ->get();
        
        $data = [
            'code' => 1,
            'error' => '',
            'user' => $entry,
            'roles' => $roles 
        ];
        
        return $data;
    }
    
}
