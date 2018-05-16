<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\viewmodels\RegisterViewModel;
use Illuminate\Validation\Validator;


class AccountsController extends Controller
{
    //    
    public function register(Request $request)
    {
         $rules = [
          'email' => 'unique:user|required',
          'name' => 'required',
          'lastname' => 'required'  
        ];
        
        $input = $request->only('email','name','lastname','password');
        
        $validator = Validator::make($input, $rules);
        
        if($validator->fails())
        {
            return [
                'code' =>'-1',
                'error'=> 'Invalid input',
                'data' => $validator->messages()
            ];
        }
        
        $model = new RegisterViewModel;        
        $model->email = $request->input('email');
        $model->username = $request->input('email');
        $model->password = $request->input('password');
        $model->name = $request->input('name');
        $model->lastName = $request->input('lastName');        
        
        
        $res = $model->register();
        return $res;
    }
    
    public static function login()
    {
        
    }
    
}
