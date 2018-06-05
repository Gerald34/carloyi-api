<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\DealerCars;

class DealerCarsResource extends Resource
{
    private static $response;
    public static function saveCollection($dealerSelectedData) {

        if (DealerCars::where('model_id', $dealerSelectedData['model_id'])->exists()) {
            self::$response = [
                'errorCode' => 809,
                'errorMessage' => 'Car saved already'
                ];
        } else {
            $dealerCars = DealerCars::create([
                'dealer_id' => $dealerSelectedData['dealer_id'],
                'model_id' => $dealerSelectedData['model_id']
            ]);
            $dealerCars->save();
            $allDealerCars = DealerCars::
            where('dealer_id', $dealerSelectedData['dealer_id'])
            ->get();
            self::$response = [
                'successCode' => 208,
                'successMessage' => 'Car Successfully Saved',
                'allDealerCars' => $allDealerCars
            ];
        }

        return self::$response;
    }
}
