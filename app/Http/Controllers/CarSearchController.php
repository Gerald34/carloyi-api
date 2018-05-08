<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use \Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\CarSearch;
/**
 * Description of CarSearchController
 *
 * @author macsox
 */
class CarSearchController extends Controller {
    //put your code here
    
    public function specific($id)
    {       
        return CarSearch::searchByModel($id);
    }
    
    public function affordability(Request $request)
    {
        return "hello affodability";
    }
    
}
