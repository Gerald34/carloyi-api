<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\viewmodels\CarSearchFilters;

// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use App\AllCarsModel;
/**
 * Description of CarSearch
 *
 * @author macsox
 */
class CarSearch extends Model
{
    //put your code here
    /*
     * FIELD OPTIONS
      '1','field_name'
      '2','field_category'
      '3','field_price'
      '4','field_car_type'
      '5','field_city_driving'
      '6','field_carrying_people'
      '7','field_long_distance_driving'
      '8','field_off_roading'
      '10','field_moving_luggage'
      '11','field_fuel_efficiency'
      '12','field_enjoyment'
      '13','field_practicality'
      '14','field_performance'
      '15','field_comfort'
      '16','field_reliability'
      '17','field_car_image'
      '18','field_total_score'
      '19','field_verdict'
      '20','field_engine_type'
      '21','field_power_kw'
      '22','field_torque_nm'
      '23','field_acceleration_0_100'
      '24','field_consumption_l_100km'
      '25','field_gallery_images'
     */

    public static $response;

    /**
     * Search By Price
     * @param $amount
     * @return array
     */
    public static function byPrice($amount)
    {
        $carsByPrice = DB::table('vfq0g_allcars')
            ->where('price', '<', $amount['price'])
            // ->orderBy('total_score', 'desc')
            ->get();

        $modelID = [];
        foreach ($carsByPrice as $cars) {
            $modelID[$cars->model_id] = $cars;
        }

        self::$response = [
            'successCode' => 204,
            'data' => $modelID
        ];

        return self::$response;
    }

    /**
     * Search By Price 2
     * @param $affordablePrice
     * @return array
     */
    public static function searchCarsByprice($affordablePrice)
    {
        // return $affordablePrice;
        $carsByPrice = DB::table('vfq0g_allcars')
            ->where('approved', 1)
            ->where('price', '<', $affordablePrice)
            ->orderBy('total_score', 'desc')
            ->get();

        $list = array();
        foreach ($carsByPrice as $item) {
            if ($item->approved === 1) {
                isset($list[$item->model_id]) or $list[$item->model_id] = $item;
            }
        }

        $iteratedArrays = [];
        foreach ($list as $car) {
            $iteratedArrays[] = $car;
        }

        // $modelID = [];

        // foreach ($carsByPrice as $cars) {
        //     $modelID[$cars->model_id] = $cars;
        //     // $modelID[$cars->total_score] = $cars;
        // }

        // $sorted = self::sortCars($modelID);

        // return $modelID;
        self::$response = [
            'successCode' => 201,
            'data' => $iteratedArrays,
            'finance_amount' => $affordablePrice
        ];

        return self::$response;
    }

    public static function sortCars($modelID)
    {
        usort(
            $modelID,
            function ($a, $b) {
                return $a['total_score'] <=> $b['total_score'];
            }
        );
    }


    /**
     * @param $key
     * @param $data
     * @return array
     */
    public static function remove_duplicateKeys($key, $data)
    {
        return $key;
        $_data = array();

        foreach ($data as $v) {
            if (isset($_data[$v[$key]])) {
                // found duplicate
                continue;
            }
            // remember unique item
            $_data[$v[$key]] = $v;
        }

        // if you need a zero-based array
        // otherwise work with $_data
        $data = array_values($_data);
        return $data;
    }

    /**
     * @param $model
     * @return array
     */
    public static function searchByModel($model)
    {
        $cars = DB::table('vfq0g_allcars')
	    ->select('id', 'name', 'price', 'car_type', 'approved', 'total_score', 'car_image')
            ->where('model_id', $model)
            ->where('approved', 1)
            ->get();
        if (count($cars) == 0) {
            self::$response = [
                'code' => -1,
                'error' => 'No cars found',
                'data' => []
            ];
        } else {

            $types = [];
	    $prices = [];
            foreach ($cars as $car) {
		$prices[] = $car->price;
                $types[] = $car->car_type;
            }

            $result = array_unique($types);

            $thisType[] = DB::table('vfq0g_allcars')->whereIn('car_type', $result)->get();

            // Get max price from price array
            $maxPrice = array_reduce($prices, function ($a, $b) {
                return @$a['price'] > $b['price'] ? $a : $b ;
            });

            self::$response = [
                'code' => 1,
                'error' => '',
                'type' => 1,
                'model' => $model,
                'data' => $cars,
                'car_type' => $result,
		'max_price' => $maxPrice
            ];


        }

        return self::$response;
    }

    /**
     * @param $ids
     * @param array $options
     * @return array
     */
    public static function getSearchCarsByIds($ids, $options = array())
    {
        return self::_getSearchCarsByIds($ids);
    }

    /**
     * @param $ids
     * @param array $options
     * @return array
     */
    public static function getSearchCarsByIds_dealer($ids, $options = array())
    {
        return self::_getSearchCarsByIds_dealer($ids);
    }

    /**
     * @param $ids
     * @param array $options
     * @return mixed
     */
    public static function getSearchCarsByIds_2($ids, $options = array())
    {
        return self::_getSearchCarsByIds_2(arsort($ids));
    }

    /**
     * Search Car Type
     * @param $searchData
     * @return array
     */
    public static function getByCarType($searchData)
    {

        $selectedTypes = $searchData['car_type'];
        $maxPrice = ceil($searchData['max_price'] / 100000) * 100000;

        $allTypes = DB::table('vfq0g_allcars')
            ->whereIn('car_type', $selectedTypes);

        $allTypes->orderBy('total_score', 'desc');
        $results = $allTypes->get();

        if ($allTypes !== null) {
            $sortedByRating = [];

            $list = array();
            foreach ($results as $item) {
                if ($item->approved === 1 && $item->price < $maxPrice) {
                    isset($list[$item->model_id]) or $list[$item->model_id] = $item;
                }
            }

            $iteratedArrays = [];
            foreach ($list as $car) {
                $iteratedArrays[] = $car;
            }

            $response = [
                'successCode' => 206,
                'successMessage' => 'cars found',
                'data' => $iteratedArrays,
                'maxPrice' => $maxPrice
            ];

        } else {
            $response = [
                'errorCode' => 700,
                'errorMessage' => 'No cars were found'
            ];
        }

        return $response;
    }

    public static function super_unique($results, $key)
    {
        $temp_array = [];
        foreach ($results as &$v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] = &$v;
        }
        $results = array_values($temp_array);
        return $array;
    }

    /**
     * Filter Search
     * @param $model
     * @return array
     */
    public static function searchByFilters($model)
    {

        $car_type = $model['car_type'];

        // return $car_type;

        $query = AllCarsModel::whereIn('car_type', $car_type);
        $query->orderBy('total_score', 'desc');

        if ($model['price_min']) {
            $query->where('price', '>', $model['price_min']);
        }

        if ($model['price_max']) {
            $query->where('price', '<', $model['price_max']);
        }

        if ($model['seats'] !== null) {
            $query->where('carrying_people', '>=', $model['seats']);
        }

        $results = $query->get();

        if (count($results) == 0) {

            self::$response = [
                'code' => -1,
                'error' => 'No cars were found',
            ];

        } else {

            $car_points = [];

            foreach ($results as $entry) {
                $points = 0;
                $points += (isset($model["city_driving"])) ? $entry->city_driving * $model["city_driving"] : 0;
                $points += (isset($model["long_distance"])) ? $entry->long_distance_driving * $model["long_distance"] : 0;
                $points += (isset($model["off_road"])) ? $entry->off_road * $model["off_road"] : 0;
                $points += (isset($model["moving_luggage"])) ? $entry->moving_luggage * $model["moving_luggage"] : 0;
                $points += (isset($model["fuel_capacity"])) ? $entry->fuel_capacity * $model["fuel_capacity"] : 0;
                $points += (isset($model["enjoyment"])) ? $entry->enjoyment * $model["enjoyment"] : 0;
                $points += (isset($model["practicality"])) ? $entry->practicality * $model["practicality"] : 0;
                $points += (isset($model["performance"])) ? $entry->performance * $model["performance"] : 0;
                $points += (isset($model["comfort"])) ? $entry->comfort * $model["comfort"] : 0;

                // $points += (isset($model[ "reliability"]))? $entry->reliability * $model[ "reliability"] : 0;
                $car_points[$entry->id] = $points;
            }

            arsort($car_points);

            $len = count($car_points) > 1000 ? 1000 : count($car_points);
            $final_car_ids = [];
            $i = 0;

            foreach ($car_points as $key => $entry) {

                $savePoints = DB::table('vfq0g_allcars')
                    ->where('id', $key)
                    ->update(['points' => $entry]);

                // return $savePoints;

                $final_car_ids[] = $key;
                $i++;
                if ($i >= $len) {
                    break;
                }
            }

            self::$response = self::getSearchCarsByIds($final_car_ids);
        }

        return self::$response;
    }

    /**
     * @param $ids
     * @return array
     */
    private static function _getSearchCarsByIds($ids)
    {
        self::$response = [];

        $results = DB::table('vfq0g_allcars')
            ->whereIn('id', $ids)
            ->where('approved', 1)
            ->orderBy('points', 'desc')
            ->orderBy('total_score', 'desc')
            ->get();

        $filtered = self::filterByApproval($results);

        $allTypes = [];

        foreach ($filtered as $type) {
            $allTypes[] = $type->car_type;
        }

        $thisType = DB::table('vfq0g_car_types')->whereIn('id', $allTypes)->get();

        self::$response = [
            'successCode' => 1,
            'data' => $filtered,
            'type' => $thisType
        ];

        return self::$response;
    }

    private static function _getSearchCarsByIds_dealer($ids)
    {
        self::$response = [];

        $results = DB::table('vfq0g_allcars')
            ->whereIn('id', $ids)
            ->orderBy('points', 'desc')
            ->orderBy('total_score', 'desc')
            ->get();

        $filtered = self::filterByApproval($results);

        $allTypes = [];

        foreach ($filtered as $type) {
            $allTypes[] = $type->car_type;
        }

        $thisType = DB::table('vfq0g_car_types')->whereIn('id', $allTypes)->get();

        self::$response = [
            'successCode' => 1,
            'data' => $filtered,
            'type' => $thisType
        ];

        return self::$response;
    }



    public static function filterByApproval($results)
    {
        return $results->where('approved', '=', 1);
    }

}
