<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarSearch;
use Illuminate\Support\Facades\DB;
// Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Facades\Log;
// Resources
use App\Http\Resources\FinanceResource;

class AffordabilityController extends Controller
{
    public $response;

    private $userFinance;
    private $period = 60;

    public function __construct() {
        // create a log channel
        $this->logAffordability = new Logger('Affordability');
        $this->logAffordability->pushHandler(new RotatingFileHandler('../storage/logs/affordability.log'));
        $this->logAffordability->pushHandler(new FirePHPHandler());
    }

    public function getByAmount(Request $request) {
        $amount = [
            'price' => $request->input('amount'),
        ];

        $carsByPrice = DB::table('vfq0g_allcars')
        ->where('price', '<', $amount['price'])
        ->orderBy('total_score', 'desc')
        ->get();
    
        $this->response = [
          'successCode' => 204,
          'data' => $carsByPrice
        ];
    
        return $this->response;
    }

    public function getAffodability(Request $request) {


        // User inputs
        $this->userFinance = [
            'income' => $request->input('income'),
            'expenses' => $request->input('expenses')
        ];

        $this->response = $this->affordabiltyCalculator($this->userFinance);

        return $this->response;
    }

    private function affordabiltyCalculator(array $userFinance) {

        $this->response = FinanceResource::financeCalculator($userFinance);

        $this->logAffordability->info(
            json_encode([
                "income" => $userFinance['income'],
                "expenses" => $userFinance['expenses']
                    ]),
            ["finance_amount" => $this->response]
        );

        return $this->searchByAffordablePrice($this->response);
    }

    private function searchByAffordablePrice($affordablePrice) {
        // return $affordablePrice;
        $this->response = CarSearch::searchCarsByprice($affordablePrice);
        return $this->response;
    }
}
