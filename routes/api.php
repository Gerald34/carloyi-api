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
Route::post('/signin', 'UserLoginController@userSignIn');
Route::post('/signup', 'UserRegistrationController@userRegistration');

Route::group(['prefix' => '/accounts' ], function()
{
    Route::post('/register', 'AccountsController@register');
});



Route::group(['prefix' => '/showroom' ], function()
{
    Route::get('/cars/{id}', 'ShowroomController@getUserShowroom');
    Route::get('/offers/{id}', 'ShowroomController@showroomOffers');
    Route::post('/add', 'ShowroomController@addNew');
    Route::post('/placerequest', 'ShowroomController@placeRequest');
    Route::post('/updateprofile', 'ShowroomController@updateProfile');
    Route::get('/interested/{id}', 'ShowroomController@interested');
    Route::get('/reject/{id}', 'ShowroomController@rejected');
    Route::get('/allcars/{id}', 'ShowroomController@getCars');
});


Route::group(['prefix' => '/portal' ], function()
{
    Route::get('/{id}', 'DealerPortalController@getDealerShowroom');
    Route::post('/reply', 'DealerPortalController@reply');
    Route::post('/placeoffer', 'DealerPortalController@placeOffer');
    Route::post('/login', 'DealerPortalController@login');
    Route::get('/view/{id}', 'DealerPortalController@view');
    Route::post('/save/cars', 'DealerPortalController@saveDealerCars');
    Route::get('/all', 'DealerPortalController@getAllModels');
    Route::get('/deals/{id}', 'DealerPortalController@dealerDeals');
});

Route::get('/brands', function(){
    return App\Brand::getBrandOptions();
});

Route::get('/ttest', function(){
    return App\User::getActiveDealers();
});


Route::get('/brands/{id}', function($id){
    return App\Brand::getBrand($id);
});

Route::group(['prefix' => '/carsearch' ], function()
{
    Route::get('/specific/{id}', 'CarSearchController@specific');
    Route::get('/affordability', 'CarSearchController@affordability');
    Route::post('/filter', 'CarSearchController@filter');
    Route::get('/filter-options', 'CarSearchController@getFilterOptions');
});
//Async Validation Requests
Route::post('/checkuser', 'UserRegistrationController@checkUserEmail');
