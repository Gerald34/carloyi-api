<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DealerUserPost extends Model
{
    //
    public $table = "vfq0g_dealer_user_posts";

    public $fillable = [
        'request_id',
        'parent_id',
        'dealer_id',
        'user_id',
        'car_id',
        'offer',
        'comment',
        'car_brand',
        'car_model',
        'car_name'
    ];

    public $attributes = [
        'request_id',
        'parent_id',
        'dealer_id',
        'user_id',
        'car_id',
        'offer',
        'comment',
        'car_brand',
        'car_model',
        'car_name'
    ];


    public $request_id;
    public $parent_id;
    public $dealer_id;
    public $user_id;
    public $car_id;
    public $offer;
    public $comment;
    public $car_image;
    public $attachment;
    public $car_model;
    public $car_name;
    public $car_brand;

    public static function getPostsByRequestID($id) {
        $posts = self::where(['request_id' => $id])->get();
        return $posts;
    }

    public static function getPostsByUserID($id) {
        $posts = self::where(['user_id' => $id])->get();
        return $posts;
    }

     public function placeOffer() {

        $this->fill($this->getOfferData());

        $saved = $this->save();

         var_dump($this);

        if($saved) {
            return ['code' => 1, 'error' =>'', 'data' => [$this] ];
        }

        return ['code' => -1, 'error' => 'failed to save', 'data' => [$this] ];
     }

     public function getOfferData() {
         $data = [
            'request_id' => $this->request_id,
            'parent_id' => $this->parent_id,
            'dealer_id' => $this->dealer_id,
            'user_id' => $this->user_id,
            'car_id' => $this->car_id,
            'offer' => $this->offer,
            'comment' => $this->comment,
            'car_brand' => $this->car_brand,
            'car_model' => $this->car_model,
            'car_name' => $this->car_name
        ];

         return $data;
     }

    public static function getPost($id) {
        return self::find($id);
    }

}
