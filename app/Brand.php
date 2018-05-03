<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $table ="vfq0g_sobipro_object";
    
    public static function getBrandOptions()
    {
        $brands = Brand::where('oType','category')
                ->where('parent',1)
                ->where('approved',1)
                ->get(['id','name']);
        return $brands;
    }
    public static function getBrand($id)
    {
        
        
        $brand= Brand::where('id',$id)
                ->where('parent',1)
                ->get(['id','name','approved'])->first();
        
        if($brand == null){
            return ['code' => -1, 'error' => 'Brand not found', 'brand' => NULL,  'data' => [] ];
        }
        $models = Brand::getModelsByBrand($id);
        
        return [
            'code' =>1,
            'error' => '',
            'brand' => $brand,
            'models' => $models
        ];
    }
    
   
    
    private static function getModelsByBrand($id)
    {
        $models = Brand::where('oType','category')
                ->where('parent', $id)
                ->where('approved',1)
                ->get(['id','name']);
        return $models;
    }
    
    
}
