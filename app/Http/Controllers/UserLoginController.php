<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserLoginController extends Controller
{
    public function userSignIn(Request $request) {
      $userInput = [
        'email' => $request->input('email'),
        'password' => $request->input('password')
      ];

      dd($userInput);
    }
}
