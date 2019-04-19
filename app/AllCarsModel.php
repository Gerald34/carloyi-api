<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllCarsModel extends Model
{
    //
//   protected $primaryKey = 'id';
 
//   public $table = "vfq0g_allcars";
//   public $fillable = [
//	'id','approved','state','brand_id','model_id','name','price','car_type','city_driving','carrying_people','long_distance_driving','off_roading',
//	'moving_luggage', 'fuel_efficiency', 'enjoyment', 'practicality', 'performance', 'comfort', 'reliability', 'car_image', 'total_score', 'verdict',
//	'engine_type', 'power_kw', 'torque_nm', 'acceleration_0_100', 'consumption_l_100km', 'points' => 0, 'updated_at','created_at'
//   ];

    public $table = "vfq0g_allcars";
    public $fillable = [
        'id',
        'state',
        'brand_id',
        'model_id',
        'car_image',
        'name',
        'price',
        'car_type',
        'city_driving',
        'carrying_people',
        'long_distance_driving',
        'off_roading',
        'moving_luggage',
        'fuel_efficiency',
        'enjoyment',
        'practicality',
        'performance',
        'comfort',
        'reliability',
        'total_score',
        'approved',
        'verdict',
        'engine_type',
        'power_kw',
        'torque_nm',
        'acceleration_0_100',
        'consumption_l_100km',
        'updated_at',
        'created_at'

    ];
}
