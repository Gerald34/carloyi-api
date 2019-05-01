<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SpecialOffersModel as SpecialOffers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SpecialOffersController extends Controller
{
    public function getOffers() {
        return SpecialOffers::select('id', 'name', 'car_image', 'updated_at')->get();
    }

    public function getFullInformation($offerID) {
        return SpecialOffers::where('id', $offerID)->first();
    }

    public function updateOfferImage(Request $request) {
        $fileNameToStore = [];

        if ($request->hasFile('specialOfferImage')) {

            // get filename with extension
            $fileNameWithExtension = $request->file('specialOfferImage')->getClientOriginalName();

            // get filename without extension
            $filename = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

            // get file extension
            $extension = $request->file('specialOfferImage')->getClientOriginalExtension();

            // filename to store
            $fileNameToStore = $filename . '.' . $extension;

            // upload File to external server
            Storage::disk('local')->put($fileNameToStore, fopen($request->file('specialOfferImage'), 'r+'));

            // store $fileNameToStore in the database
        }

        return ['successCode' => 205, 'successMessage' => 'Images uploaded', 'newImagePath' => $fileNameToStore];
    }

    public function updateOffer(Request $request) {
        $update = [
            'id' => $request->input('id'),
            'car_id' => $request->input('car_id'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'model_id' => $request->input('model_id'),
            'car_image' => $request->input('car_image'),
            'car_type' => $request->input('car_type'),
            'discount' => $request->input('discount'),
            'total_score' => $request->input('total_score'),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ];
        $data = SpecialOffers::where('id', '=', $update['id'])->update($update);
        return $update;
    }
}
