<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerOffers extends Model
{
    public $table = "vfq0g_dealer_user_posts";

    public $fillable = [
        'user_id',
        'dealer_id',
        'offer_id',
        'car_id',
        'request_id',
        'comment',
        'offer',
        'status',
        'created_at',
        'updated_at',
        'car_image',
        // 'car_model',
        'name',
        // 'dealer_location'
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
    public $name;
    public $car_brand;
    public $dealer_location;
    public $response;

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

        if($saved) {
            $this->response = [
                'code' => 1
            ];
        } else {
            $this->response = ['code' => -1, 'error' => 'failed to save', 'data' => [$this]];
        }

        return $this->response;
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
            'name' => $this->name,
            'car_image' => $this->car_image
        ];
        
        return $data;
    }

    public static function getPost($id) {
        return self::find($id);
    }
}
