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
use Illuminate\Support\Facades\DB;

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
    public function __construct()
    {
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

        if (isset($checkUsers)) {

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

        if (isset($checkEmail['errorCode'])):
            self::$response = [
                'errorCode' => 401,
                'errorMessage' => 'user exists'
            ];
        else:

            // Save new customer
            $api_token = $newUserData['api_token'];
            $otp = $newUserData['otp'];

            $newCustomer = UserRegistrationModel::create([
                'name' => $newUserData['name'],
                'username' => $newUserData['email'],
                'email' => $newUserData['email'],
                'password' => hash::make($newUserData['password']),
                'lastName' => $newUserData['lastName'],
                'registerDate' => $registrationDate,
                'lastvisitDate' => $registrationDate,
                'lastResetTime' => $registrationDate,
                'status' => 0,
                'activation' => $api_token,
                'api_token' => $api_token,
                'otp' => $otp,
                'identity' => $newUserData['identity'],
                'nationality' => '',
                'updateDateTime' => Carbon::now(),
                'gender' => '',
                'agreement' => '0'
            ]);

            $newUser = DB::table('vfq0g_users')->where('email', $newUserData['email'])->first();

            $activateEmail = ActivateEmailResource::activateEmail($newUser);
            // $activateEmail = 'sent';

            if ($activateEmail === 'sent'):


                // $newCustomer->save();
                self::$response = [
                    'successCode' => 209,
                    'successMessage' => "Thank you, New Account Successfully Registered",
                    'userData' => $newUser
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
