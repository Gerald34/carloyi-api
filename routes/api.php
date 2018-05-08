<?php

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Token
Route::get('/token', function()
{
    return csrf_token();
});

// ASync Submission Requests
Route::get('/affordability', 'AffordabilityController@getAffodability');
Route::get('/signin', 'UserLoginController@userSignIn');
Route::post('/signup', 'UserRegistrationController@userRegistration');

Route::get('/brands', function(){
    return App\Brand::getBrandOptions();
});
Route::get('/brands/{id}', function($id){
    return App\Brand::getBrand($id);
});

Route::group(['prefix' => '/carsearch' ], function()
{
    Route::get('/specific/{id}', 'CarSearchController@specific');
    Route::get('/affordability', 'CarSearchController@affordability');
});


//Async Validation Requests
Route::post('/checkuser', 'UserRegistrationController@checkUserEmail');

