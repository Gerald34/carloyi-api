<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\PushNotificationModel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class PushNotificationResource extends Resource
{
    public static $response;

    /**
     * 1st check if user exists
     * Compare browser endpoint
     * Database { table: app_notifications }
     * @param $subscriptionData
     * @return array
     */
    public static function saveSubscription($subscriptionData) {
        $checkUserData = PushNotificationModel::where('userid', $subscriptionData['userID'])->first();

        if (!empty($checkUserData)) {
           if($checkUserData->endpoint === $subscriptionData['subscription']['endpoint'] &&
               $checkUserData->auth === $subscriptionData['subscription']['keys']['auth']) {
               self::$response = [
                   'successCode' => 400,
                   'successMessage' => 'Already subscribed'
               ];
           } else {
               self::$response = self::_updatePushEndpoint($subscriptionData);
           }
        } else {
            self::_subscribeNewUser($subscriptionData);
        }

        return self::$response;
    }

    /**
     * Update user subscription data
     * Database { table: app_notifications }
     * @param $subscriptionData
     * @return array
     */
    private static function _updatePushEndpoint($subscriptionData) {

        $updateData = [
            'endpoint' => $subscriptionData['subscription']['endpoint'],
            'auth' => $subscriptionData['subscription']['keys']['auth'],
            'p256dh' => $subscriptionData['subscription']['keys']['p256dh'],
            'updated_at' => Carbon::now()
        ];

        $update = PushNotificationModel::where('userid', $subscriptionData['userID'])
            ->update($updateData);

        if($update === 1) {
            self::$response = [
                'successCode' => 400,
                'successMessage' => 'updated'
            ];
self::sendSubscriptionPushMessage($subscriptionData);
        } else {
            self::$response = [
                'successCode' => 500,
                'successMessage' => 'Could not update'
            ];
        }

        return self::$response;
    }

    /**
     * Save new user into the table
     * Database { table: app_notifications }
     * @param $subscriptionData
     * @return array
     */
    private static function _subscribeNewUser($subscriptionData) {
        $saveNewUser = new PushNotificationModel;
        $saveNewUser->userid = $subscriptionData['userID'];
        $saveNewUser->endpoint = $subscriptionData['subscription']['endpoint'];
        $saveNewUser->auth = $subscriptionData['subscription']['keys']['auth'];
        $saveNewUser->p256dh = $subscriptionData['subscription']['keys']['p256dh'];
        $saveNewUser->created_at = Carbon::now();
        $saveNewUser->updated_at = Carbon::now();
        $saveNewUser->save();

        self::$response = [
            'successCode' => 400,
            'successMessage' => 'New Record Inserted'
        ];
self::sendSubscriptionPushMessage($subscriptionData);
        return self::$response;
    }

    private static function sendSubscriptionPushMessage($subscriptionData) {
        $client = new Client(['verify' => base_path('cacert.pem')]);
        $client->post('https://node.carloyi.com:8080/subscription',
            [ RequestOptions::JSON =>
                [
                    'subscription' => [
                       'endpoint' => $subscriptionData['subscription']['endpoint'],
                        'auth' => $subscriptionData['subscription']['keys']['auth'],
                        'p256dh' => $subscriptionData['subscription']['keys']['p256dh']
                    ]
                ]
            ]);
    }

    /**
     * Send push notification
     * Node Sever
     * URI: https://154.66.197.198
     * Port: 8080
     * @param $userID
     */
    public static function sendPushMessage($userID) {
        $send = [ 'userID' => $userID ];
        $client = new Client([
            // 'base_uri' => 'https://api.carloyi.com',
//            'base_uri' => '154.66.197.198',
            'verify' => base_path('cacert.pem'),
        ]);
        $client->post(
            'https://node.carloyi.com:8080/sendOfferNotification',
            [ RequestOptions::JSON => [ 'userID' => $userID ] ]);
    }
    
    /**
     * Send push notification
     * Node Sever
     * URI: https://154.66.197.198
     * Port: 8080
     * @param $userID
     * @param $carImage
     * @param $carName
     */
    public static function sendOfferPushMessage($userID, $carImage, $carName) {

        $client = new Client([
            'base_uri' => 'http://127.0.0.1:8000',
            'verify' => base_path('cacert.pem'),
        ]);
        $client->post(
            'https://node.carloyi.com:8080/sendOfferNotification',
            [ RequestOptions::JSON =>
                [
                    'userID' => $userID,
                    'car_image' =>  $carImage,
                    'carName' => $carName
                ]
            ]);
    }

        /**
     * Send Dealer Notification
     * Node Sever
     * URI: https://154.66.197.198
     * Port: 8080
     * @param $dealerID
     * @param $carImage
     * @param $carName
     */
    public static function sendDealerPushNotification($dealerID, $carImage, $carName) {

        $dealerSubscriptions = PushNotificationModel::where('userid', $dealerID)->first();
        $client = new Client(['verify' => base_path('cacert.pem')]);
        $client->post(
            'https://node.carloyi.com:8080/sendDealerNotification',
            [ RequestOptions::JSON =>
                [
                    'endpoint' => $dealerSubscriptions->endpoint,
                    'auth' => $dealerSubscriptions->auth,
                    'p256dh' => $dealerSubscriptions->p256dh,
                    'dealerID' => $dealerID,
                    'car_image' =>  $carImage,
                    'carName' => $carName
                ]
            ]);
    }

}

