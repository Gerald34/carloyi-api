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
use App\Http\Resources\PushNotificationResource;

class DealerPortalController extends Controller
{
  public $response;
    //


    public function fetchChats($dealerID) {
        $chats = DB::table('active_chats')->where('dealer_id', $dealerID)->get();
        $active = [];
        foreach($chats as $object) {
            $active[] = $object['chat_id'];
        }

        return $active;



        if(!empty($chats)) {

            $this->response = [
                'successCode' => 333,
                'successMessage' => 'Chats found',
                'data' => $chats
            ];

        } else {
            $this->response = [
                'errorCode' => 333,
                'errorMessage' => 'Chats found'
            ];
        }

        return $this->response;

    }

    /**
     * @param Request $request
     * @return array
     */
    public function loginDealer(Request $request) {
//        return md5($request->input('password'));
        $dealerData = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        $this->response = LoginViewModel::DealerLogin($dealerData);

        return $this->response;
    }

    /**
     * @param $id
     * @return array
     */
   public function view($id) {
       $model = new DealerShowroomPosts();
       return $model->getFullShowroomPost($id);
   }

   /**
    * @return User Data Object
    * @paran Request request
    */
    public function login(Request $request) {
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
                break;
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
        'model_id' => $request->input('model_id'),
        'availability' => 1,
        'email' => $request->input('email')
      ];

      $this->response = DealerCarsResource::saveCollection($dealerSelectedData);

      return $this->response;
    }

    public function floorCars($dealerID) {
        $this->response = DealerCarsResource::getCars($dealerID);

        return $this->response;
    }

    public function removeDealerCars($modelID) {
        $dealerSelectedData = [
            'dealer_id' => $request->input('dealer_id'),
            'model_id' => $request->input('model_id'),
        ];

        $this->response = DealerCarsResource::removeCollection($dealerSelectedData);

        return $this->response;
    }

    /**
     * Get Dealer Showroom
     * @param {id}
     * @return array
     */
    public function getDealerShowroom($id) {
        $model = new \App\DealerShowroomPosts();
        $car_ids = DB::table('vfq0g_dealer_showroom')
                ->where(['dealer_id' => $id])
                ->select('car_id')
                ->get();

        if(count($car_ids) == 0) {
            return [
                'code' => -1,
                'error' => 'No entries found'
            ];
        }
        
        $posts_data = $model->getDealerShowroomEntries($id);
        return $posts_data;
    }

    /**
     * 
     */
    public function placeOffer(Request $request) {
        
        $model = new DealerOffers;

        $request_id = $request->input('request_id');

        //valid
        $showroom_post = DealerShowroomPosts::getPost($request_id);
        
        if($showroom_post == null) {
            return [
                'code' =>'-1',
                'error'=> 'Invalid input',
            ];
        } else {

            $model->request_id = $request->input('request_id');
            $model->dealer_id = $showroom_post->dealer_id;
            $model->user_id = $showroom_post->user_id;
            $model->car_id = $showroom_post->car_id;
            $model->offer = $request->input('offer');
            $model->comment = $request->input('comment');
            $model->name = $request->input('name');
            $model->status = 'pending';
            $model->car_image = $request->input('car_image');

            $createOffer = $model->placeOffer();
            if($createOffer['code'] === 1) {
                $this->response = DealerShowroomPosts::getPost($request_id);
                PushNotificationResource::sendOfferPushMessage(
                    $showroom_post->user_id,
                    $model->car_image,
                    $model->name
                );
            } else {
                $this->response = $createOffer;
            }
        }

        return $this->response;
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


        if($parent_post == null) {
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

// sort by latest
// Auto highlight
// User email for dealer offers
