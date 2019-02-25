<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\viewmodels\RegisterViewModel;
use \Validator;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    //
    private $url = 'https://www.carloyi.com';

    /**
     * @param $api_token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate($api_token) {
        $activate = DB::table('vfq0g_users')
            ->where('api_token', $api_token)
            ->update(['activation' => $api_token]);

        return redirect()->away('https://www.carloyi.com');
    }

    public function register(Request $request)
    {
         $rules = [
          'email' => 'unique:user|required',
          'name' => 'required',
          'lastname' => 'required'
        ];

        $input = $request->only('email','name','lastname','password');

        //$validator = Validator::make($input, $rules);

        // if($validator->fails())
        // {
        //     return [
        //         'code' =>'-1',
        //         'error'=> 'Invalid input',
        //         'data' => $validator->messages()
        //     ];
        // }

        $model = new RegisterViewModel;
        $model->email = $request->input('email');
        $model->username = $request->input('email');
        $model->password = $request->input('password');
        $model->name = $request->input('name');
        $model->lastName = $request->input('lastName');
        $model->phone = $request->input('phone');
        $model->status = 0;


        $res = $model->register();

        return $res;
    }

    public static function login()
    {

    }

    public function getUsers() {
        return DB::table('vfq0g_users')->get();
    }

    public function getDealer() {
        return DB::table('vfq0g_dealers')->get();
    }
}
