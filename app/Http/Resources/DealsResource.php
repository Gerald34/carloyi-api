<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\DealsModel;
use App\DealerOffers;
use Illuminate\Support\Facades\DB;
use App\CarSearch;
use App\CarnetUsers;

class DealsResource extends Resource
{
    public static $response;
    private static $offer;

    public static function getDeals($offerID) {

        $find = DealsModel::where('offer_id', $offerID['offer_id'])->exists();

        if($find == true):
            self::$response = [
                'errorCode' => 604,
                'errorMessage' => 'Deal response sent.'
            ];
        else:
            self::$offer = DealerOffers::where('id', $offerID['offer_id'])->first();

            $data = response()->json(self::$offer);
            $the_deals = $data->original;

            $deals = [
                'user_id' => $the_deals->user_id,
                'dealer_id' => $the_deals->dealer_id,
                'offer_id' => $the_deals->id,
                'car_id' => $the_deals->car_id,
                'request_id' => $the_deals->request_id,
                'status' => 0,
                'created_at' => $the_deals->created_at,
                'updated_at' => $the_deals->updated_at,
            ];
            self::saveDeals($deals);
            self::$response = [
                'successCode' => 600,
                'succesMessage' => 'Response Sent to dealer.'
            ];

        endif;

        return self::$response;
    }

    public static function updateStatus($offerID) {
        $update = DealsModel::where('id', $offerID['offer_id'])->update(['status' => 1]);

        self::$response = [
            'successCode' => 900,
            'successMessage' => 'Interested response sent to dealer',
            'response' => $update
        ];

        return self::$response;
    }

    public static function rejectStatus($offerID) {
        $update = DealsModel::where('id', $offerID['offer_id'])->update(['status' => 2]);

        self::$response = [
            'successCode' => 800,
            'successMessage' => 'Offer Rejected',
            'response' => $update
        ];

        return self::$response;
    }

    private static function saveDeals($deals) {

        $deal = new DealsModel;

        $deal->user_id = $deals['user_id'];
        $deal->dealer_id = $deals['dealer_id'];
        $deal->offer_id = $deals['offer_id'];
        $deal->car_id = $deals['car_id'];
        $deal->request_id = $deals['request_id'];
        $deal->status = $deals['status'];
        $deal->created_at = $deals['created_at'];
        $deal->updated_at = $deals['updated_at'];
        $deal->save();

        return true;
    }

    public static function getDealerDeals($dealerID) {

        $deals = DB::table('vfq0g_dealer_user_posts')
        ->where('dealer_id', $dealerID)->get();

        if(count($deals) > 0):

            self::$response = [
                'successCode' => 1,
                'successMessage' => 'Dealer deals found',
                'data' => $deals
            ];
//            foreach($deals as $key) {
//            $user = CarnetUsers::where('id', $key->user_id)->first();
//            $car = CarSearch::getSearchCarsByIds_2($key->car_id);
//            // var_dump($car);
//
//            self::$response = [
//                'successCode' => 1,
//                'successMessage' => 'Dealer deals found',
//                'data' => [
//                    'cars' => $car
//                ]
//            ];
//            }

        else:
            self::$response = [
                'errorCode' => -1,
                'errorMessage' => 'No Deals Found'
            ];
        endif;

        return self::$response;
    }
}
