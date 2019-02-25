<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
// Resources
use App\Http\Resources\UserRegistrationResource;

/**
 * Class UserRegistrationController
 * @package App\Http\Controllers
 */
class UserRegistrationController extends Controller
{
    /**
     * @var
     */
    private $response;

    /**
     * UserRegistrationController constructor.
     */
    public function __construct() {

    }

    /**
     * @param Request $request
     * @return array
     */
    public function checkUserEmail(Request $request) {

        if ( $request->input('email') !== null || !empty($request->input('email'))) {
            $newUserEmail = [
                'email' => strtolower(strip_tags(trim($request->input('email'))))
            ];

            $checkEmail = UserRegistrationResource::checkIfUserExists($newUserEmail);

            if (!isset($checkEmail['errorCode'])) {
                $this->response = $this->userRegistration($request);
            } else {
                $this->response = [
                    'errorCode' => 401,
                    'errorMessage' => 'user exists'
                ];
            }
        } else {
            $this->response = [
                'errorCode' => 501,
                'errorMessage' => 'Email Required'
            ];
        }

        return $this->response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function userRegistration(Request $request)
    {

        $newUserData = [
            'username' => strtolower(strip_tags(trim($request->input('username')))),
            'email' => strtolower(strip_tags(trim($request->input('email')))),
            'name' => strtolower(strip_tags(trim($request->input('name')))),
            'lastName' => strtolower(strip_tags(trim($request->input('lastName')))),
            'password' => strtolower(strip_tags(trim( $request->input('password')))),
            'phone' => strtolower(strip_tags(trim( $request->input('phone')))),
            'api_token' => str_random(60),
            'otp' => mt_rand(100000, 999999),
            'identity' => ''
        ];

        $validation = $this->validateData($newUserData);

        if(isset($validation['errorCode'])) {
            $this->response = $validation;
        } else {
            $this->response = UserRegistrationResource::newUserRegistration($newUserData);
        }

        return $this->response;
    }

    private function validateData($newUserData) {

        $this->response[] = '';

        if (empty($newUserData['email'])) {
            $this->response = ['errorCode' => 555, 'errorMessage' => 'Email Required'];
        }

        if (!filter_var($newUserData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response = ['errorCode' => 556, 'errorMessage' => 'Email Format Incorrect'];
        }

        if (empty($newUserData['name'])) {
            $this->response = ['errorCode' => 557, 'errorMessage' => 'Name Required'];
        }

        if (empty($newUserData['lastName'])) {
            $this->response = ['errorCode' => 558, 'errorMessage' => 'Last name Required'];
        }

        if (empty($newUserData['password'])) {
            $this->response = ['errorCode' => 559, 'errorMessage' => 'Password Required'];
        }

        if (strlen($newUserData['password']) < 6 ) {
            $this->response = ['errorCode' => 560, 'errorMessage' => 'Password must be at least 6 characters'];
        }

        if (!is_numeric($newUserData['phone'])) {
            $this->response = ['errorCode' => 561, 'errorMessage' => 'Phone Number Format Incorrect'];
        }

        return $this->response;

    }
}
