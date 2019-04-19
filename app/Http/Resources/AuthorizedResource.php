<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\AuthorizedModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\DealersModel as Dealers;
use App\models as Models;
use App\Brand as Brands;
use App\AllCarsModel as Cars;
use App\BlogPostsModel as BlogPosts;
use Illuminate\Support\Facades\Storage;
use App\FeaturedArticleModel as FeaturedArticles;

class AuthorizedResource extends Resource
{
    public static $response;

    /**
     * Admin login
     * @param $email
     * @param $password
     * @return array
     */
    public static function getAuthorizedAccount($email, $password)
    {
        $authorizedUsers = AuthorizedModel::where('email', $email)->first();

        if (!empty($authorizedUsers)) {
            $verify = self::verifyPassword($password, $authorizedUsers->password);

            if ($verify !== false) {

                self::$response = [
                    'authStatus' => true,
                    'successMessage' => 'Authorized',
                    'adminInformation' => $authorizedUsers
                ];

            } else {
                self::$response = [
                    'authStatus' => false,
                    'errorMessage' => 'Unauthorized user password',
                ];
            }
        } else {
            self::$response = [
                'authStatus' => false,
                'errorCode' => 401,
                'errorMessage' => 'No authorized user found...'
            ];
        }

        return self::$response;
    }

    /**
     * Verify password
     * @param $password
     * @param $authPassword
     * @return mixed
     */
    public static function verifyPassword($password, $authPassword)
    {
        return Hash::check($password, $authPassword);
    }

    /**
     * Fetch database collection
     * @return mixed
     */
    public static function databaseTables()
    {
        return DB::select('SHOW TABLES');
    }

    /**
     * Convert CSV to array
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    public static function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Create new dealer
     * @param $newDealer
     * @return array
     */
    public static function newDealer($newDealer) {
        $dealers = self::dealerExists($newDealer['email']);

        if($dealers === null) {
            $generatedPassword = self::generatePassword();

            $create = new Dealers;
            $create->name = $newDealer['name'];
            $create->email = $newDealer['email'];
            $create->password = $newDealer['password'];
            $create->location = $newDealer['location'];
            $create->status = 1;
            $create->save();
	    self::sendDealerEmail($newDealer['email'], $newDealer['password']);
            self::$response = [
                'successCode' => 201,
                'successMessage' => 'New dealer account has been created and a detailed email with login information has been sent to ' . $newDealer['email'],
                'dealers' => Dealers::all()
            ];

        } else {
            self::$response = [
                'errorCode' => 405,
                'errorMessage' => 'Account eith given email exists'
            ];
        }

        return self::$response;

    }

    /**
     * @param $dealerEmail
     * @param $dealerPassword
     * @return bool
     */
    public static function sendDealerEmail($dealerEmail, $dealerPassword) {
        $body = "";
        $to = $dealerEmail;
        $subject = "Carloyi Dealer Registration";

        $message = "
              <h2>Dealer portal login details</h2>
              <h4 class='helloMsg'>Username: $dealerEmail</h4>
              <h4>Password: $dealerPassword</h4>
        ";
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <carloyi.dealers@carloyi.com>' . "\r\n";
        mail($to, $subject, $message, $headers, $body);
        return true;
    }

    /**
     * Generate random alphanumeric string
     * @param int $length
     * @return string
     */
    public static function generatePassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Check if dealer exists
     * @param $email
     * @return mixed
     */
    private static function dealerExists($email) {
        return Dealers::where('email', '=', $email)->first();
    }

    /**
     * Truncate database table
     * @param $tableName
     * @return array
     */
    public static function truncateTable($tableName) {
        DB::table($tableName)->truncate();
        return [ 'successCode' => 200 ];
    }

    /**
     * Add new model
     * @param $id
     * @param $modelName
     * @param $brandID
     * @return array
     */
    public static function addModel($id, $modelName, $brandID) {

        // Check if model exists
        $findModel = self::_findModel($id);

        // Save model
        if($findModel === null && empty($findModel)) {
            // All Models instance
            $addModel = new Models;
            $addModel->id = $id;
            $addModel->model = $modelName;
            $addModel->brand_id = $brandID;
            $addModel->save();

            self::$response = [
                'successCode' => 201,
                'successMessage' => 'New model added'
            ];

        } else {
            self::$response = [
                'errorCode' => 401,
                'errorMessage' => 'Model with id: ' . $id .' exists, Try a unique model id.'
            ];
        }

        return self::$response;
    }

    /**
     * Check if model exists
     * @param $id
     * @return mixed
     */
    public static function _findModel($id){
        return DB::table('vfq0g_models')->where('id', $id)->first();
    }

    public static function modelUpdate($updateData) {
        $update = Models::where('id', '=', $updateData['id'])->update($updateData);

        if($update === 1) {
            self::$response = [
                'successCode' => 200,
                'successMessage' => $updateData['model'] . ' has been successfully updated.'
            ];
        } else {
            self::$response = [
                'errorCode' => 200,
                'errorMessage' => $updateData['model'] . ' could not be updated.'
            ];
        }
        return self::$response;
    }

    public static function removeModel($modelID) {
        Models::where('id', $modelID)->delete();
        self::$response = [
            'successCode' => 200,
            'successMessage' => 'Model has been successfully removed.'
        ];

        return self::$response;
    }

    public static function dealerUpdate($updateData) {
        $update = Dealers::where('id', '=', $updateData['id'])->update($updateData);

        if($update === 1) {
            self::$response = [
                'successCode' => 200,
                'successMessage' => $updateData['name'] . ' has been successfully updated.'
            ];
        } else {
            self::$response = [
                'errorCode' => 205,
                'errorMessage' => $updateData['name'] . ' could not be updated.'
            ];
        }
        return self::$response;
    }

    public static function deactivateDealerUpdate($dealerID) {
        $status = [ 'status' => 0 ];
        $update = Dealers::where('id', '=', $dealerID)->update($status);

        if($update === 1) {
            self::$response = [
                'successCode' => 200,
                'successMessage' => 'Account has been successfully deactivated.'
            ];
        } else {
            self::$response = [
                'errorCode' => 205,
                'errorMessage' => 'Account could not be deactivated.'
            ];
        }
        return self::$response;
    }

    public static function activateDealerUpdate($dealerID) {
        $status = [ 'status' => 1 ];
        $update = Dealers::where('id', '=', $dealerID)->update($status);

        if($update === 1) {
            self::$response = [
                'successCode' => 200,
                'successMessage' => 'Account has been successfully activated.'
            ];
        } else {
            self::$response = [
                'errorCode' => 205,
                'errorMessage' => 'Account could not be activated.'
            ];
        }
        return self::$response;
    }

    public static function getAllBrands() {
        $allBrands = new Brands;
        return $allBrands->all();
    }

    public static function getAllCars() {
        $allCars = new Cars;
        return $allCars->all();
    }


        public static function updateVehicle($updateCar) {
        $update = Cars::where('id', $updateCar['id'])->update($updateCar);
        return $update;
    }

/**
     * @return BlogPosts[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function allPosts()
    {
        return BlogPosts::all();
    }

    /**
     * @param $post
     * @return array
     */
    public static function newBlog($post)
    {
        $newBlog = new BlogPosts;
        $newBlog->blog_title = $post['blog_title'];
        $newBlog->blog_description = $post['blog_description'];
        $newBlog->blog_caption = $post['blog_caption'];
        $newBlog->blog_thumbnail = $post['blog_thumbnail']['path'];
        $newBlog->blog_background = $post['blog_background']['path'];
        $newBlog->external_link = $post['external_link'];
        $newBlog->updated_at = $post['updated_at'];
        $newBlog->created_at = $post['created_at'];
        $newBlog->save();

        return self::$response = [
            'successCode' => 200,
            'successMessage' => 'Blog post successful',
            'allPosts' => self::allPosts()
        ];
    }

    /**
     * @param $edit
     * @return array
     */
    public static function editPost($edit) {
        $newBlog = BlogPosts::find($edit['id']);
        $newBlog->blog_title = $edit['blog_title'];
        $newBlog->blog_description = $edit['blog_description'];
        $newBlog->blog_caption = $edit['blog_caption'];
        $newBlog->blog_thumbnail = $edit['blog_thumbnail'];
        $newBlog->blog_background = $edit['blog_background'];
        $newBlog->external_link = $edit['external_link'];
        $newBlog->updated_at = $edit['updated_at'];
        $newBlog->save();

        return self::$response = [
            'successCode' => 200,
            'successMessage' => 'Blog post successful',
            'allPosts' => self::allPosts()
        ];
    }

    /**
     * @param $postID
     * @return mixed
     */
    public static function getPost($postID) {
        return BlogPosts::where('id', $postID)->get();
    }

    /**
     * @param $post
     * @return array
     */
    public static function newArticle($post) {
        $newBlog = new FeaturedArticles;
        $newBlog->featured_title = $post['featured_title'];
        $newBlog->article_slug = $post['article_slug'];
        $newBlog->featured_caption = $post['featured_caption'];
        $newBlog->featured_thumbnail = $post['featured_thumbnail'];
        $newBlog->featured_background_image = $post['featured_background_image'];
        $newBlog->featured_story = $post['featured_story'];
        $newBlog->external_link = $post['external_link'];
        $newBlog->author = $post['author'];
        $newBlog->updated_at = $post['updated_at'];
        $newBlog->created_at = $post['created_at'];
        $newBlog->save();

        return self::$response = [
            'successCode' => 200,
            'successMessage' => 'Blog post successful',
            'allPosts' => self::allPosts()
        ];
    }

}

