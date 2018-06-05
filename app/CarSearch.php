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

        $cars = self::_getSearchCarsByIds($id_option);


                //return $cars->toSql();

         return ['code' => 1, 'error' => '', 'type' => 1,  'model' => $model, 'data' => $cars ];
    }

    public static function getSearchCarsByIds($ids, $options =array())
    {
        return self::_getSearchCarsByIds($ids);
    }

    public static function getSearchCarsByIds_2($ids, $options =array())
    {
        return self::_getSearchCarsByIds_2($ids);
    }
    /*
     * @param $ids array of car ids
     */
    private static function _getSearchCarsByIds($ids, $options = array())
    {
        $query = DB::table('vfq0g_sobipro_object AS a')
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

//           ->leftjoin('vfq0g_sobipro_field_data AS m', function ($join) {
//           $join->on('a.id', '=', 'm.sid')
//                ->where('m.fid', '=', 19);
//           })

           ->leftjoin('vfq0g_sobipro_object AS n', function ($join) {
           $join->on('a.parent', '=', 'n.id');

           })

           ->leftjoin('vfq0g_sobipro_relations AS o', function ($join) {
           $join->on('a.id', '=', 'o.id');
           })

          ->join('vfq0g_sobipro_object AS p', function ($join) {
           $join->on('o.pid', '=', 'p.id')
                    ->where('p.parent', '!=', '1');
           });

           //dd($query->toSql(), $query->getBindings());

          // options

//        $col = [
//            'm' => DB::select('SELECT baseData FROM vfq0g_sobipro_field_data where fid = 19 and sid = 804 limit 1')
//        ];


        $query->whereIn('a.id', $ids)
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
            'a.name AS  description',
            'n.name AS  brand',
            'p.name AS  model',
            'n.id AS Brand ID',
            'p.id AS model ID'
        ]);


        $cars = $query->get();

        $results = [];

        foreach($cars as $key => $car) {
            $desc = DB::select("SELECT baseData FROM vfq0g_sobipro_field_data where fid = 19 and sid = $car->id limit 1");
            $car->description = count($desc) > 0 ? $desc[0]->baseData : ""  ;
            $results[] = $car;
        }
       return $results;

    }

    private static function _getSearchCarsByIds_2($ids, $options = array())
    {
        $query = DB::table('vfq0g_sobipro_object AS a')
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

//           ->leftjoin('vfq0g_sobipro_field_data AS m', function ($join) {
//           $join->on('a.id', '=', 'm.sid')
//                ->where('m.fid', '=', 19);
//           })

            ->leftjoin('vfq0g_sobipro_object AS n', function ($join) {
                $join->on('a.parent', '=', 'n.id');

            })

            ->leftjoin('vfq0g_sobipro_relations AS o', function ($join) {
                $join->on('a.id', '=', 'o.id');
            })

            ->join('vfq0g_sobipro_object AS p', function ($join) {
                $join->on('o.pid', '=', 'p.id')
                    ->where('p.parent', '!=', '1');
            });

        //dd($query->toSql(), $query->getBindings());

        // options

//        $col = [
//            'm' => DB::select('SELECT baseData FROM vfq0g_sobipro_field_data where fid = 19 and sid = 804 limit 1')
//        ];


        $query->where('a.id', $ids)
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
                'a.name AS  description',
                'n.name AS  brand',
                'p.name AS  model',
                'n.id AS Brand ID',
                'p.id AS model ID'
            ]);


        $cars = $query->get();

        $results = [];

        foreach($cars as $key => $car) {
            $desc = DB::select("SELECT baseData FROM vfq0g_sobipro_field_data where fid = 19 and sid = $car->id limit 1");
            $car->description = count($desc) > 0 ? $desc[0]->baseData : ""  ;
            $results[] = $car;
        }
        return $results;

    }

    public static function searchByFilters($model) {

        // var_dump($model); exit;

        $car_type = $model['car_type'];

        $query = DB::table('vfq0g_sobipro_object AS a')
            ->join('vfq0g_sobipro_field_data AS c', function ($join) use ($car_type) {
                $join->on('a.id', '=', 'c.sid')
                    ->where([
                        ['c.fid', '=', 4  ],
                        ['c.baseData', '=', $car_type]
                    ]); //car type
            });
        if(isset($model['price_min']) && isset($model['price_max'])) {
            $price_range  = [$model['price_min'], $model['price_max']];
            $query->join('vfq0g_sobipro_field_data AS b', function ($join) use ($price_range) {
                $join->on('a.id', '=', 'b.sid')
                    ->where('b.fid', '=', 3)
                    ->whereBetween('b.baseData',$price_range);
            });
        }

        $query->select('a.id');

        $results =  $query->get();
        
        if(count($results) < 0)
        {
            return [
                'code' => -1,
                'error' => 'No cars were found',
            ];
        }

        $car_ids = [];
        foreach($results as $entry) {
            if(!in_array($entry->id, $car_ids)) {
                $car_ids[] = $entry->id;
            }
        }

        $car_scores = self::_getCarScores($car_ids);
        $car_points = [];

        foreach ($car_scores as $entry) {
            $points = 0;

            $points += (isset($model[ "city_driving"]))? $entry->city_driving * $model[ "city_driving"] : 0;
            $points += (isset($model[ "long_distance"]))? $entry->long_distance * $model[ "long_distance"] : 0;
            $points += (isset($model[ "off_road"]))? $entry->off_road * $model[ "off_road"] : 0;
            $points += (isset($model[ "luggage"]))? $entry->luggage * $model[ "luggage"] : 0;
            $points += (isset($model[ "fuel_capacity"]))? $entry->fuel_capacity * $model[ "fuel_capacity"] : 0;
            $points += (isset($model[ "enjoyment"]))? $entry->enjoyment * $model[ "enjoyment"] : 0;
            $points += (isset($model[ "practicality"]))? $entry->practicality * $model[ "practicality"] : 0;
            $points += (isset($model[ "performance"]))? $entry->perfomance * $model[ "performance"] : 0;
            $points += (isset($model[ "comfort"]))? $entry->comfort * $model[ "comfort"] : 0;
            $points += (isset($model[ "reliability"]))? $entry->city_driving * $model[ "reliability"] : 0;

            $car_points[$entry->id] = $points;

        }
        arsort($car_points);

        $len = count($car_points) > 16 ? 16 : count($car_points);

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
        //$results = self::_getSearchCarsByIds($cars);
        return ['code' => 1, 'error' => '',  'data' => $cars];
    }

    private static function _getCarScores($car_ids) {
        $query = DB::table('vfq0g_sobipro_object AS a')
            ->leftjoin('vfq0g_sobipro_field_data AS d', function ($join) {
                $join->on('a.id', '=', 'd.sid')
                    ->where('d.fid', '=', 5); // city driving
            })
            ->leftjoin('vfq0g_sobipro_field_data AS e', function ($join) {
                $join->on('a.id', '=', 'e.sid')
                    ->where('e.fid', '=', 6); // carrying people
            })
            ->leftjoin('vfq0g_sobipro_field_data AS f', function ($join) {
                $join->on('a.id', '=', 'f.sid')
                    ->where('f.fid', '=', 7); // long distance driving
            })
            ->leftjoin('vfq0g_sobipro_field_data AS g', function ($join) {
                $join->on('a.id', '=', 'g.sid')
                    ->where('g.fid', '=', 8); // off road
            })
            ->leftjoin('vfq0g_sobipro_field_data AS h', function ($join) {
                $join->on('a.id', '=', 'h.sid')
                    ->where('h.fid', '=', 10); //moving luggage
            })
            ->leftjoin('vfq0g_sobipro_field_data AS i', function ($join) {
                $join->on('a.id', '=', 'i.sid')
                    ->where('i.fid', '=', 11); //fuel
            })
            ->leftjoin('vfq0g_sobipro_field_data AS j', function ($join) {
                $join->on('a.id', '=', 'j.sid')
                    ->where('j.fid', '=', 12); // Enjoyment
            })
            ->leftjoin('vfq0g_sobipro_field_data AS k', function ($join) {
                $join->on('a.id', '=', 'k.sid')
                    ->where('k.fid', '=', 13); // practicality
            })
            ->leftjoin('vfq0g_sobipro_field_data AS l', function ($join) {
                $join->on('a.id', '=', 'l.sid')
                    ->where('l.fid', '=', 14); // perfomance
            })
            ->leftjoin('vfq0g_sobipro_field_data AS m', function ($join) {
                $join->on('a.id', '=', 'm.sid')
                    ->where('m.fid', '=', 15); // comfort
            })
            ->leftjoin('vfq0g_sobipro_field_data AS n', function ($join) {
                $join->on('a.id', '=', 'n.sid')
                    ->where('n.fid', '=', 16); // reliability
            });

        // $query->where('c.baseDatam', '=', $model['car_type']);

        $query->whereIn('a.id', $car_ids);

        $query->select([
            'a.id',
            'd.baseData AS city_driving',
            'e.baseData AS seats',
            'f.baseData AS long_distance',
            'g.baseData AS off_road',
            'h.baseData AS luggage',
            'i.baseData AS fuel',
            'j.baseData AS enjoyment',
            'k.baseData AS practicality',
            'l.baseData AS performance',
            'm.baseData AS comfort',
            'n.baseData AS reliability',

        ]);

        return $query->get();
    }

}
