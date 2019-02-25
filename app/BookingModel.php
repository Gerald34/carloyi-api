<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Http\Controllers\FireBaseController;
use App\Http\Resources\BookingResource;
class BookingModel extends Model
{
    protected $table ="vfq0g_test_drives";

    protected $fillable = [
        'bookingDate',
        'offer_id',
        'time',
        'user_id',
        'dealer_id',
        'created_at',
        'updated_at'
    ];
    public static $response;

    public static function saveBooking($booking) {

        $checkBooking = BookingModel::where('offer_id', $booking['offer_id'])->first();

        if(empty($checkBooking)) {

            $book = new BookingModel;
            $book->offer_id = $booking['offer_id'];
            $book->bookingDate = $booking['bookingDate'];
            $book->time = $booking['time'];
            $book->user_id = $booking['user_id'];
            $book->dealer_id = $booking['dealer_id'];
            $book->created_at = Carbon::now();
            $book->updated_at = Carbon::now();
            $book->save();

            // $liveBooking = FireBaseController::liveBooking($book);
            $sendEmail = BookingResource::bookingTemplate($book);

            self::$response = [
                'successCode' => 202,
                'successMessage' => 'Booking Sent',
                // 'data' => $liveBooking,
                'email' => $sendEmail
            ];
        } else {
            self::$response = [
                'warningCode' => 204,
                'warningMessage' => 'Test Drive already booked'
            ];
        }

        return self::$response;

    }

}
