<?php

namespace App\Http\Controllers;

use App\Http\Resources\DealsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DealerShowroomPosts;
use App\DealerUserPost;
use App\Http\Resources\DealerCarsResource;
use Illuminate\Support\Facades\Input;
use App\viewmodels\LoginViewModel;
use \Validator;
use App\DealerOffers;

class DealerPortalController extends Controller
{
  public $response;
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

        if($passed) {
            return $results;
        }

        return [
              'code' => -1,
              'error' => 'You do not have permissions for this page'
            ];
    }

    /**
     * @param $roles
     * @return bool
     */
    private function checkDealerPermissions($roles)
    {
        $success = FALSE;
        if(count($roles) == 0) {
            return  $success;
        }

        foreach ($roles as $role) {
            if ($role->group_id == 11)
            {
                $success = TRUE;
                break;;
            }
        }
        return $success;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveDealerCars(Request $request) {

      $dealerSelectedData = [
        'dealer_id' => $request->input('dealer_id'),
        'model_id' => $request->input('model_id')
      ];

      $this->response = DealerCarsResource::saveCollection($dealerSelectedData);

      return $this->response;
    }

    public function getAllModels() {

    }

    public function getDealerShowroom($id)
    {
        $model = new \App\DealerShowroomPosts();
        $car_ids = DB::table('vfq0g_dealer_showroom')
                ->where(['dealer_id' => $id])
                ->select('car_id')
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
        $model = new DealerOffers;

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

        if($showroom_post == null) {
            return [
                'code' =>'-1',
                'error'=> 'Invalid input',
            ];
        } else {

//            $data = [
//                'request_id' => $request_id,
//                'dealer_id' => $showroom_post->dealer_id,
//                'user_id' => $showroom_post->user_id,
//                'car_id' => $showroom_post->car_id,
//                'offer' => $request->input('offer'),
//                'comment' => $request->input('comment'),
//                'car_brand' => $request->input('car_brand'),
//                'car_model' => $request->input('car_model'),
//                'car_name' => $request->input('car_name')
//            ];

            $model->request_id = $request->input('request_id');
            $model->dealer_id = $showroom_post->dealer_id;
            $model->user_id = $showroom_post->user_id;
            $model->car_id = $showroom_post->car_id;
            $model->offer = $request->input('offer');
            $model->comment = $request->input('comment');
            $model->car_brand = $request->input('car_brand');
            $model->car_model = $request->input('car_model');
            $model->car_name = $request->input('car_name');
            $model->dealer_location = $showroom_post->dealer_location;

            return $model->placeOffer();
        }
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

    public function dealerDeals($dealerID) {
       $this->response = DealsResource::getDealerDeals($dealerID);

       return $this->response;
    }
}
