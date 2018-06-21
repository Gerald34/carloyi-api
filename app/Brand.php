<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Brand extends Model
{
    //
    protected $table ="vfq0g_brands";

    public static function getBrandOptions()
    {
        $brands = Brand::where('state',1)->get(['id','brand']);
        return $brands;
    }
    public static function getBrand($id)
    {


        $brand= Brand::where('id',$id)
                ->get(['id','brand'])->first();

        if($brand == null){
            return ['code' => -1, 'error' => 'Brand not found', 'brand' => NULL,  'data' => [] ];
        }
        $models = Brand::getModelsByBrand($id);

        // $thisType = DB::table('vfq0g_car_types')->where('id', $models[0]->type)->first();

        return [
            'code' =>1,
            'error' => '',
            'brand' => $brand,
            'models' => $models,
            // 'type' => $thisType
        ];
    }



    private static function getModelsByBrand($id)
    {
        $models = DB::table('vfq0g_models')
                ->where('brand_id', $id)
                ->get(['id','model']);
        return $models;
    }


}
