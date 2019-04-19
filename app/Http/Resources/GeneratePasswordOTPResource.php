<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GeneratePasswordOTPResource extends Resource
{
    public static $response;

    public static function generateUniqueOtp() {
        $x = 5;
        return substr(str_shuffle("0123456789"), 0, $x);
    }

    public static function emailOTP($userInfoSystemData) {

        $to = $userInfoSystemData->email;
        $subject = "New Password OTP";
        $message = "Hi " . $userInfoSystemData->name . " OTP successfully generated" . "\r\n";
        $message .= $userInfoSystemData->uniqueOTP;
        $body = "";

        return self::sendEmail($to, $subject, $message, $body);
    }

    public static function sendEmail($to, $subject, $message, $body)
    {
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <info@carloyi.com>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        if (mail($to, $subject, $message, $headers, $body)) {
            self::$response = 'sent';
        } else {
            self::$response = 'not sent';
        }

        return self::$response;
    }
}
