<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerCars extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'vfq0g_dealer_cars';
    public $timestamps = false;
    protected $fillable = array(
        'dealer_id',
        'model_id'
    );
}
