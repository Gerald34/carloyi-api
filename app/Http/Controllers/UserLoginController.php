<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


// Resources
use App\Http\Resources\UserLoginResource;

class UserLoginController extends Controller
{
  public $response; // all return json responses
  public $log; // system logger

    public function userSignIn(Request $request) {
      $userInput = [
        'email' => $request->input('email'),
        'password' => $request->input('password')
      ];

      $this->response = UserLoginResource::findUser($userInput);

      return $this->response;
    }
}
