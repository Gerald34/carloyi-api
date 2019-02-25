<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FinanceResource extends Resource {

    private static $ceiling = (30 / 100); // System Overall Percetage
    private static $interest = 0.01042291; // System Overall Interest
    private static $base = (90 / 100);
    private static $monthlyInterest = (13.25 / 100);
    private static $term = 60;

    private static $percentage;

    public static $response;

    public static function financeCalculator(array $userFinance) {

        $percentage = self::getPercentage($userFinance['income']);
        $deduction = $userFinance['income'] - $userFinance['expenses'];
        
        if ($percentage < $deduction):
            self::$response = self::_getPrice($percentage);
        else:
            self::$response = self::_getPrice($deduction);
        endif;
        
        return self::$response;
    }

    /**
     * 
     * return number
     */
    public static function getPercentage(string $income) {
        //  Get 20% of user income
        return self::$ceiling * $income;
    }


    /**
     * 
     * return $price
     */
    private static function _getPrice($remainder) {

        $powerOf = pow((1 + self::$interest), -self::$term);
        $FinanceCalc = ((1 - $powerOf) / self::$interest ) / self::$base;
        $price = $remainder * $FinanceCalc;

        // return round($price);

        return ceil($price / 10000)  * 10000;
    }

    private static function _roundUp10($roundee) {
        $r = $roundee % 10;
        if ($r == 0):
          return $roundee;
        else:
            return $roundee + 10 - $r;
        endif;
    }

    public static function vehicleValue() {
        return self::$response;
    }

    public static function monthlyInstallment() {
        return self::$response;
    }

}
