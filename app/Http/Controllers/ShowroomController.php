<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserShowroom;


class ShowroomController extends Controller
{
    //
    
    public function getUserShowroom($id)
    {
        return  UserShowroom::getShowroomByUser($id);
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
            $model->sendRequest();
            if($model_data->requested == 1)
            {
                
                return
                [
                'code' =>  -1,
                'error' => 'You already sent for request for this car',
                'data' => $model_data
                ];                
            }
            
           
            $model_data->requested = 1;
            $model_data->request_date = date("Y-m-d H:i:s");
            $model_data->save();
            
            
            return
            [
                'code' =>  1,
                'error' => '',
                'data' => $model_data
            ];
        }
        
        return
            [
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
        
        if($data['exists'])
        {
            return
            [
                'code' =>  1,
                'error' => 'Already Added',
                'data' => $data['entry']
            ];
        }
        
        $model->fill($request->all());
        
        $res = $model->save();
        
        return
        [
            'code' => ($res)? 1 : -1,
            'error' => '',
            'data' => $model
        ];
    }
}
