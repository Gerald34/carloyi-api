<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Facades
use Illuminate\Support\Facades\DB;

// Resources
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\GeneratePasswordTokenResource;
use App\Http\Resources\GeneratePasswordOTPResource;


class UserLoginController extends Controller
{
  public $response; // all return json responses
  public $log; // system logger

  public function userSignIn(Request $request)
  {

    $userInput = [
      'email' => $request->input('email'),
      'password' => $request->input('password')
    ];

    $this->response = UserLoginResource::findUser($userInput);

    return $this->response;
  }

  public function newPasswordToken(Request $request) {

      $email = $request->input('email');

      $uniqueToken = GeneratePasswordTokenResource::generatePasswordToken($email);
      $uniqueOTP = GeneratePasswordOTPResource::generateUniqueOtp();

      $storeToken = DB::table('vfq0g_users')
          ->where('email', $email)
          ->update(['passwordToken' => $uniqueToken]);

      $storeOTP = DB::table('vfq0g_users')
          ->where('email', $email)
          ->update(['uniqueOTP' => $uniqueOTP]);


      if ($storeToken === 1 && $storeOTP === 1) {

          $userData = DB::table('vfq0g_users')->where('email', $email)->first();
          $userInfoJson = response()->json($userData);
          $userInfoSystemData = $userInfoJson->original;

          $otpEmail = GeneratePasswordOTPResource::emailOTP($userInfoSystemData);

          if ($otpEmail === 'sent') {
              $this->response = [
                  'successCode' => 204,
                  'successMessage' => 'Token Successfully Generated',
                  'userName' => $userData->name,
                  'passwordToken' => $userData->passwordToken
              ];
          } else {
              $this->response = [
                  'errorCode' => 804,
                  'errorMessage' => 'System Error, Please try again'
              ];
          }

      } else {
          $this->response = [
              'errorCode' => 504,
              'errorMessage' => 'Token not generated'
          ];
      }

      return $this->response;

  }
}
