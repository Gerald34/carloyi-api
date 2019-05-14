<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorizedResource;
use App\Exports\AllCarsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\ActiveChats;
use App\AllCarsModel as AllCars;
use App\models;
use Illuminate\Support\Facades\Storage;
use App\Brand;
use Carbon\Carbon;
use App\FeaturedArticleModel as FeaturedArticles;
use App\BlogPostsModel as BlogPosts;

class AuthorizedController extends Controller
{
    private $_email;
    private $_password;

    public $response;

    /**
     * Authorized login
     * @param Request $request
     * @return array
     */
    public function adminLogin(Request $request) {
        $this->_email = strip_tags($request->input('email'));
        $this->_password = strip_tags($request->input('password'));

        $this->response = AuthorizedResource::getAuthorizedAccount($this->_email, $this->_password);

        return $this->response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function newModel(Request $request) {
        $id = $request->input('id');
        $modelName = $request->input('model');
        $brandID = $request->input('brand_id');

        return AuthorizedResource::addModel($id, $modelName, $brandID);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export() {
        return Excel::download(new AllCarsExport, 'allcars.xlsx');
    }

    /**
     * @return mixed
     */
    public function getTables() {
        $this->response = AuthorizedResource::databaseTables();
        return $this->response;
    }

    /**
     * @param $tableName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getTable($tableName) {
        $tableModel = DB::table($tableName)->all();
        return Excel::download($tableModel , 'allcars.xlsx');
    }

    /**
     * New Dealer
     * @param Request $request
     * @return array
     */
    public function createNewDealer(Request $request) {
        $name = ucfirst(strip_tags($request->input('name')));
        $email = strip_tags($request->input('email'));
        $password = strip_tags($request->input('password'));
        $location = ucfirst(strip_tags($request->input('location')));

        $newDealer = [
            'name' => $name,
            'email' => $email,
            'password' =>$password,
            'location' => $location
        ];

        $this->response = AuthorizedResource::newDealer($newDealer);
        return $this->response;
    }

    /**
     * Generate Secure Password
     * @return array
     */
    public function securePassword() {
        return [
            'successCode' => 200,
            'successMessage' => 'Password Generated',
            'password' => AuthorizedResource::generatePassword()
        ];
    }

    /**
     * Get CSV file
     * @param Request $request
     * @return array|bool
     */
    public function getCsvData(Request $request) {
        $file = $request->file('spreadsheet');
        return AuthorizedResource::csvToArray($file);
    }

    /**
     * Import CSV file
     * @param Request $request
     * @return array
     */
    public function importCsv(Request $request)
    {
        $file = $request->file('spreadsheet');
        $customerArr = AuthorizedResource::csvToArray($file);
	
	// return $customerArr;
        
	$clear = AuthorizedResource::truncateTable('vfq0g_allcars');
        if ($clear['successCode'] === 200) {
            $data = [];
            foreach ($customerArr as $car) {
                $import = new AllCars;
                $import->id = $car['id'];
                $import->state = $car['state'];
                $import->brand_id = $car['brand_id'];
                $import->model_id = $car['model_id'];
                $import->car_image = $car['car_image'];
                $import->name = $car['name'];
                $import->price = $car['price'];
                $import->car_type = $car['car_type'];
                $import->city_driving = $car['city_driving'];
                $import->carrying_people = $car['carrying_people'];
                $import->long_distance_driving = $car['long_distance_driving'];
                $import->off_roading = $car['off_roading'];
                $import->moving_luggage = $car['moving_luggage'];
                $import->fuel_efficiency = $car['fuel_efficiency'];
                $import->enjoyment = $car['enjoyment'];
                $import->practicality = $car['practicality'];
                $import->performance = $car['performance'];
                $import->comfort = $car['comfort'];
                $import->reliability = $car['reliability'];
                $import->total_score = $car['total_score'];
                $import->approved = $car['approved'];
                $import->verdict = $car['verdict'];
                $import->engine_type = $car['engine_type'];
                $import->power_kw = $car['power_kw'];
                $import->torque_nm = $car['torque_nm'];
                $import->acceleration_0_100 = $car['acceleration_0_100'];
                $import->consumption_l_100km = $car['consumption_l_100km'];
                $import->updated_at = Carbon::now();
                $import->created_at = Carbon::now();
                $import->save();
            }
            $this->response = AuthorizedResource::getAllCars();
        } else {
             $this->response = [
                'errorCode' => 300,
                'errorMessage' => 'Spreadsheet could not upload'
            ];
        }


        return $this->response;
    }

    /**
     * @return models[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getModels() {
        $allModels = new Models;
        return $allModels->all();
    }

    /**
     * Fetch model data
     * @param $modelID
     * @return mixed
     */
    public function modelData($modelID) {
        $modelData = DB::table('vfq0g_models')->where('id', $modelID)->get();
        return $modelData;
    }

    public function updateModel(Request $request) {
        $updateData = [
            'id' => $request->input('id'),
            'model' => $request->input('model'),
            'brand_id' => $request->input('brand_id')
        ];

        $this->response = AuthorizedResource::modelUpdate($updateData);

        return $this->response;
    }

    public function deleteModel(Request $request) {
        $modelID = $request->input('id');

        $this->response = AuthorizedResource::removeModel($modelID);

        return $this->response;
    }

    public function dealerInformation(Request $request) {
        $dealerID = $request->input('dealerID');
        $modelData = DB::table('vfq0g_dealers')->where('id', $dealerID)->get();
        return $modelData;
    }

    public function updateDealerInformation(Request $request) {
        $update = [
            'id' => $request->input('id'),
            'name' => $request->input('name'),
            'location' => $request->input('location')
        ];

        $this->response = AuthorizedResource::dealerUpdate($update);

        return $this->response;
    }

    public function deactivateAccount($dealerID) {
        return AuthorizedResource::deactivateDealerUpdate($dealerID);
    }

    public function activateAccount($dealerID) {
        return AuthorizedResource::activateDealerUpdate($dealerID);
    }

    public function getBrands() {
        $this->response = AuthorizedResource::getAllBrands();

        return $this->response;
    }

    public function getBrandData($brandID) {
        $brandData = DB::table('vfq0g_brands')->where('id', $brandID)->get();
        return $brandData;
    }

    public function getCars() {
        $this->response = AuthorizedResource::getAllCars();

        return $this->response;
    }

    public function carInformation($carID) {
        $carData = DB::table('vfq0g_allcars')->where('id', $carID)->get();
        return $carData;
    }

    public function image($filename) {
       return Storage::get('cars/live/' . $filename);
    }

    public function store(Request $request)
    {
        $filenametostore = [];
        if($request->hasFile('single')) {

            //get filename with extension
            $filenamewithextension = $request->file('single')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('single')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '.' . $extension;

            //Upload File to external server
            Storage::disk('local')->put($filenametostore, fopen($request->file('single'), 'r+'));

            //Store $filenametostore in the database
        }
       
        return ['successCode' => 200, 'successMessage' => 'Images uploaded', 'name' => $filenametostore];
    }

    public function newBrand(Request $request) {

        $exists = Brand::where('id', $request->input('id'))->first();

        if($exists !== null) {
            $this->response = [
                'errorCode' => 303,
                'errorMessage' => 'Brand exists'
            ];
        } else {
            $brand = new Brand;
            $brand->id = $request->input('id');
            $brand->brand = $request->input('brand');
            $brand->state = $request->input('state');
	    $brand->save();

            $this->response = [
                'successCode' => 201,
                'successMessage' => 'New record inserted'
            ];
        }

        return $this->response;
    }

    public function removeBrand($id) {
        Brand::where('id', $id)->delete();

        $this->response = [
            'successCode' => 200,
            'successMessage' => 'Brand has been successfully removed.'
        ];

        return $this->response;
    }

    public function editBrand(Request $request) {

        $updateData = [
            'id' => $request->input('id'),
            'brand' => $request->input('brand'),
            'state' => $request->input('state')
        ];

        $update = DB::table('vfq0g_brands')->where('id', '=', $request->input('id'))->update($updateData);

        if($update === 1) {
            $this->response = [
                'successCode' => 200,
                'successMessage' => $updateData['brand'] . ' has been successfully updated.'
            ];
        } else {
            $this->response = [
                'errorCode' => 200,
                'errorMessage' => $updateData['brand'] . ' could not be updated.'
            ];
        }
        return $this->response;
    }

    public function updateCar(Request $request) {

        $updateCar = [
            'id' => $request->input('id'),
            'approved' => $request->input('approved'),
            'state' => $request->input('state'),
            'brand_id' => $request->input('brand_id'),
            'model_id' => $request->input('model_id'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'car_type' => $request->input('car_type'),
            'city_driving' => $request->input('city_driving'),
            'carrying_people' => $request->input('carrying_people'),
            'long_distance_driving' => $request->input('long_distance_driving'),
            'off_roading' => $request->input('off_roading'),
            'moving_luggage' => $request->input('moving_luggage'),
            'fuel_efficiency' => $request->input('fuel_efficiency'),
            'enjoyment' => $request->input('enjoyment'),
            'practicality' => $request->input('practicality'),
            'performance' => $request->input('performance'),
            'comfort' => $request->input('comfort'),
            'reliability' => $request->input('reliability'),
            'car_image' => $request->input('car_image'),
            'total_score' => $request->input('total_score'),
            'verdict' => $request->input('verdict'),
            'engine_type' => $request->input('engine_type'),
            'power_kw' => $request->input('power_kw'),
            'torque_nm' => $request->input('torque_nm'),
            'acceleration_0_100' => $request->input('acceleration_0_100'),
            'consumption_l_100km' => $request->input('consumption_l_100km')
        ];

        $car = AuthorizedResource::updateVehicle($updateCar);

        if($car === 1) {
            $this->response = [
                'successCode' => 200,
                'successMessage' => $updateCar['name'] . ' has been successfully updated.'
            ];
        } else {
            $this->response = [
                'errorCode' => 203,
                'errorMessage' => $updateCar['name'] . ' could not be updated.'
            ];
        }

        return $this->response;
    }

    /**
     * Blog Posts
     */
    public function getBlogPosts()
    {
        return BlogPosts::all();
    }

    /**
     * @param $postID
     * @return mixed
     */
    public function getBlogPost($postID)
    {
        return AuthorizedResource::getPost($postID);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function createBlog(Request $request)
    {
        $post = [
            'blog_title' => strip_tags($request->input('blog_title')),
            'blog_description' => strip_tags($request->input('blog_description')),
            'blog_caption' => strip_tags($request->input('blog_caption')),
            'blog_thumbnail' => $this->_storeThumbnail($request),
            'blog_background' => $this->_storeBackground($request),
            'external_link' => strip_tags($request->input('external_link')),
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now()
        ];

        return AuthorizedResource::newBlog($post);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function editPost(Request $request) {

        $thumbnailImage = 'none.jpg';
        if($request->hasFile('article_thumbnail')) {
            $thumbnailResult = $this->_storeThumbnail($request);
            $thumbnailImage = $thumbnailResult['path'];
        }

        $backgroundImage = 'none.jpg';
        if($request->hasFile('article_background')) {
            $backgroundResult = $this->_storeBackground($request);
            $backgroundImage = $backgroundResult['path'];
        }

        $edit = [
            'id' => strip_tags($request->input('itemID')),
            'blog_title' => strip_tags($request->input('blog_title')),
            'blog_description' => strip_tags($request->input('blog_description')),
            'blog_caption' => strip_tags($request->input('blog_caption')),
            'blog_thumbnail' => $thumbnailImage,
            'blog_background' => $backgroundImage,
            'external_link' => strip_tags($request->input('external_link')),
            'updated_at' => Carbon::now(),
        ];

        return AuthorizedResource::editPost($edit);
    }

    /**
     * @param $request
     * @return array
     */
    private function _storeThumbnail($request) {
        $fileNameToStore = [];

        if ($request->hasFile('article_thumbnail')) {

            // get filename with extension
            $fileNameWithExtension = $request->file('article_thumbnail')->getClientOriginalName();

            // get filename without extension
            $filename = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

            // get file extension
            $extension = $request->file('article_thumbnail')->getClientOriginalExtension();

            // filename to store
            $fileNameToStore = $filename . '_' . uniqid() . '.' . $extension;

            // upload File to external server
            Storage::disk('thumbnail')->put($fileNameToStore, fopen($request->file('article_thumbnail'), 'r+'));

            // store $fileNameToStore in the database
        }

        return ['status' => 'Images uploaded', 'path' => $fileNameToStore];
    }

    /**
     * @param $request
     * @return array
     */
    private function _storeBackground($request) {
        $fileNameToStore = [];

        if ($request->hasFile('article_background')) {

            // get filename with extension
            $fileNameWithExtension = $request->file('article_background')->getClientOriginalName();

            // get filename without extension
            $filename = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

            // get file extension
            $extension = $request->file('article_background')->getClientOriginalExtension();

            // filename to store
            $fileNameToStore = $filename . '_' . uniqid() . '.' . $extension;

            // upload File to external server
            Storage::disk('background')->put($fileNameToStore, fopen($request->file('article_background'), 'r+'));

            // store $fileNameToStore in the database
        }

        return ['status' => 'Images uploaded', 'path' => $fileNameToStore];
    }

    /**
     * @return FeaturedArticles[]|\Illuminate\Database\Eloquent\Collection
     */
    public function featured() {
        return FeaturedArticles::all();
    }
}

