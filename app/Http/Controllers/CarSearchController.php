<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use \Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\CarSearch;
use App\viewmodels\CarSearchFilters;
use Illuminate\Support\Facades\DB;
/**
 * Description of CarSearchController
 *
 * @author macsox
 */
class CarSearchController extends Controller {
    //put your code here

    public function specific($id)
    {
        return CarSearch::searchByModel($id);
    }

    public function affordability(Request $request)
    {
        return "affodability";
    }

    public function getFilterOptions()
{
    $car_types  =DB::table('vfq0g_sobipro_field_option')
            ->where([
                'fid' => 4
            ])->select('optValue')
            ->get();
    $clean_car_types = [];
    foreach($car_types as $key => $val)
    {
        $clean_car_types[] = $val->optValue;
    }
    return
    [
        'code' => 1,
        'data' =>
        [
            'car_types' => $clean_car_types
        ]
    ];
}

    public function filter(Request $request)
    {

        $rules = [
            'car_type' => 'required',
        ];

//        $input = $request->only('car_type');

//        $validator = Validator::make($input, $rules);
//
//        if($validator->fails()) {
//            return [
//                'code' =>'-1',
//                'error'=> 'Invalid filter options',
//                'data' => $validator->messages()
//            ];
//        }

        $model = [
            "price_max" => $request->input('price_max'),
            "price_min" => $request->input('price_min'),
            "car_type" => $request->input('car_type'),
            "fuel_consumption" => $request->input('fuel_consumption'),
            "practicality" => $request->input('practicality'),
            "comfort" => $request->input('comfort'),
            "enjoyment" => $request->input('enjoyment'),
            "performance" => $request->input('performance'),
            "reliability" => $request->input('reliability'),
            "city_driving" => $request->input('city_driving'),
            "long_distance" => $request->input('long_distance'),
            "moving_luggage" => $request->input('moving_luggage'),
            "seats" => $request->input('seats'),
            "offroad" => $request->input('offroad')
        ];


        return CarSearch::searchByFilters($model);

    }

}
