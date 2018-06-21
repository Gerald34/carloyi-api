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
/**
 * Description of CarSearch
 *
 * @author macsox
 */
class CarSearch extends Model{
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

    public static function searchByModel($model) {

        $id_option = [];
        $cars = DB::table('vfq0g_allcars')
                ->where([
                    'approved' => 1,
                    'model_id' => $model
                ])->get();

        if(count($cars) == 0) {
            self::$response = [
              'code' => -1,
              'error' => 'No entries match your model',
              'data' => []
            ];
        } else {
          $thisType = DB::table('vfq0g_car_types')->where('id', $cars[0]->car_type)->first();
          self::$response = [
            'code' => 1,
            'error' => '',
            'type' => 1,
            'model' => $model,
            'data' => $cars,
            'car_type' => $thisType->car_type
          ];
        }

        return self::$response;
    }

    public static function getSearchCarsByIds($ids, $options =array())
    {
        return self::_getSearchCarsByIds($ids);
    }

    public static function getSearchCarsByIds_2($ids, $options =array())
    {
        return self::_getSearchCarsByIds_2($ids);
      }

    public static function getByCarType($type) {
      $response = [];
      $selectedTypes = [];
      $allTypes = DB::table('vfq0g_allcars')->where('car_type', $type)->orderBy('total_score', 'desc')->get();

      if($allTypes !== null) {
        foreach($allTypes as $entry) {
          if(!array_key_exists($entry->model_id,$selectedTypes)) {
            $selectedTypes[$entry->model_id] = $entry;
          }
        }

        $thisType = DB::table('vfq0g_car_types')->where('id', $type)->first();
        $response = [
          'successCode' => 206,
          'successMessage' => 'cars found',
          'data' => array_values($selectedTypes),
          'type' => $thisType
        ];
      } else {
        $response = [
          'errorCode' => 700,
          'errorMessage' => 'No cars were found'
        ];
      }

      return $response;
    }

    public static function searchByFilters($model) {

        $car_type = [$model['car_type']];

        $query = DB::table('vfq0g_allcars')->whereIn('car_type', $car_type);

        if(isset($model['price_min']) && isset($model['price_max'])) {
            $price_range  = [$model['price_min'], $model['price_max']];
            $query->whereBetween('price',$price_range);
        }

        $results =  $query->get();

        if(count($results) == 0)
        {
            return [
                'code' => -1,
                'error' => 'No cars were found',
            ];
        }

        $car_points = [];

        foreach ($results as $entry) {
            $points = 0;
            $points += (isset($model[ "city_driving"]))? $entry->city_driving * $model[ "city_driving"] : 0;
            $points += (isset($model[ "long_distance"]))? $entry->long_distance_driving * $model[ "long_distance"] : 0;
            $points += (isset($model[ "off_road"]))? $entry->off_road * $model[ "off_road"] : 0;
            $points += (isset($model[ "luggage"]))? $entry->luggage * $model[ "luggage"] : 0;
            $points += (isset($model[ "fuel_capacity"]))? $entry->fuel_capacity * $model[ "fuel_capacity"] : 0;
            $points += (isset($model[ "enjoyment"]))? $entry->enjoyment * $model[ "enjoyment"] : 0;
            $points += (isset($model[ "practicality"]))? $entry->practicality * $model[ "practicality"] : 0;
            $points += (isset($model[ "performance"]))? $entry->performance * $model[ "performance"] : 0;
            $points += (isset($model[ "comfort"]))? $entry->comfort * $model[ "comfort"] : 0;
            $points += (isset($model[ "reliability"]))? $entry->city_driving * $model[ "reliability"] : 0;

            $car_points[$entry->id] = $points;

        }

        arsort($car_points);

        $len = count($car_points) > 1000 ? 1000 : count($car_points);
        $final_car_ids = [];

        $i = 0;
        foreach ($car_points as $key => $entry) {
            $final_car_ids[] = $key;
            $i++;
            if($i >= $len) {
                break;
            }
        }

        $cars = self::getSearchCarsByIds($final_car_ids);

        self::$response = [
          'successCode' => 1,
          'successMessage' => 'Cars Found',
          'data' => $cars
        ];

        return self::$response;
    }

    private static function _getSearchCarsByIds($ids) {
      self::$response = [];
      $results = DB::table('vfq0g_allcars')->whereIn('id', $ids)->get();

      $allTypes = [];

      foreach($results as $type) {
        $allTypes[] = $type->car_type;

      }

      $thisType = DB::table('vfq0g_car_types')->whereIn('id', $allTypes)->get();

      self::$response = [
        'cars' => $results,
        'type' =>$thisType
      ];

      return self::$response;
    }



}
