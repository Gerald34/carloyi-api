<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\CarSearch;
use Carbon\Carbon;

class UserShowroom extends Model
{

    protected $table ="vfq0g_profiles_cars";

    //
    public $id;
    public $uid;
    public $cid;
    public $test_drive_date;
    public static $response;
    public $extras;

    protected $fillable = [
        'uid',
        'cid',
        'test_drive_date',
    ];

    /**
     * Get User Showroom Cars
     * @param $id
     * @return array
     */
    public static function getShowroomByUser($id)
    {
        $entries = DB::table('vfq0g_profiles_cars')
                ->where([
                    'uid' =>$id
                ])
                ->get();
        if(count($entries) == 0)
        {
            return [
                'code' => -1,
                'error' => 'No cars were found',
                'data' => []
            ];
        }

        $car_ids = self::getCarIds($entries);

        $cars  = CarSearch::getSearchCarsByIds($car_ids);

        return
        [
            [
                'code' =>1,
                'error' => '',
                'data' => [
                    'entries' =>$entries,
                    'cars' => $cars
                ]
            ]
        ];

    }

    /**
     * @param $itemID
     * @return array
     */
    public static function getItemInfo($itemID) {

        $itemInfo = DB::table('vfq0g_dealer_user_posts')->where(['id' =>$itemID])->first();

        if (!empty($itemInfo)) {
            self::$response = [
                'successCode' => 206,
                'successMessage' => 'Item Found',
                'item' => $itemInfo,
                'dealer' => DB::table('vfq0g_dealers')->where('id', $itemInfo->dealer_id)->first()
            ];
        } else {
            self::$response = [
                'errorCode' => 306,
                'errorMessage' => 'Item Not Found'
            ];
        }

        return self::$response;
    }

    /**
     * Get User Offers From Dealers
     * @param $id
     * @return array
     */
    public static function getOffersByUser($id)
    {
        $entries = DB::table('vfq0g_dealer_user_posts')->where(['user_id' =>$id])->get();

        if(count($entries) == 0) {
          self::$response = [
            'errorCode' => 311,
            'errorMessage' => 'No offers were found',
          ];
        } else {

          $car_ids = self::getCarIds_2($entries);
          $cars  = CarSearch::getSearchCarsByIds($car_ids);
          
          self::$response = [
              'successCode' => 1,
              'offers' => $entries,
              'cars' => $cars,
          ];

        }

        return self::$response;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getUserOffers($id) {
      $entries = DealerUserPost::getPostsByUserID($id);

        if(count($entries) == 0)
        {
            return [
                'code' => -1,
                'error' => 'No offers were found',
                'data' => []
            ];
        }

        $car_ids = self::getCarIdsFromPosts($entries);
        $cars  = CarSearch::getSearchCarsByIds($car_ids);

        return
        [
            [
                'code' =>1,
                'error' => '',
                'data' => [
                    'entries' =>$entries,
                    'cars' => $cars
                ]
            ]
        ];

    }

    /**
     * @param $entries
     * @return array
     */
    private static function getCarIdsFromPosts($entries) {
        $data = [];
        foreach($entries as $entry)
        {
            $data[] = $entry->car_id;
        }
        return $data;
    }

    /**
     * @param $entries
     * @return array
     */
    public static function getCarIds_2($entries) {
        $data = [];
        foreach($entries as $entry)
        {
            $data[] = $entry->car_id;
        }
        return $data;
    }

    /**
     * @param $entries
     * @return array
     */
    private static function getCarIds($entries) {
        $data = [];
        foreach($entries as $entry) {
            $data[] = $entry->cid;
        }

        return $data;
    }

    public function reqExists() {

        $entry = self::where(['uid' => $this->uid, 'cid' => $this->cid] )->first();

        if($entry === null ) {

            $response = [
                'exists'  => false,
                'entry' => $entry
            ];

        } else {

            $response = [
                'exists'  => true,
                'entry' => $entry
            ];

        }


        return $response;
    }

    /**
     * @return array
     */
    public static function IsExisting (array $requestInfo) {

       $entry = self::where(['uid' => $requestInfo['uid'], 'cid' => $requestInfo['cid']] )->first();

       if($entry !== null ) {

        $response = [
            'exists'  => true,
            'entry' => $entry
        ];

       } else {

        $response = [
            'exists'  => false,
            'entry' => $entry
        ];

       }


       return $response;
    }

    /**
     * @param $requestObject
     * @return mixed
     */
    public static function saveRequest($requestObject) {

        $save = self::insert([
                'uid' => $requestObject['uid'],
                'cid' => $requestObject['cid'],
                'requested' => $requestObject['requested'],
                'request_date' => $requestObject['request_date'],
                'created_at' => date("Y-m-d H:i:s")
            ]
        );

        if($save === true) {
            self::$response = 'saved';
        } else {
            self::$response = 'not saved';
        }

        return self::$response;
    }

    /**
     * @return array|int
     */
    public function sendRequest() {

         $cars = CarSearch::getSearchCarsByIds([$this->cid]);
         $user = BaseUser::getUderById($this->uid);

         if($user == null) {
             return [
                 'code' => -1,
                 'error' =>'User not found'
             ];
         }

         $body = "";

         // $to = "info@carloyi.com,gerald@coppertable.co.za,mnqobimachi@gmail.com";
         $to = 'gerald@coppertable.co.za';
         $subject = "Carnet Quote requests";

        $message = "
        <h3>User Details</h3>
        <table>
        <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Email</th>
        </tr>
        <tr>
        <td>"  . $user->name  .  " </td>
         <td>"  . $user->lastName  .  " </td>
         <td>"  . $user->email  .  " </td>
        </tr>
        </table>
        ";

        //Add cars
        $message .="<h3>Cars</h3><table>";
        $message .="<tr> <th>ID</th>  <th>Name</th> <th>Price</th> <th>Type</th></td>";

        foreach ($cars['data'] as $car)
        {
            $message .="<tr>";
            $message .="<td>".  $car->id. "</td>";
            $message .="<td>".  $car->name. "</td>";
            $message .="<td>".  $car->price. "</td>";
            $message .="<td>".  $car->car_type. "</td>";
            $message .="</tr>";
        }

        $message .="</table>";

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <info@carnet.com>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        if(mail($to,$subject,$message,$headers)) {
            return 1;
        } else {
            return -1;
        }

    }

}
