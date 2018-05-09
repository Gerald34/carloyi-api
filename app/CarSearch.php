<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    
    public static function searchByModel($model)
    {
        $id_option = [];
        $car_ids = DB::table('vfq0g_sobipro_relations')
                ->where([
                    'oType' => 'entry',
                    'pid' =>$model
                ])->select('id')
                ->get();
        
        if(count($car_ids) == 0)
        {
            return ['code' => -1, 'error' => 'No entries match your model', 'data' => [] ];
        }
        
        foreach($car_ids as $key => $val)
        {
           $id_option[] = $val->id; 
            
        }
        
         $cars = DB::table('vfq0g_sobipro_object AS a')
                 ->join('vfq0g_sobipro_field_data AS b', function ($join) {
                    $join->on('a.id', '=', 'b.sid')
                         ->where('b.fid', '=', 3);
                    })
                 ->join('vfq0g_sobipro_field_data AS c', function ($join) {
                    $join->on('a.id', '=', 'c.sid')
                         ->where('c.fid', '=', 4);
                    })
                 ->leftjoin('vfq0g_sobipro_field_data AS d', function ($join) {
                    $join->on('a.id', '=', 'd.sid')
                         ->where('d.fid', '=', 17);
                    })
                    
                     ->leftjoin('vfq0g_sobipro_field_data AS e', function ($join) {
                    $join->on('a.id', '=', 'e.sid')
                         ->where('e.fid', '=', 18);
                    })
                    
                     ->leftjoin('vfq0g_sobipro_field_data AS f', function ($join) {
                    $join->on('a.id', '=', 'f.sid')
                         ->where('f.fid', '=', 20);
                    })
                    
                     ->leftjoin('vfq0g_sobipro_field_data AS g', function ($join) {
                    $join->on('a.id', '=', 'g.sid')
                         ->where('g.fid', '=', 21);
                    })
                     ->leftjoin('vfq0g_sobipro_field_data AS h', function ($join) {
                    $join->on('a.id', '=', 'h.sid')
                         ->where('h.fid', '=', 22);
                    })
                    
                     ->leftjoin('vfq0g_sobipro_field_data AS i', function ($join) {
                    $join->on('a.id', '=', 'i.sid')
                         ->where('i.fid', '=', 23);
                    })
                    
                    ->leftjoin('vfq0g_sobipro_field_data AS j', function ($join) {
                    $join->on('a.id', '=', 'j.sid')
                         ->where('j.fid', '=', 24);
                    })
                    ->leftjoin('vfq0g_sobipro_field_data AS k', function ($join) {
                    $join->on('a.id', '=', 'k.sid')
                         ->where('k.fid', '=', 11);
                    })
                    ->leftjoin('vfq0g_sobipro_field_data AS l', function ($join) {
                    $join->on('a.id', '=', 'l.sid')
                         ->where('l.fid', '=', 8);
                    })
                    
                    
                
                 ->whereIn('a.id', $id_option)
                 ->select([
                     'a.id',
                     'a.name',
                     'b.baseData AS price',
                     'c.baseData AS type',
                     'd.baseData AS image',
                     'e.baseData AS score',
                     'f.baseData AS engine',
                     'g.baseData AS power',
                     'h.baseData AS torque',
                     'i.baseData AS acceleration',
                     'j.baseData AS consumption',
                     'k.baseData AS fuel_efficiency',
                     'l.baseData AS score_offroad',
                 ])
                 ->get();
                    
                //return $cars->toSql();
         
         return ['code' => 1, 'error' => '', 'type' => 1,  'model' => $model, 'data' => $cars ];
    }
}
