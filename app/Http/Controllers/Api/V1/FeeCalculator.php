<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Admin\Currency;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Admin\TransactionSetting;

class FeeCalculator extends Controller
{
    /**
     * My marketplace page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request){

        $fee = 0;

        $currencies = Currency::orderBy('default', 'ASC')->get()->map(function ($item){
            return [
                'id' => $item->id,
                'code' => $item->code,
                'rate' => getDynamicAmount($item->rate),
            ];
        });

        $default_currency = Currency::default();

        $default_currency = [
            'id' => $default_currency->id,
            'code' => $default_currency->code,
            'rate' => getDynamicAmount($default_currency->rate),
        ];

        $message = ['success' => ['Data fetch successful!']];

        $amount = 0.00;

        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        if(isset($request->amount) && isset($request->currency) && !empty($request->amount) && !empty($request->currency)){

            $currency = Currency::find($request->currency);
            $subtotal = $request->amount / $currency->rate;
            $amount = $request->amount;
            $charges = TransactionSetting::where('slug', 'my_excrow')->first();
            $intervals = $charges->intervals;

            if(isset($intervals)){
                $first_element = current($intervals);
                $last_element = end($intervals);
                $min_limit = $first_element->min_limit;
                $max_limit = $last_element->max_limit;
            }else{
                $min_limit = 0;
                $max_limit = 0;
            }

            $max_total = $max_limit * $currency->rate;

            if($subtotal > $max_limit){
                return ApiResponse::onlyError(['error' => ['Maximum fee calculation limit is: '.getDynamicAmount($max_total, $currency->code)]]);
            }

            $charges = feesAndChargeCalculation($intervals, $subtotal);

            $fee = $charges['total_charge'];
        }

        $data =[
            'currencies'       => $currencies,
            'default_currency' => $default_currency,
            'fee'              => getDynamicAmount($fee),
            'amount'           => getDynamicAmount($amount),
        ];

        return ApiResponse::success($message, $data);

    }
}
