<?php

namespace App\viewmodels;

use Illuminate\Database\Eloquent\Model;

class CarSearchFilters extends Model
{
    //

    public $price_min;
    public $price_max;

    public $car_type;

    //scores
    public $fuel;
    public $practicality;
    public $comfort;
    public $enjoyment;
    public $performance;
    public $reability;

    public $city_driving;
    public $long_distance;
    public $moving_luggage;
    public $seats;
    public $offroad;

}
