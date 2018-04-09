<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FinanceResource extends Resource {

    private static $ceiling = 0.3;
    private static $interest = 0.01042291;
    private static $standard;
    private static $remainder;

    public static $response;

    public static function financeCalculator($income, $expenses) {

        self::$standard = self::$ceiling * $income;
        self::$remainder = $income - $expenses;

        if(self::$standard < self::$ceiling) {
            echo self::$standard;
        } else {
            echo self::$remainder;
        }
        exit;
        

        return self::$response;
    }

    public static function vehicleValue() {

        return self::$response;
    }

    public static function monthlyInstallment() {

        return self::$response;
    }

}
