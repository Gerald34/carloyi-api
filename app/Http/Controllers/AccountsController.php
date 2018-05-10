<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\viewmodels\RegisterViewModel;

class AccountsController extends Controller
{
    //    
    public function register(Request $request)
    {
        $model = new RegisterViewModel;        
        $model->email = $request->input('email');
        $model->username = $request->input('email');
        $model->password = $request->input('password');
        $model->name = $request->input('name');
        $model->lastName = $request->input('lastName');        
        
        
        $res = $model->register();
        return $res;
    }
    
}
