<?php

namespace App\Http\Controllers;

use App\CarSearch;
use Illuminate\Http\Request;
use App\UserShowroom;
use App\CarnetUsers;
use App\Http\Resources\DealsResource;

class ShowroomController extends Controller {
    // Properties
    public $response;

    public function getUserShowroom($id)
    {
        return  UserShowroom::getShowroomByUser($id);
    }

    public function showroomOffers($id)
    {
        return  UserShowroom::getOffersByUser($id);
    }

    public function placeRequest(Request $request)
    {

        $model = new UserShowroom;
        $model->cid = $request->input('cid');
        $model->uid = $request->input('uid');
        $data = $model->IsExisting();

        if($data['exists'])
        {
            $model_data = $data['entry'];
            if($model_data->requested == 1)
            {
                return [
                'code' =>  -1,
                'error' => 'You already sent for request for this car',
                'data' => $model_data
                ];
            }

            $added_post_results = \App\DealerShowroomPosts::addPost($model->cid, $model->uid);
            $model->sendRequest();
            $model_data->requested = 1;
            $model_data->request_date = date("Y-m-d H:i:s");
            $model_data->save();

            return [
                'code' =>  1,
                'error' => '',
                'data' => $model_data
            ];
        }

        return [
                'code' =>  -1,
                'error' => "Whe couldn't find the car you requesting quote for from your show room",
                'data' => $model
            ];
    }


    public function addNew(Request $request)
    {
        $model = new UserShowroom;
        $model->cid = $request->input('cid');
        $model->uid = $request->input('uid');
        $data = $model->IsExisting();
        if($data['exists']) {
            return [
                'code' =>  1,
                'error' => 'Already Added',
                'data' => $data['entry']
            ];
        }
        $model->fill($request->all());
        $res = $model->save();
        return [
            'code' => ($res)? 1 : -1,
            'error' => '',
            'data' => $model
        ];
    }

    public function updateProfile(Request $request) {
      if($request->input('agreement') === 'on') {

        $updateData = [
          'agreement' => 1
        ];

        $update = CarnetUsers::find($request->input('userID'));

        $update->name = $request->input('firstName');
        $update->lastName = $request->input('lastName');
        $update->email = $request->input('email');
        $update->identity = $request->input('identity');
        $update->nationality = $request->input('nationality');
        $update->gender = $request->input('gender');
        $update->agreement = $updateData['agreement'];
        $update->contactNumber = $request->input('contactNumber');
        $update->save();

        $userUpdatedData = CarnetUsers::where('id', $request->input('userID'))->first();
        $this->response = [
          'successCode' => 255,
          'data' => [$userUpdatedData],
          'successMessage' => 'Profile Updated'
        ];
      } else {
        $this->response = [
          'errorCode' => 355,
          'errorMessage' => 'Please note that if you do not consent for a "Credit Check" you will not recieve offers from dealerships'
        ];
      }

      return $this->response;
    }

    public function interested($offerID) {

        $offerID = [
            'offer_id' => $offerID
            ];

        $this->response = DealsResource::updateStatus($offerID);

        return $this->response;
    }

    public function rejected(Request $request) {

        $offerID = [
            'offer_id' => $request->input('offer_id')
        ];

        $this->response = DealsResource::rejectStatus($offerID);

        return $this->response;
    }

    public function getCars($id) {

        $cars = CarSearch::getSearchCarsByIds_2($id);

        return $cars;
    }
}
