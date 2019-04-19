<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;
use App\NewsLetterModel;

class NewsLetterResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $response;

    public static function checkUser($newsLetterData) {
        $model = new NewsLetterModel;
        $users = NewsLetterModel::where('email', '=', $newsLetterData['email'])->exists();

        if($users == true) {
            self::$response = [
              'errorCode' => 405,
              'errorMessage' => 'Email already registered, please use different email address.'
            ];
        } else {
            $model->name = $newsLetterData['name'];
            $model->email = $newsLetterData['email'];
            $model->save($newsLetterData);
            self::$response = [
                'successCode' => 205,
                'successMessage' => 'Subscription Successful.'
            ];
        }

        return self::$response;
    }

}
