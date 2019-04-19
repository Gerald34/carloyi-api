<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ActivateEmailResource extends Resource
{
    private static $response;

    public static function activateEmail($newUser)
    {
        if ($newUser == null) {
            self::$response = [
                'code' => -1,
                'error' => 'User not found'
            ];
        } else {
            self::$response = self::emailTemplate($newUser);
        }

        return self::$response;
    }

    public static function emailTemplate($newUser)
    {


        $body = "";

        // $to = "info@carloyi.com,gerald@coppertable.co.za,mnqobimachi@gmail.com," . $newUser->email;
        $to = $newUser->email;
        // $to = 'gerald@coppertable.co.za';
        $subject = 'Welcome to Carloyi Users';

        $message = "
<style>
.mailBody {
width: 90%;
min-width: 400px;
padding-top: 0;
padding-left: 0;
padding-right: 0;
padding-bottom: 0;
margin-left: auto;
margin-right: auto;
display: block;
background: #fff;
-webkit-box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
border-radius:0.4rem;
border:2px solid #fff;
overflow: hidden;
}

.carloyi {
background: #3d6983;
margin: 0;
padding: 0.7rem;
}

.helloMsg {
background: whitesmoke;
margin: 0;
font-size: 1.3rem;
font-weight: normal;
color: #3d6983;
padding: 0.7rem;
}

.mailcontent {
padding: 1.2rem;
color: #999999;
}

.mailHeader {
font-size: 1.3rem;
font-weight: normal;
}

.activate {
display: block;
padding: 0.7rem;
background: #3d6983;
color: whitesmoke;
font-weight: 500;
text-decoration: none;
margin-bottom: 0;
}
</style>

<div class='mailbody'>
<div class='carloyi'>

<a href='https://www.carloyi.com'>
<img src='https://www.carloyi.com/assets/images/carnet_logo_white_1.png'/>
</a>

</div>
<h1 class='helloMsg'>Hi " . ucfirst($newUser->name) . "</h1>
<div class='mailcontent'>
<h3 class='mailHeader'>Your account information</h3>
<p><bold>Firstname:</bold> " . ucfirst($newUser->name) . "</p>
<p><bold>Lastname:</bold> " . ucfirst($newUser->lastName) . "</p>
<p><bold>Username:</bold> " . $newUser->email . "</p>
<p>Welcome to the one-stop, convenience dealership</p></div>
</div>
";

        self::$response = self::sendEmail($to, $subject, $message, $body);

        return self::$response;
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
