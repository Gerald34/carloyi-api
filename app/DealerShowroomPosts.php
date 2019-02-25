<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DealerShowroomPosts extends Model
{
    //
    protected $table  ='vfq0g_dealer_showroom';

    protected $fillable = ['parent_id','request_id','user_id', 'name','car_id','dealer_id','offer','comment', 'status', 'car_image', 'extras'];
    public static $response;

    public function getDealerShowroomEntries($id)
    {

       $posts = self::where(['dealer_id' => $id])->orderBy('created_at', 'DESC')->get();
       
            if(count($posts) == 0) {
                $response = [
                    'code' => -1,
                    'error' =>'No posts found',
                ];
            }
            //
            
            $entries  = [];

            foreach ($posts as $post) {
                $entries[] = [
                    'entry' => $post,
                    'users' => BaseUser::getUderById($post->user_id),
                    'car' => CarSearch::getSearchCarsByIds([$post->car_id]),
                    'posts' =>  DealerUserPost::getPostsByRequestID($post->id)
                ];
            }

            $response = [
                'code' => 1,
                'data' => $entries
            ];

            return $response;

    }

    public function getFullShowroomPost($id)
    {
            $post = self::find($id);
            if($post == null)
            {
                return [
                    'code' => -1,
                    'error' =>'No post found',
                ];
            }
            $entries[] = [
                'entry' => $post,
                'user' => BaseUser::getUderById($post->user_id),
                'car' => CarSearch::getSearchCarsByIds([$post->car_id]),
                'posts' =>  DealerUserPost::getPostsByRequestID($post->id)
            ];

            return
            [
                'code' => 1,
                'data' => $entries
            ];
    }

    public static function getPost($id)
    {
        return self::find($id);
    }

    public static function addPost($car_id, $user_id) {
        
        $dealers  = User::getActiveDealers();
        if($dealers['code'] == -1) {
            return
            [
                'code' => -1,
                'error' => 'No dealers were found'
            ];
        } else {
            $data = [];
            foreach($dealers['data'] as $key => $dealer) {
                $data[] =[
                  'dealer_id' => $dealer->id,
                  'user_id' => $user_id,
                  'status' => 1,
                  'car_id' => $car_id,
                  'created_at' => date("Y-m-d H:i:s")
                ];
            }
    
            $bool = self::insert($data);
    
            if($bool) {
                self::$response = [
                    'code' => 1,
                    'error' => ''
                ];
            } else {
                self::$response = [
                    'code' => -1,
                    'error' => 'Failed to insert to dealers'
                ];
            }
        }



        return self::$response;
    }
}
