<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FireBaseResource;

class FireBaseController extends Controller
{
    public function getFireBaseData($data)
    {
        return FireBaseResource::newOffer($data);
    }

    public static function activateChat($data)
    {
        return FireBaseResource::newOffer($data);
    }

    public static function liveBooking($data)
    {
        return FireBaseResource::newBooking($data);
    }

}
