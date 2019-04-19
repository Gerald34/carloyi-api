<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarSpecialsModel as CarSpecials;
class CarSpecialsController extends Controller
{
    public function getSpecialOffers() {
        return CarSpecials::all();
    }
}
