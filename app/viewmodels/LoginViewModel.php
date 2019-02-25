<?php

namespace App\viewmodels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class LoginViewModel extends Model
{
    //
    public $table = 'vfq0g_users';
    public static $response;

    public static function DealerLogin($dealerData)
    {
        // return $dealerData;
        $findDealer = DB::table('vfq0g_dealers')
            ->where([
                'email' => $dealerData['email'], 'password' => $dealerData['password']
            ])->first();

        if (isset($findDealer)) {
            if ($findDealer->status === 1) {
                self::$response = [
                    'code' => 1,
                    'error' => '',
                    'user' => $findDealer
                ];
            } else {
                self::$response = [
                    'code' => -1,
                    'error' => 'You do not have permissions for this page'
                ];
            }
        } else {
            self::$response = [
                'code' => -1,
                'error' => 'User not found'
            ];
        }

        return self::$response;
    }

    public static function login($email, $password)
    {
        $entry = self::where(['email' => $email, 'password' => md5($password)])->first();

        if ($entry == null) {
            return [
                'code' => -1,
                'error' => 'User not found'
            ];
        }

        $roles = DB::table('vfq0g_user_usergroup_map AS g')
            ->join('vfq0g_usergroups AS p', 'g.group_id', '=', 'p.id')
            ->where('g.user_id', "=", $entry->id)
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
