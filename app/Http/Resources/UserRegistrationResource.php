<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\UserRegistrationModel;
use App\Http\Resources\ActivateEmailResource;
// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;

/**
 * Class UserRegistrationResource
 * @package App\Http\Resources
 */
class UserRegistrationResource extends Resource
{
    private static $response; // all return json responses
    public $log; // system logger

    /**
     * UserRegistrationResource constructor.
     */
    public function __construct() {
        // create a log channel
        $this->log = new Logger('registration');
        $this->log->pushHandler(new RotatingFileHandler('/var/log/the-car-net_logs/carnet_registration.log', Logger::INFO));
        $this->log->pushHandler(new FirePHPHandler());
    }

    /**
     * @param $newUserEmail
     * @return array
     */
    public static function checkIfUserExists($newUserEmail)
    {
        $checkUsers = UserRegistrationModel::where('email', $newUserEmail['email'])->first();

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

    /**
     * @param $newUserData
     * @return array
     */
    public static function newUserRegistration($newUserData)
    {
        $registrationDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
        $checkEmail = self::checkIfUserExists($newUserData);

        if(isset($checkEmail['errorCode'])):
            self::$response = [
                'errorCode' => 401,
                'errorMessage' => 'user exists'
            ];
        else:
            $newCustomer = UserRegistrationModel::create([
                'name' => $newUserData['name'],
                'username' => $newUserData['username'],
                'email' => $newUserData['email'],
                'password' => hash::make($newUserData['password']),
                'lastName' => $newUserData['lastname'],
                'registerDate' => $registrationDate,
                'lastvisitDate' => $registrationDate,
                'lastResetTime' => $registrationDate
            ]);

            // Save new customer
            $newCustomer->save();
            if(ActivateEmailResource::activateEmail($newUserData) == 1):
                self::$response = [
                    'successCode' => 209,
                    'successMessage' => "Thank you, activation email has been sent to {$newUserData['email']}"
                ];
            else:
                self::$response = [
                    'errorCode' => 408,
                    'errorMessage' => 'user registered'
                ];
            endif;

        endif;

        return self::$response;
    }

}
