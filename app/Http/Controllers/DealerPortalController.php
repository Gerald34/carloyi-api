<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\viewmodels\LoginViewModel;

class DealerPortalController extends Controller
{
    //
    
   
    
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
    public function makeOffer(Request $request)
    {
        
    }
    
    public function reply(Request $request)
    {
        
    }
}
