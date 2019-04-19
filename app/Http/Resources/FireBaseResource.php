<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FireBaseResource extends Resource
{

    public static $serviceAccount;
    private static $firebaseUri;

    public function __construct(mixed $resource)
    {
        parent::__construct($resource);
        self::$serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/google-service-account.json');
        self::$firebaseUri = 'https://carloyi-6928d.firebaseio.com';
    }

    public static function newOffer($data) {
        // This assumes that you have placed the Firebase credentials in the same directory
        // as this PHP file.

        $firebase = (new Factory)
            ->withServiceAccount(self::$serviceAccount)
    // The following line is optional if the project id in your credentials file
    // is identical to the subdomain of your Firebase project. If you need it,
    // make sure to replace the URL with the URL of your project.
            ->withDatabaseUri(self::$firebaseUri)
            ->create();

        $database = $firebase->getDatabase();

        $newPost = $database
            ->getReference('activechats/' . $data->chat_id)
            ->push([
                'user_id' => $data->user_id,
                'dealer_id' => $data->dealer_id,
                'car_id' => $data->car_id,
                'offer_id' => $data->offer_id,
                'request_id' => $data->request_id
            ]);

        return $newPost->getValue();

        $newPost->getKey(); // => -KVr5eu8gcTv7_AHb-3-
        $newPost->getUri(); // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-

        $newPost->getChild('title')->set('Changed post title');
        $newPost->getValue(); // Fetches the data from the realtime database
        $newPost->remove();
    }

    public static function newBooking($data) {
        // This assumes that you have placed the Firebase credentials in the same directory
        // as this PHP file.


        $firebase = (new Factory)
            ->withServiceAccount(self::$serviceAccount)
            // The following line is optional if the project id in your credentials file
            // is identical to the subdomain of your Firebase project. If you need it,
            // make sure to replace the URL with the URL of your project.
            ->withDatabaseUri('https://carloyi-6928d.firebaseio.com')
            ->create();

        $database = $firebase->getDatabase();

        $newPost = $database
            ->getReference('test_drives')
            ->push([
                'user_id' => $data->user_id,
                'dealer_id' => $data->dealer_id,
                'bookingDate' => $data->bookingDate,
                'offer_id' => $data->offer_id,
                'time' => $data->time,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at
            ]);

        return $newPost->getValue();

        // => -KVr5eu8gcTv7_AHb-3-
        // $newPost->getKey();

        // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-
        // $newPost->getUri();
        // $newPost->getChild('title')->set('Changed post title');

        // Fetches the data from the realtime database
        // $newPost->getValue();
        // $newPost->remove();
    }
}
