<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\UserRegistrationModel;

// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;

class UserRegistrationResource extends Resource
{
    private static $response; // all return json responses
    private static $systemData; // all users data
    public $log; // system logger

    public function __construct() {
        // create a log channel
        $this->log = new Logger('registration');
        $this->log->pushHandler(new RotatingFileHandler('/var/log/the-car-net_logs/carnet_registration.log', Logger::INFO));
        $this->log->pushHandler(new FirePHPHandler());
    }

    public static function checkIfUserExists($newUserData) {
        $checkUsers = UserRegistrationModel::where('email', $newUserData['email'])->first();

        if(isset($checkUsers)) {
            self::$response = [
                'errorCode' => 401,
                'errorMessage' => 'user exists'
            ];
        } else {
            self::$response = [
              'successCode' => 210,
              'successMessage' => 'Email still available'
            ];
        }

        return self::$response;
    }
}
