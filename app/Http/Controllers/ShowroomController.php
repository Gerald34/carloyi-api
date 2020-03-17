<?php

namespace App\Http\Controllers;

use App\CarSearch;
use Illuminate\Http\Request;
use App\UserShowroom;
use App\CarnetUsers;
use App\Http\Resources\DealsResource;
use App\Http\Resources\ProfileUpdateResource;
use Carbon\Carbon;
use App\Http\Resources\UserRequestsResource;
use Illuminate\Support\Facades\DB;
use App\DealerShowroomPosts;
use App\Http\Resources\DealerCarsResource;
use App\AllCarsModel;
use App\Http\Resources\SuperDealerResource;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\DealsModel;
use App\ActiveChats;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FireBaseController;
use App\BookingModel;
use App\Http\Resources\PushNotificationResource;
use App\DealersModel;
class ShowroomController extends Controller
{
    // Properties
    public $response;

    /**
     * @param Request $request
     * @return array
     */
    public function subscribePushNotification(Request $request) {
        $subscriptionData = [
            'subscription' => $request->input('subscription'),
            'userID' => $request->input('userid')
        ];

        return PushNotificationResource::saveSubscription($subscriptionData);
    }

    /**
     * @param Request $request
     */
    public function pushMessage(Request $request) {
        $userID = $request->input('userID');

        return PushNotificationResource::sendPushMessage($userID);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function removeCar(Request $request)
    {
        $itemID = $request->input('itemID');
        $uid = $request->input('uid');

        $removeCar = DB::table('vfq0g_profiles_cars')
            ->where('cid', $itemID)
            ->where('uid', $uid)
            ->delete();

        if ($removeCar == 1) {
            $this->response = UserShowroom::getShowroomByUser($uid);
        } else {
            $this->response = [
                'errorCode' => 305,
                'errorMessage' => 'Could not delete'
            ];
        }

        return $this->response;
    }

    /**
     * @param $itemID
     * @return array
     */
    public function offerInformation($itemID)
    {
        $this->response = UserShowroom::getItemInfo($itemID);

        return $this->response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function booking(Request $request)
    {
        $booking = [
            'bookingDate' => str_replace('/', '-', $request->input('booking_date')),
            'time' => $request->input('booking_time'),
            'user_id' => $request->input('uid'),
            'dealer_id' => $request->input('dealer_id'),
            'offer_id' => $request->input('offer_id')
        ];

        $this->response = BookingModel::saveBooking($booking);

        return $this->response;
    }

    /**
     * @param $id
     * @return array
     */
    public function getUserShowroom($id)
    {
        return UserShowroom::getShowroomByUser($id);
    }

    /**
     * @param Request $request
     */
    public function sendNotification(Request $request)
    {
        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);
        $client->post('http://localhost:5000/sendDealerNotification', ['body' => json_encode(['dealerID' => '4'])]);
    }

    /**
     * @param $id
     * @return array
     */
    public function showroomOffers($id)
    {
        return UserShowroom::getOffersByUser($id);
    }

    /**
     * @param Request $request
     * @return array|string
     *
     */
    public function placeRequest(Request $request)
    {

        $requestData = [
            'cid' => $request->input('cid'),
            'uid' => $request->input('uid'),
            'email' => $request->input('email'),
            'extras' => $request->input('extras')
        ];

        $model = new UserShowroom;
        $model->cid = $request->input('cid');
        $model->uid = $request->input('uid');

        $checkRequest = UserShowroom::where('cid', $requestData['cid'])
            ->where('uid', $requestData['uid'])
            ->first();

        if (isset($checkRequest)) {
            if ($checkRequest->requested == 1) {
                $this->response = [
                    'code' => -1,
                    'error' => 'You already sent a request for this car',
                ];
            } else {

                // Save Into Dealer Showroom
                $saveIntoDealerPosts = DealerCarsResource::saveIntoDealerPosts($requestData);

                // Save Into User Showroom
                $updateUserShowroom = UserShowroom::where('uid', $requestData['uid'])
                    ->where('cid', $requestData['cid'])
                    ->update(['requested' => 1, 'request_date' => date("Y-m-d H:i:s")]);

                if ($saveIntoDealerPosts['status'] === 1 && $updateUserShowroom === 1) {

                    // Get car Information
                    $requestedCarInformation = AllCarsModel::where('id', $request->input('cid'))->first();

                    // Get User Information
                    $userInformation = DB::table('vfq0g_users')->where('email', $request->input('email'))->first();

                    // Find Dealers with requested car
                    $findDealers = DealerCarsResource::findCarDealers($requestedCarInformation, $userInformation);

                    // Return Mail Response
                    if ($findDealers['status'] === 1) {

                        $dealers = $findDealers['dealers'];

                        $dealerCollection = [];
                        foreach ($dealers as $dealer) {

                            // Get Dealer Information
                            $dealerInformation = DealersModel::where('id', $dealer->dealer_id)->first();
                            $dealerCollection[] = $dealerInformation;
                        }

                        foreach ($dealerCollection as $dealer) {
                            // Send Email To All Found Dealers
                            $this->response = UserRequestsResource::dealerEmailTemplate(
                            // Requested car information
                                $requestedCarInformation,
                                // User information
                                $userInformation,
                                // Default dealer
                                $dealer);
                        }

                        // Send confirmation email to user
                        UserRequestsResource::emailTemplate($userInformation, $requestedCarInformation);

                        // Send request email to super dealer
                        $superDealer = $findDealers['superDealer'];
                        SuperDealerResource::superDealerEmailTemplate(
                            // Requested car information
                            $requestedCarInformation,
                            // User information
                            $userInformation,
                            // Default dealer
                            $superDealer
                        );

                        // Return successful response
                        $this->response = [
                            'status' => 'Multiple dealers',
                            'successCode' => 200,
                            'message' => 'Request Successfully sent to dealers'
                        ];

                    } else {

                        // Send confirmation email to user
                        UserRequestsResource::emailTemplate($userInformation, $requestedCarInformation);

                        // Send request email to super dealer
                        $superDealer = $findDealers['superDealer'];
                        SuperDealerResource::superDealerEmailTemplate(
                        // Requested car information
                            $requestedCarInformation,
                            // User information
                            $userInformation,
                            // Default dealer
                            $superDealer
                        );

                        // Return successful response
                        $this->response = [
                            'status' => 'Super dealer only',
                            'successCode' => 200,
                            'message' => 'Request Successfully sent to dealers'
                        ];
                    }

                } else {
                    $this->response = "could not save and send";
                }
            }

        } else {
            $this->response = [
                'code' => -1,
                'error' => "We couldn't find the car you requesting quote for from your show room",
                'data' => $model
            ];
        }

        return $this->response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function placeRequest_2(Request $request)
    {

        $model = new UserShowroom;

        $requestInfo = [
            'cid' => $request->input('cid'),
            'uid' => $request->input('uid')
        ];


        $data = UserShowroom::IsExisting($requestInfo);

        if ($data['exists'] == false) {

            if (isset($data['entry'])) {
                $model_data = $data['entry'];
            }


            $added_post_results = \App\DealerShowroomPosts::addPost(
                $requestInfo['cid'], $requestInfo['uid']
            );

            $uid = $requestInfo['uid'];
            $cid = $requestInfo['cid'];

            $requestObject = [
                'uid' => $requestInfo['uid'],
                'cid' => $requestInfo['cid'],
                'requested' => 1,
                'request_date' => date("Y-m-d H:i:s")
            ];

            $request = UserShowroom::saveRequest($requestObject);

            if ($request === 'saved') {
                $response = [
                    'code' => 1,
                    'successMessage' => 'Request successfully sent to dealerships',
                    'data' => $model
                ];
            } else {
                $response = [
                    'code' => -1,
                    'error' => 'An error occurred please try again',
                    'data' => null
                ];
            }

            $sendEmail = UserShowroom::sendRequest($uid, $cid);

            if ($sendEmail === 1) {


            } else {
                $response = [
                    'code' => -1,
                    'error' => 'An email could not be sent please try again',
                    'data' => null
                ];
            }

        } else {
            $response = [
                'code' => -1,
                'error' => 'You already sent for request for this car',
                'data' => null
            ];
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function addNew(Request $request)
    {

        $uid = $request->input('uid');
        $cid = $request->input('cid');

        $data = UserShowroom::reqExists($uid, $cid);

        if ($data['message'] == true) {
            return [
                'code' => 1,
                'error' => 'Already Added'
            ];
        }

        $res = UserShowroom::create([
            'uid' => $uid,
            'cid' => $cid,
            'test_drive_date' => null,
            'requested' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return [
            'code' => ($res) ? 1 : -1,
            'error' => ''
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateProfile(Request $request)
    {

        if ($request->input('agreement') === 'on') {

            $updateData = [
                'agreement' => 1
            ];

            $userID = $request->input('userID');

            $updateable = [
                'name' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'identity' => '',
                'nationality' => $request->input('nationality'),
                'gender' => $request->input('gender'),
                'agreement' => $updateData['agreement'],
                'contactNumber' => $request->input('contactNumber'),
                'status' => $updateData['agreement'],
                'updateDateTime' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())
            ];

            $update = DB::table('vfq0g_users')
                ->where('id', $userID)
                ->update($updateable);

            if ($update == 1) {
                $userUpdatedData = CarnetUsers::where('email', $request->input('email'))->first();
                $UpdatedData = ProfileUpdateResource::emailTemplate($userUpdatedData);

                if ($UpdatedData === 'sent') {
                    $this->response = [
                        'successCode' => 255,
                        'successMessage' => 'Profile Updated',
                        'userData' => $userUpdatedData

                    ];
                } else {
                    $this->response = [
                        'errorCode' => 265,
                        'errorMessage' => 'Profile not updated'
                    ];
                }
            } else {
                $this->response = [
                    'errorCode' => 266,
                    'errorMessage' => 'Profile not updated'
                ];
            }
//            $update->name = $request->input('firstName');
//            $update->lastName = $request->input('lastName');
//            $update->email = $request->input('email');
//            $update->identity = '';
//            $update->nationality = $request->input('nationality');
//            $update->gender = $request->input('gender');
//            $update->agreement = $updateData['agreement'];
//            $update->contactNumber = $request->input('contactNumber');
//            $update->status = $updateData['agreement'];
//            $update->updateDateTime = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
//            return $update;
//            $update->save();

        } else {
            $this->response = [
                'errorCode' => 355,
                'errorMessage' => 'Please note that if you do not consent for a "Credit Check" you will not recieve offers from dealerships'
            ];
        }

        return $this->response;
    }

    /**
     * @param $offerID
     * @return array
     */
    public function interested($offerID)
    {

        $offerID = ['offer_id' => $offerID];
        $update = DealsResource::updateStatus($offerID);
	$offer = DealsModel::where('id', $offerID)->get();
        if ($update['response']['code'] === 1) {
// $this->createActiveChat($offerID);
            $this->response = [
                'successCode' => 400,
                'successMessage' => 'You can now book for a test drive or (Click on the Chat Room tab)',
                'response' => $update,
		'offer' => $offer
            ];

        } else if ($update['response'] === 0) {
            $this->response = [
                'errorCode' => 405,
                'errorMessage' => 'Deal not found.'
            ];
        } else {
            $this->response = [
                'errorCode' => 405,
                'errorMessage' => 'Something wrong happened'
            ];
        }

        return $this->response;
    }

    /**
     * @param $offerID
     * @return array
     */
    public function createActiveChat($offerID)
    {

        $getOffer = DealsModel::where('id', $offerID['offer_id'])->first();

        // return $getOffer;
        if ($getOffer !== null) {
            $newChat = new ActiveChats;
            $newChat->user_id = $getOffer->user_id;
            $newChat->dealer_id = $getOffer->dealer_id;
            $newChat->offer_id = $getOffer->id;
            $newChat->car_id = $getOffer->car_id;
            $newChat->request_id = $getOffer->request_id;
            $newChat->chat_id = $this->generateRandomString(10);

            $activeChats = ActiveChats::where('offer_id', $newChat->offer_id)->first();

            if ($activeChats === null || $activeChats->offer_id !== $newChat->offer_id) {
                $newChat->save();
                $activateChat = FireBaseController::activateChat($newChat);
                $this->response = [
                    'successMessage' => 'Chat successfully activated',
                    'successCode' => 202,
                    'activeChat' => $activateChat,
                    'dealerInformation' => DB::table('vfq0g_dealers')
                        ->select('id', 'email', 'name', 'location', 'status')
                        ->where('id', $activateChat['dealer_id'])
                        ->first()
                ];
            } else {
                $this->response = [
                    'errorMessage' => 'Chat already activated',
                    'errorCode' => 607
                ];
            }

        } else {
            $this->response = [
                'errorMessage' => 'Offer does not exist',
                'errorCode' => 907
            ];
        }

        return $this->response;
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $id
     * @return array
     */
    public function rejected($id) {
        $offerID = [ 'offer_id' => $id ];
        $this->response = DealsResource::rejectStatus($offerID);
        return $this->response;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCars($id)
    {

        $cars = CarSearch::getSearchCarsByIds_2($id);

        return $cars;
    }
}
