<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
use App\CarnetUsers;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;

class UserLoginResource extends Resource
{
  private static $response; // all return json responses
  private static $systemData; // all users data
  public $log; // system logger

  public function __construct() {
    // create a log channel
    $this->log = new Logger('signin');
    $this->log->pushHandler(new RotatingFileHandler('/var/log/the-car-net_logs/carnet_signin.log', Logger::INFO));
    $this->log->pushHandler(new FirePHPHandler());
}

  public static function findUser($userInput) {

    self::$systemData = CarnetUsers::where('email',$userInput['email'])->first();
    $userInfoJson = response()->json(self::$systemData);
    $userInfoSystemData = $userInfoJson->original;

    if(isset(self::$systemData)) {
      self::$response = self::checkPassword($userInfoSystemData ,$userInput);
    } else {
      self::$response = [
        'errorCode' => 405,
        'errorMessage' => 'User not found'
      ];
    }

    return self::$response;
  }

  public static function checkPassword($userInfoSystemData, $userInput) {

    if(isset($userInfoSystemData->password)){
        $lastVisitTime = Carbon::now();

        self::$response = [
            'successCode' => 201,
            'userData' => $userInfoSystemData,
            'data' => [
              $userInfoSystemData
            ],
            'successMessage' => 'Successful login',
            'offers' => null,
            'lastVisitDate' => $lastVisitTime
        ];

        self::updateLoginTime($lastVisitTime, $userInfoSystemData);

    } else {
        self::$response = [
            'errorCode' => 501,
            'errorMessage' => 'Email and Password do not match.'
        ];
    }

    return self::$response;

  }

  public static function updateLoginTime($lastVisitTime, $userInfoSystemData) {
      $updateTime = CarnetUsers::find($userInfoSystemData->id);
      $updateTime->lastvisitDate = $lastVisitTime;
      $updateTime->save();
  }

}
