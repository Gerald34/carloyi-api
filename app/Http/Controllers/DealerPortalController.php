<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DealerShowroomPosts;
use App\DealerUserPost;

use App\viewmodels\LoginViewModel;
use \Validator;

class DealerPortalController extends Controller
{
    //

   public function view($id)
   {
       $model = new DealerShowroomPosts();
       return $model->getFullShowroomPost($id);
   }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $results  = LoginViewModel::login($email, $password);

        if($results['code'] == -1)
        {
            return
            [
              'code' => -1,
              'error' => 'Failed to authenticate the user'
            ];
        }

        $passed = $this->checkDealerPermissions($results['roles']);

        if($passed)
        {
            return $results;
        }

        return
            [
              'code' => -1,
              'error' => 'You do not have permissions for this page'
            ];
    }
    private function checkDealerPermissions($roles)
    {
         $success = FALSE;
        if(count($roles) == 0)
        {
            return  $success;
        }



        foreach ($roles as $role)
        {
            if ($role->group_id == 11)
            {
                $success = TRUE;
                break;;
            }
        }
        return $success;

    }

    public function getDealerShowroom($id)
    {
        $model = new \App\DealerShowroomPosts();
        $car_ids = DB::table('vfq0g_dealer_showroom')
                ->where([
                    'dealer_id' => $id
                ])->select('car_id')
                ->get();

        if(count($car_ids) == 0)
        {
            return [
                'code' => -1,
                'error' => 'No entries found'
            ];
        }
        $posts_data = $model->getDealerShowroomEntries($id);

         return $posts_data;
    }

    public function placeOffer(Request $request)
    {
        $model = new DealerUserPost;

        // $rules = [
        //   'request_id' => 'required',
        //   'offer' => 'required',
        //   'comment' => 'required'
        // ];
        //
        // $input = $request->only('request_id','offer', 'comment');
        //
        //
        // $validator = Validator::make($input, $rules);
        // if($validator->fails())
        // {
        //     return [
        //         'code' =>'-1',
        //         'error'=> 'Invalid input',
        //         'data' => $validator->messages()
        //     ];
        // }

        $request_id = $request->input('request_id');
        //valid
        $showroom_post = \App\DealerShowroomPosts::getPost($request_id);

        if($showroom_post == null)
        {
            return [
                'code' =>'-1',
                'error'=> 'Invalid input',
            ];
        }
            $model->request_id = $request_id;
            $model->dealer_id =$showroom_post->dealer_id;
            $model->user_id = $showroom_post->user_id;
            $model->car_id = $showroom_post->car_id;
            $model->offer = $request->input('offer');
            $model->comment = $request->input('comment');



        return $model->placeOffer();

    }
    public function reply(Request $request)
    {


        $rules = [
          'parent_id' => 'required',
          'comment' => 'required',

        ];
        $input = $request->only('parent_id','comment');

        $validator = Validator::make($input, $rules);
        if($validator->fails())
        {
            return [
                'code' =>'-1',
                'error'=> 'Invalid input',
                'data' => $validator->messages()
            ];
        }
        $parent_id = $request->input('parent_id');

        $parent_post = \App\viewmodels\UserDealerPostViewModel::getPost($parent_id);
        $data_post = $parent_post->original;

        $dealer = $parent_post->dealer_id;


        if($parent_post == null)
        {
            return [
                'code' =>'-1',
                'error'=> 'Invalid post',
            ];
        }


         $model = new DealerUserPost();
         $model->comment = $request->input('comment');


        $model->parent_id = $parent_post->id;
        $model->user_id = $parent_post->user_id;
        $model->request_id = $parent_post->request_id;
        $model->dealer_id = $parent_post->dealer_id;
        $model->car_id = $parent_post->car_id;

        $new_offer = $request->input('offer');
        $model->offer = (empty($new_offer))? $parent_post->offer : $new_offer;


        return $model->placeOffer();

    }
}
