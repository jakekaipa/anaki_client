<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Admin\Currency;
use App\Http\Controllers\Controller;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;

class FeesCalculator extends Controller
{
    /**
     * My marketplace page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request){

        $page_title = "Fee Calculator";

        $fee = 0;

        $currencies = Currency::orderBy('default', 'ASC')->get();
        $default_currency = Currency::default();
        $amount = 0.00;

        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        return view('user.sections.fee-calculator.index',compact("page_title", "default_currency","currencies",'intervals'));
    }
}
