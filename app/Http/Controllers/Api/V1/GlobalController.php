<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UserWallet;
use App\Models\Admin\Currency;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\Admin\TransactionSetting;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class GlobalController extends Controller
{
    public function currencyList(){
        $rate_currency = Currency::where('default', 1)->get()->map(function($item){
            return [
                'id'     => $item->id,
                'code'   => $item->code,
                'symbol' => $item->symbol,
                'flag'   => $item->flag ?? '',
                'rate'   => getDynamicAmount($item->rate),
            ];
        });


        $sale_currency = Currency::where('status', 1)->orderBy('default', 'asc')->get()->map(function($item){
            return [
                'id'     => $item->id,
                'code'   => $item->code,
                'symbol' => $item->symbol,
                'flag'   => $item->flag ?? '',
                'rate'   => getDynamicAmount($item->rate),
            ];
        });


        $wallet = UserWallet::with('currency')->where('user_id', Auth::id())->get()->map(function ($item){
            return [
                'id' => $item->id,
                'flag' => $item->currency->flag,
                'code' => $item->currency->code,
                'name' => $item->currency->name,
                'balance' => getDynamicAmount($item->balance),
            ];
        })->first();

        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        if(isset($intervals)){
            $first_element = current($intervals);
            $last_element = end($intervals);
            $min_limit = $first_element->min_limit;
            $max_limit = $last_element->max_limit;
        }else{
            $min_limit = '0';
            $max_limit = '0';
        }

        $data = [
            'base_url'      => url('/'),
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('currency-flag'),
            'rate_currency' => $rate_currency,
            'sale_currency' => $sale_currency,
            'wallet'        => $wallet,
            'trade_info'    => ['min_limit' => $min_limit, 'max_limit' => $max_limit]
        ];


        return ApiResponse::success(['success' => ['Data Fetch Successfully']], $data);
    }


    public function notificationList(){
        $notifications = UserNotification::where('user_id', Auth::guard(get_auth_guard())->user()->id)->latest('id')->take(4)->get()->map(function($item){
            return[
                'id'         => $item->id,
                'type'       => $item->type,
                'message'    => $item->message,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });


        $data = [
            'notifications' => $notifications,
        ];


        return ApiResponse::success(['success' => ['Data Fetch Successfully']], $data);

    }
}
