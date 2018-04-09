<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Resources
use App\Http\Resources\FinanceResource;

class AffordabilityController extends Controller
{
    public $response;

    public function getAffodability(Request $request) {

        // User inputs
        $income = $request->input('income');
        $expenses = $request->input('expenses');
        $period = $request->input('period');

        $this->response = $this->affordabiltyCalculator($income, $expenses);

        return $this->response;
    }

    private function affordabiltyCalculator($income, $expenses) {

        $this->response = FinanceResource::financeCalculator($income, $expenses);

        return $this->response;
    }
}
