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

    public function checkUserEmail(Request $request) {
        $newUserEmail = [
            'email' => strtolower(strip_tags(trim($request->input('email'))))
        ];
        $this->response = UserRegistrationResource::checkIfUserExists($newUserEmail);

        return $this->response;
    }

    public function userRegistration(Request $request) {
        $newUserData = [
            'username' => strtolower(strip_tags(trim($request->input('username')))),
            'email' => strtolower(strip_tags(trim($request->input('email')))),
            'name' => strtolower(strip_tags(trim($request->input('name')))),
            'lastname' => strtolower(strip_tags(trim($request->input('lastname')))),
            'password' => strtolower(strip_tags(trim( $request->input('password'))))
        ];

        $this->response = UserRegistrationResource::newUserRegistration($newUserData);

        return $this->response;
    }
}
