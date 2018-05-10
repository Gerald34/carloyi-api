<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\CarSearch;

class UserShowroom extends Model
{
    
    protected $table ="vfq0g_profiles_cars";
    
    //
    public $id;
    public $uid;
    public $cid;
    public $test_drive_date; 
    
    protected $fillable = [
        'uid',
        'cid',
        'test_drive_date',
    ];
    
    
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
    
    private static function getCarIds($entries)
    {
        $data = [];
        foreach($entries as $entry)
        {
            $data[] = $entry->cid;
        }
        return $data;
    }
    
    public function IsExisting ()
    {
        
       $entry= self::where(['uid' => $this->uid, 'cid' => $this->cid] )->first();
       return 
       [
           'exists'  =>($entry == null)? FALSE : TRUE,
           'entry' => $entry
       ];
        
    }
    
    public function sendRequest()
    {
         $cars=CarSearch::getSearchCarsByIds([$this->cid]);
         $user = BaseUser::getUderById($this->uid);
         if($user == null)
         {
             return
             [
                 'code' => -1,
                 'error' =>'User not found'
             ];
         }
         
         
         $body = "";
         
         $to = "sibu@coppertable.co.za,gerald@coppertable.co.za";
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
        foreach ($cars as $car)
        {
            $message .="<tr>";
            $message .="<td>".  $car->id. "</td>";
            $message .="<td>".  $car->name. "</td>";
            $message .="<td>".  $car->price. "</td>";
            $message .="<td>".  $car->type. "</td>";
            $message .="</tr>";
        }
        
        $message .="</table>";

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <info@carnet.com>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        if(mail($to,$subject,$message,$headers))
        {
            return 1;
        }else{
            return -1;
        }
         
    }
    
    
}
