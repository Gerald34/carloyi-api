<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
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
Route::post('/affordability', 'AffordabilityController@getAffodability');
Route::post('/byAmount', 'AffordabilityController@getByAmount');
Route::post('/signin', 'UserLoginController@userSignIn');
Route::post('/signup', 'UserRegistrationController@userRegistration');
Route::post('/newsletter', 'NewsLetterController@signUp');
Route::post('/register', 'UserRegistrationController@checkUserEmail');
Route::get('/firebase', 'FireBaseController@getFireBaseData');
Route::post('/notification', 'ShowroomController@sendNotification');
Route::post('/newPasswordToken', 'UserLoginController@newPasswordToken');
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
    Route::post('/getOfferData', 'ShowroomController@createActiveChat');
    Route::get('/offerInformation/{itemID}', 'ShowroomController@offerInformation');
    Route::post('/booking', 'ShowroomController@booking');
    Route::post('/removeCar', 'ShowroomController@removeCar');
    Route::post('/subscribe', 'ShowroomController@subscribePushNotification');
    Route::post('/pushMessage', 'ShowroomController@pushMessage');
});

Route::get('/allCars', 'CarSearchController@getAllCars');

Route::group(['prefix' => '/portal' ], function()
{
    Route::get('/{id}', 'DealerPortalController@getDealerShowroom');
    Route::post('/reply', 'DealerPortalController@reply');
    Route::post('/placeoffer', 'DealerPortalController@placeOffer');
    Route::post('/login', 'DealerPortalController@login');
    Route::post('/dealerLogin', 'DealerPortalController@loginDealer');
    Route::get('/view/{id}', 'DealerPortalController@view');
    Route::post('/save/cars', 'DealerPortalController@saveDealerCars');
    Route::post('/remove/cars', 'DealerPortalController@removeDealerCars');
    Route::get('/all', 'DealerPortalController@getAllModels');
    Route::get('/deals/{id}', 'DealerPortalController@dealerDeals');
    Route::get('/floorCars/{dealerID}', 'DealerPortalController@floorCars');
    Route::get('/chats/{id}', 'DealerPortalController@fetchChats');
});

Route::get('/brands', function(){
    return App\Brand::getBrandOptions();
});

Route::get('/ttest', function(){
    return App\User::getActiveDealers();
});


Route::get('/brands/{id}', function($id) {
    return App\Brand::getBrand($id);
});

Route::group(['prefix' => '/carsearch' ], function()
{
    Route::get('/specific/{id}', 'CarSearchController@specific');
    Route::get('/affordability', 'CarSearchController@affordability');
    Route::post('/filter', 'CarSearchController@filter');
    Route::get('/filter-options', 'CarSearchController@getFilterOptions');
    Route::post('/type', 'CarSearchController@byType');
    Route::get('/getCarInfo/{carID}', 'CarSearchController@getCarInfo');
    Route::post('/randomcars', 'CarSearchController@randomCars');
    Route::post('/randomcarsfour', 'CarSearchController@randomCarsFour');
});
//Async Validation Requests
Route::post('/checkuser', 'UserRegistrationController@checkUserEmail');

Route::get('/dealerName/{id}', function($dealerID) {
    $dealer = DB::table('vfq0g_dealers')->where('id', $dealerID)->first();
    return [ 'dealer' => $dealer ];
});

Route::group(['prefix' => 'push_notifications'], function() {
    Route::post('/subscribe', 'PushNotificationController@subscribePushNotification');
    Route::post('/offer_push_message', 'PushNotificationController@offerPushMessage');
    Route::post('/request_push_message', 'PushNotificationController@requestPushMessage');
});

Route::group(['prefix' => '/admin'], function() {
   Route::post('/signin', 'AuthorizedController@adminLogin');
   Route::get('/export', 'AuthorizedController@export');
   Route::get('/tables', 'AuthorizedController@getTables');
   Route::get('/table/{tableName}', 'AuthorizedController@getTable');
   Route::post('/upload', 'AuthorizedController@importCsv');

   Route::post('/csvData', 'AuthorizedController@getCsvData');

   // Model routes
    Route::group(['prefix' => 'models'], function () {
        Route::post('/newModel', 'AuthorizedController@newModel');
        Route::get('/models', 'AuthorizedController@getModels');
        Route::get('/getModel/{modelID}', 'AuthorizedController@modelData');
        Route::post('/updateModel', 'AuthorizedController@updateModel');
        Route::post('/deleteModel', 'AuthorizedController@deleteModel');
   });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/get', 'AccountsController@getUsers');
    });

   // Dealers routes
    Route::group(['prefix' => 'dealers'], function () {
        Route::get('/getusers', 'AccountsController@getUsers');
        Route::get('/getdealers', 'AccountsController@getDealer');
        Route::post('/createDealer', 'AuthorizedController@createNewDealer');
        Route::get('/generatePassword', 'AuthorizedController@securePassword');
        Route::post('/dealerInformation', 'AuthorizedController@dealerInformation');
        Route::post('/updateDealerInformation', 'AuthorizedController@updateDealerInformation');
        Route::get('/deactivateAccount/{dealerID}', 'AuthorizedController@deactivateAccount');
        Route::get('/activateAccount/{dealerID}', 'AuthorizedController@activateAccount');
    });

   // Brands routes
   Route::group(['prefix' => 'brands'], function () {
        Route::get('/get', 'AuthorizedController@getBrands');
        Route::get('/getBrandData/{brandID}', 'AuthorizedController@getBrandData');
        Route::post('/edit', 'AuthorizedController@editBrand');
        Route::get('/remove', 'AuthorizedController@removeBrands');
        Route::post('/new', 'AuthorizedController@newBrand');
	Route::get('/remove/{id}', 'AuthorizedController@removeBrand');
   });

    Route::group(['prefix' => 'cars'], function () {
        Route::get('/cars', 'AuthorizedController@getCars');
        Route::get('/carInformation/{carID}', 'AuthorizedController@carInformation');
	Route::post('/modelInformationUpdate', 'AuthorizedController@updateCar');
   });

    Route::group(['prefix' => 'images'], function() {
       Route::get('/image/{filename}', 'AuthorizedController@image');
       Route::post('/single', 'AuthorizedController@store');
    });

});

Route::get('/getusers', 'AccountsController@getUsers');
Route::get('/getdealers', 'AccountsController@getDealer');
