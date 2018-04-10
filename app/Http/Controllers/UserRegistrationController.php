<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Resources
use App\Http\Resources\UserRegistrationResource;

class UserRegistrationController extends Controller
{
    private $response;

    public function __construct() {

    }

    public function userRegistration(Request $request) {
        $newUserData = [
            'username' => strtolower(strip_tags(trim($request->input('username')))),
            'email' => strtolower(strip_tags(trim($request->input('email')))),
            'name' => strtolower(strip_tags(trim($request->input('name')))),
            'lastname' => strtolower(strip_tags(trim($request->input('lastname')))),
            'password' => strtolower(strip_tags(trim( $request->input('password'))))
        ];

        $this->response = UserRegistrationResource::checkIfUserExists($newUserData);

        return $this->response;
    }
}
