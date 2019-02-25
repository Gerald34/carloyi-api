<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\DealerCars;
use App\DealersModel;
use App\AllCarsModel;
use App\DealerShowroomPosts;
use App\AllModelsModel;

class DealerCarsResource extends Resource
{
    private static $response;

    public static function saveCollection($dealerSelectedData)
    {
        if (DealerCars::where('model_id', $dealerSelectedData['model_id'])->exists()) {
            self::$response = [
                'errorCode' => 809,
                'errorMessage' => 'Car saved already'
            ];
        } else {
            
            // $dealerCars = DealerCars::create([
            //     'dealer_id' => $dealerSelectedData['dealer_id'],
            //     'model_id' => $dealerSelectedData['model_id'],
            //     'email' => $dealerSelectedData['email'],
            //     'availability' => $dealerSelectedData['availability']
            // ]);
            $dealerCars = new DealerCars;
            $increment = 1;
            $dealerCars->id = ++$increment;
            $dealerCars->dealer_id = $dealerSelectedData['dealer_id'];
            $dealerCars->model_id = $dealerSelectedData['model_id'];
            $dealerCars->email = $dealerSelectedData['email'];
            $dealerCars->availability = $dealerSelectedData['availability'];

            $dealerCars->save();
            $allDealerCars = DealerCars::where('dealer_id', $dealerSelectedData['dealer_id'])
                ->get();
            self::$response = [
                'successCode' => 208,
                'successMessage' => 'Car Successfully Saved',
                'allDealerCars' => self::getCars($allDealerCars[0]->dealer_id)
            ];
        }

        return self::$response;
    }

    public static function getCars($dealerID)
    {
        $find = DealerCars::where('dealer_id', $dealerID)->get();

        $allModels = [];
        foreach ($find as $models) {
            $allModels[] = $models->model_id;
        }

        $getModels = AllModelsModel::whereIn('id', $allModels)->get();

        return $getModels;
    }

    /**
     * @param $requestedCarInformation
     * @return array
     */
    public static function findCarDealers($requestedCarInformation)
    {

        $dealers = DealerCars::where('model_id', $requestedCarInformation->model_id)->get();

        $superDealer = DealersModel::where('email', 'mnqobimachi@gmail.com')->first();

        if (count($dealers) > 0) {

            self::$response = [
                'status' => 1,
                'dealers' => $dealers,
                'superDealer' => $superDealer
            ];

        } else {

            self::$response = [
                'status' => 2,
                'superDealer' => $superDealer
            ];
        }

        return self::$response;

    }

    /**
     * Save into dealer showroom
     * @param $requestData
     * @return array
     */
    public static function saveIntoDealerPosts($requestData)
    {
        $superDealer = DealersModel::where('email', 'mnqobimachi@gmail.com')->first();
        $requestedCarInformation = AllCarsModel::where('id', $requestData['cid'])->first();

        // return $requestedCarInformation;
        $findDealers = DealerCars::where('model_id', $requestedCarInformation->model_id)->get();

        if (count($findDealers) > 0) {
            // Save to available dealers
            foreach ($findDealers as $dealer) {
                DealerShowroomPosts::create([
                    'dealer_id' => $dealer->dealer_id,
                    'user_id' => $requestData['uid'],
                    'status' => 1,
                    'car_id' => $requestData['cid'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'extras' => $requestData['extras']
                ]);
            }

            // Save to super dealer
            DealerShowroomPosts::create([
                'dealer_id' => $superDealer->id,
                'user_id' => $requestData['uid'],
                'status' => 1,
                'car_id' => $requestData['cid'],
                'created_at' => date("Y-m-d H:i:s"),
                'extras' => $requestData['extras']
            ]);

            self::$response = [
                'status' => 1,
                'successCode' => 201,
                'successMessage' => 'sent to super dealer'
            ];

        } else {

            // Save to super dealer
            DealerShowroomPosts::create([
                'dealer_id' => $superDealer->id,
                'user_id' => $requestData['uid'],
                'status' => 1,
                'car_id' => $requestData['cid'],
                'created_at' => date("Y-m-d H:i:s"),
                'extras' => $requestData['extras']
            ]);

            self::$response = [
                'status' => 1,
                'successCode' => 201,
                'successMessage' => 'sent to super dealer'
            ];

        }

        return self::$response;

    }

    /**
     * Remove cars from dealer floor
     * @param $dealerSelectedData
     * @return array
     */
    public static function removeCollection($dealerSelectedData)
    {
        if (DealerCars::where('model_id', $dealerSelectedData['model_id'])->exists()) {
            self::$response = [
                'errorCode' => 809,
                'errorMessage' => 'Car saved already'
            ];
        } else {
            $removeCar = DealerCars::where('dealer_id');
            // $dealerCars->save();
            $allDealerCars = DealerCars::where('dealer_id', $dealerSelectedData['dealer_id'])
                ->get();
            self::$response = [
                'successCode' => 208,
                'successMessage' => 'Car Successfully Saved',
                'allDealerCars' => self::getCars($allDealerCars[0]->dealer_id)
            ];
        }

        return self::$response;
    }
}

