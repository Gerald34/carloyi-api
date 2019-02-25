<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PushNotificationResource;

class PushNotificationController extends Controller
{
    public function subscribePushNotification(Request $request) {
        $subscriptionData = [
            'subscription' => $request->input('subscription'),
            'userID' => $request->input('userid')
        ];
	
        return PushNotificationResource::saveSubscription($subscriptionData);
    }

    public function offerPushMessage(Request $request) {
        $userID = $request->input('userID');

        return PushNotificationResource::sendOfferPushMessage($userID);
    }

    public function requestPushMessage(Request $request) {
        $dealerID = $request->input('dealerID');

        return PushNotificationResource::sendDealerPushNotification($dealerID);
    }
}

