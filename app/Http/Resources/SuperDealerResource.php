<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\Resource;

class SuperDealerResource extends Resource
{

    public static $response;


    /**
     * @param $requestedCarInformation
     * @param $userInformation
     * @param $dealer
     * @return array
     */
    public static function superDealerEmailTemplate($requestedCarInformation, $userInformation, $dealer)
    {
        $body = "";
        $to = $dealer->email;
        $subject = "User Vehicle Request";

        $message = "
<style>

.helloMsg small {
  font-size: 0.8rem;
}

.col-30 {
  width: 33.33333%;
  float: right;
  padding:1rem;
}

.theMail {
  margin-top: 5rem;
  width: 70%;
  padding: 0;
  margin-left: auto;
  margin-right: auto;
  display: block;
  background: #fff;
  -webkit-box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
  box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
  border-radius:0.4rem;
  border:2px solid #fff;
  overflow: hidden;
}

.carloyi {
  background: #3d6983;
  margin: 0;
  padding: 0.7rem;
}

.helloMsg {
  background: whitesmoke;
  margin: 0;
  font-size: 1.4rem;
  font-weight: normal;
  color: #3d6983;
  padding: 0.7rem;
}

.helloMsg small {
  font-size: 0.8rem;
}

.requestBody {
  padding: 0.2rem 0 1rem 0;
}

.col-60 {
  width: 60%;
  float: left;
  padding:1rem;
  color: #999999;
}

.col-40 {
  width: 40%;
  float: right;
  padding:1rem;
}

.requestedCar {

  -webkit-box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
  box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
  border-radius:0.2rem;
  border:1px solid #fff;
  overflow: hidden;
}

.mailcontent {

  color: #999999;
}

.mailcontent p {
  line-height: 16px;
  font-size: 0.85rem;
  font-weight: 300;
  margin-bottom: 0.3rem;
}

.theBold {
  font-weight: 500;
}

.mailHeader {
  font-size: 1rem;
  font-weight: 300;
}

.activate, .mailFooter {
  clear: both;
  width: 100%;
  display: block;
  padding: 0.7rem;
  background: #3d6983;
  color: whitesmoke;
  font-weight: 500;
  text-decoration: none;
  margin-bottom: 0;
}

.mailFooter h2 {
  font-size: 1rem;
}

table {
  border-collapse: collapse;
}

.table-bordered {
  border: 1px solid #dee2e6;
}

.table {
  width: 100%;
  max-width: 100%;
  margin-bottom: 1rem;
  background-color: transparent;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #dee2e6;
}

.table tbody + tbody {
  border-top: 2px solid #dee2e6;
}

.table .table {
  background-color: #fff;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid #dee2e6;
}

.table-bordered thead th,
.table-bordered thead td {
  border-bottom-width: 2px;
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.075);
}

.table-primary,
.table-primary > th,
.table-primary > td {
  background-color: #b8daff;
}

.table-hover .table-primary:hover {
  background-color: #9fcdff;
}

.table-hover .table-primary:hover > td,
.table-hover .table-primary:hover > th {
  background-color: #9fcdff;
}

</style>
<div class='theMail'>

  <div class='carloyi'>
    <a href='https://www.carloyi.com'>
      <img src='https://www.carloyi.com/assets/images/carnet_logo_white_1.png'/>
    </a>
  </div>

  <h1 class='helloMsg'>Hi " . ucfirst($dealer->name) . ",<br>
    <small>New Request For " . $requestedCarInformation->name . "</small></h1>

  <div class='requestBody'>

    <div class='col-30'>
      <div class='requestedCar'>
        <img src='https://carloyi.com/car_images/live/" . $requestedCarInformation->car_image . "' />
      </div>
    </div>

    <div class='col-30'>
      <div class='mailcontent'>
        <h3 class='mailHeader'>Requested Vehicle Information</h3>
        <p><span class='theBold'>Car Name:</span> " . $requestedCarInformation->name . "</p>
        <p><span class='theBold'>Retail Price:</span> R " . $requestedCarInformation->price . ".00</p>
        <p><span class='theBold'>Vehicle Type:</span> " . $requestedCarInformation->car_type . "</p>
      </div>
    </div>
    
    <div class='col-30'>
      <div class='mailcontent'>
        <h3 class='mailHeader'>User Information</h3>
        <p><span class='theBold'>Name:</span> " . $userInformation->name . "</p>
        <p><span class='theBold'>last Name:</span> " . $userInformation->lastName . "</p>
        <p><span class='theBold'>Email Address:</span> " . $userInformation->email . "</p>
      </div>
    </div>

  </div>

  <div class='mailFooter'>
    <h2>Respond with an offer</h2>
  </div>
</div>

";

        // Call email function
        self::sendEmail($to, $subject, $message, $body);

        // Send push notification
        $carImage = $requestedCarInformation->car_image;
        $carName = $requestedCarInformation->name;
        PushNotificationResource::sendDealerPushNotification($dealer->id, $carImage, $carName);

        self::$response = [
            'status' => true,
            'statusMessage' => 'Email sent'
        ];

        return self::$response;
    }

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param $body
     * @return bool
     */
    public static function sendEmail($to, $subject, $message, $body)
    {
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <info@carloyi.com>' . "\r\n";
        mail($to, $subject, $message, $headers, $body);
        return true;
    }

}

