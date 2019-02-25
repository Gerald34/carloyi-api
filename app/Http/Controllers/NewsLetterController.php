<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\NewsLetterResource;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class NewsLetterController extends Controller
{
    private $response;
    public function signUp(Request $request) {

        if($request->input('name') && $request->input('email')) {

            $request->validate([
                'email' => 'required|email',
                'name' => 'required'
            ]);

            $newsLetterData = [
                'name' => strip_tags($request->input('name')),
                'email' => strip_tags($request->input('email'))
            ];

            $this->response = NewsLetterResource::checkUser($newsLetterData);

        } else {
            $this->response = [
                'errorCode' => 203,
                'errorMessage' => "All fields are required"
            ];
        }

        return $this->response;
    }
}
