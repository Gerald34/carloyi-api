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
// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Facades\Log;
/**
 * Description of CarSearchController
 *
 * @author macsox
 */
class CarSearchController extends Controller
{
    

    public function __construct()
    {
        // create a log channel
        $this->logModelSearch = new Logger('Search');
        $this->logModelSearch->pushHandler(new RotatingFileHandler('../storage/logs/car_search.log'));
       $this->logModelSearch->pushHandler(new FirePHPHandler());
    }

    public $response;

    public function randomCars(Request $request) {
        $randomID = [$request->input('car_1'), $request->input('car_2')];
        $randomCars = DB::table('vfq0g_allcars')->whereIn('id', $randomID)->get();

        return $randomCars;
    }

    public function randomCarsFour(Request $request) {
        $randomID = [
            $request->input('car_1'),
            $request->input('car_2'),
            $request->input('car_3'),
            $request->input('car_4'),
        ];
        $randomCars = DB::table('vfq0g_allcars')->whereIn('id', $randomID)->get();

        return $randomCars;
    }

    /**
     * Get All Cars Search
     */
    public function getAllCars()
    {
                $results = DB::table('vfq0g_allcars')
            // ->where('approved', 1)
            ->orderBy('total_score', 'desc')
            ->get();

        $list = array();
        foreach ($results as $item) {
            if ($item->approved === 1) {
                isset($list[$item->model_id]) or $list[$item->model_id] = $item;
            }
        }

        $iteratedArrays = [];
        foreach ($list as $car) {
            $iteratedArrays[] = $car;
        }

        return $iteratedArrays;
    }

    /**
     * Get Selected Car Information
     */
    public function getCarInfo($carID)
    {
        $all = DB::table('vfq0g_allcars')
            ->where('id', $carID)
            ->get();

        $modelID = [
            'successCode' => 207,
            'successMessage' => 'Full Car Information Found',
            'carInformation' => $all
        ];

        return $modelID;
    }

    /**
     * @param $id
     * @return array
     */
    public function specific($id)
    {

        $carModel = CarSearch::searchByModel($id);

        // Log Success Response
        if ($carModel['code'] === 1) {
            $carNames[] = '';
            foreach($carModel['data'] as $carName) {
                $carNames[] = $carName->name;
            }

            Log::info(json_encode(["car_search_results" => $carNames, "model_id" => $id]));

            $this->logModelSearch->info(
                json_encode(["model_id" => $id ]),
                ["search_return" => $carNames],
                json_encode(["Car Type" => $carModel['car_type']])
            );

        }

        return $carModel;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function affordability(Request $request)
    {
        return "affodability";
    }

    /**
     * @return array
     */
    public function getFilterOptions()
    {
        $car_types = DB::table('vfq0g_car_types')->get();

        return
            [
            'code' => 1,
            'data' =>
                [
                'car_types' => $car_types
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function byType(Request $request)
    {
                $typeArray[] = $request->input('car_type');

        $newArray = [];

        foreach ($typeArray[0] as $type) {
            $newArray[] = $type;
        }

        $searchData = [
            'car_type' => $newArray,
            'max_price' => $request->input('max_price')
        ];

        $this->response = CarSearch::getByCarType($searchData);
        return $this->response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function filter(Request $request)
    {

                $rules = [
            'car_type' => 'required',
        ];

        $typeArray[] = $request->input('car_type');

        $newArray = [];

        foreach ($typeArray[0] as $type) {
            $newArray[] = $type;
        }

        if (!empty($typeArray)) {

            $model = [
                "price_max" => $request->input('price_max'),
                "price_min" => $request->input('price_min'),
                "car_type" => $typeArray[0],
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

            // return $model;

            $this->logModelSearch->info(
                "Filter Search", ["search_query" => $model],[]
            );

            $this->response = CarSearch::searchByFilters($model);
        } else {
            $this->response = [
                'errorCode' => 305,
                'errormessage' => 'Please select at least 1 car type'
            ];
        }

        return $this->response;

    }

    /**
     *
     */
    public function getAll()
    {

    }

}
