<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerShowroomPosts extends Model
{
    //
    protected $table  ='vfq0g_dealer_showroom';
    
    
    public function getDealerShowroomEntries($id)
    {
       $posts = self::where(['dealer_id' => $id])->get();
            if(count($posts) == 0)
            {
                return [
                    'code' => -1,
                    'error' =>'No posts found',
                ];
            }
            // 

            $entries  = [];

            foreach ($posts as $post)
            {
                $entries[] = [
                    'entry' => $post,
                    'user' => BaseUser::getUderById($post->user_id),
                    'car' => CarSearch::getSearchCarsByIds([$post->car_id]),
                    'posts' =>  DealerUserPost::getPostsByRequestID($post->id)
                ];
            }
            return
            [
                'code' => 1,
                'data' => $entries
            ];
        
    }
    
    public static function addPost($car_id, $user_id)
    {
        
        $dealers  = User::getActiveDealers();
        if($dealers['code'] == -1)
        {
            return
            [
                'code' => -1,
                'error' => 'No dealers ere found'
            ];
        }
        
        $data = [];
        foreach($dealers['data'] as $key => $dealer)
        {
            $data[] =[
              'dealer_id' => $dealer->id,
              'user_id' => $user_id,
              'status' => 1,
              'car_id' => $car_id,
              'created_at' => date("Y-m-d H:i:s")
            ];
        }
        
        $bool = self::insert($data); 
        
        if($bool)
        {
            return[
                'code' => 1,
                'error' =>''
            ];
        }
        
        return [
            'code' => -1,
            'error' => 'Failed to insert to dealers'
        ];
        
    }
}
