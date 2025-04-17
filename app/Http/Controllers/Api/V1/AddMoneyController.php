<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Models\Admin\Currency;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentGateway\Manual;
use App\Traits\PaymentGateway\Stripe;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;

class AddMoneyController extends Controller
{
    use Stripe,Manual;

    /**
     * Add Money History
     *
     * @method GET
     * @return \Illuminate\Http\Response
     */
    public function addMoneyInformation(){
        $user = auth()->user();
        $userWallet = UserWallet::with('currency')->where('user_id',$user->id)->get()->map(function($data){
            return[
                'base_url'      => url('/'),
                'default_image' => get_files_public_path('default'),
                'image_path'    => get_files_public_path('currency-flag'),
                'flag'          => $data->currency->flag,
                'name'          => $data->currency->name,
                'balance'       => getDynamicAmount($data->balance),
                'currency'      => get_default_currency_code(),
            ];
        })->first();

        $transactions = Transaction::auth()->addMoney()->latest()->take(5)->get()->map(function($item){
            $statusInfo = [
                "success" =>      1,
                "pending" =>      2,
                "rejected" =>     4,
            ];
            return[
                'id'               => $item->id,
                'trx'              => $item->trx_id,
                'gateway_name'     => $item->currency->name,
                'transaction_type' => $item->type,
                'request_amount'   => getDynamicAmount($item->request_amount).' '.get_default_currency_code(),
                'payable'          => getDynamicAmount($item->payable).' '.$item->user_wallet->currency->code,
                'exchange_rate'    => '1 ' .get_default_currency_code().' = '.getDynamicAmount($item->currency->rate).' '.$item->currency->currency_code,
                'total_charge'     => getDynamicAmount($item->charge->total_charge ?? 0).' '.$item->user_wallet->currency->code,
                'current_balance'  => getDynamicAmount($item->available_balance).' '.get_default_currency_code(),
                'status'           => $item->stringStatus->value,
                'status_info'      => (object)$statusInfo,
                'rejection_reason' => $item->reject_reason??"",
                'created_at'        => $item->created_at,
            ];
        });


        $gateways = PaymentGateway::where('status', 1)->where('slug', PaymentGatewayConst::add_money_slug())->get()->map(function($gateway){
            $currencies = PaymentGatewayCurrency::where('payment_gateway_id',$gateway->id)->get()->map(function($data){
                return[
                    'id'                 => $data->id,
                    'payment_gateway_id' => $data->payment_gateway_id,
                    'type'               => $data->gateway->type,
                    'name'               => $data->name,
                    'alias'              => $data->alias,
                    'currency_code'      => $data->currency_code,
                    'currency_symbol'    => $data->currency_symbol,
                    'image'              => $data->image,
                    'min_limit'          => getAmount($data->min_limit, 8),
                    'max_limit'          => getAmount($data->max_limit, 8),
                    'percent_charge'     => getAmount($data->percent_charge, 8),
                    'fixed_charge'       => getAmount($data->fixed_charge, 8),
                    'rate'               => getAmount($data->rate, 8),
                    'created_at'         => $data->created_at,
                    'updated_at'         => $data->updated_at,
                ];
            });

            return[
                'id'                   => $gateway->id,
                'image'                => $gateway->image,
                'slug'                 => $gateway->slug,
                'code'                 => $gateway->code,
                'type'                 => $gateway->type,
                'alias'                => $gateway->alias,
                'supported_currencies' => $gateway->supported_currencies,
                'status'               => $gateway->status,
                'currencies'           => $currencies

            ];
        });

        $data =[
            'base_curr'      => get_default_currency_code(),
            'base_curr_rate' => get_amount(1),
            'default_image'  => "public/backend/images/default/default.webp",
            'image_path'     => "public/backend/images/payment-gateways",
            'base_url'       => url('/'),
            'userWallet'     => (object)$userWallet,
            'gateways'       => $gateways,
            'transactionss'  => $transactions,
        ];

        return ApiResponse::success(['success'=>['Add Money Information!']], $data);
    }

    /**
     * Add Money Form Submit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitData(Request $request) {

        $validator = Validator::make($request->all(), [
           'currency'  => "required",
           'amount'        => "required|numeric",
        ]);

       if($validator->fails()){
           $error =  ['error'=>$validator->errors()->all()];
           return ApiResponse::onlyValidation($error);
       }

       $alias = $request->currency;
       $amount = $request->amount;

       $payment_gateways_currencies = PaymentGatewayCurrency::where('alias',$alias)->whereHas('gateway', function ($gateway) {
           $gateway->where('slug', PaymentGatewayConst::add_money_slug());
           $gateway->where('status', 1);
        })->first();

       if( !$payment_gateways_currencies){
            $error = ['error'=>['Gateway Information is not available. Please provide payment gateway currency alias']];
            return ApiResponse::onlyError($error);
       }

       $user_wallet = UserWallet::auth()->first();
       $user = Auth::guard(get_auth_guard())->user();

       if(!$user_wallet) {
           $error = ['error'=>['User wallet not found!']];
           return ApiResponse::onlyError($error);
       }

       if($amount < ($payment_gateways_currencies->min_limit / $payment_gateways_currencies->rate) || $amount > ($payment_gateways_currencies->max_limit/$payment_gateways_currencies->rate)) {
           $error = ['error'=>['Please follow the transaction limit']];
           return ApiResponse::onlyError($error);
       }

       try{
           $instance = PaymentGatewayHelper::init($request->all())->gateway()->api()->get();

           $trx = $instance['response']['id'] ?? $instance['response']['trx'] ?? $instance['response']['reference_id'] ?? '';
            $temData = TemporaryData::where('identifier',$trx)->first();
           if(!$temData){
               $error = ['error'=>["Invalid Request"]];
               return ApiResponse::onlyError($error);
           }
           $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
           $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();
           if($payment_gateway->type == "AUTOMATIC") {
               if($temData->type == PaymentGatewayConst::STRIPE) {
                $payment_informations =[
                    'trx' =>  $temData->identifier,
                    'gateway_currency_name' =>  $payment_gateway_currency->name,
                    'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                    'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                    'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                    'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                    'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                ];
                $data =[
                    'gateway_type' => $payment_gateway->type,
                    'gateway_currency_name' => $payment_gateway_currency->name,
                    'alias' => $payment_gateway_currency->alias,
                    'identify' => $temData->type,
                    'payment_informations' => $payment_informations,
                    'url' => @$temData->data->response->link."?prefilled_email=".@$user->email,
                    'method' => "get",
                ];
                $message =  ['success'=>['Add Money Inserted Successfully']];
                return ApiResponse::success($message, $data);
               }else if($temData->type == PaymentGatewayConst::PAYPAL) {

                   $payment_informations = [
                        'trx'                   => $temData->identifier,
                        'gateway_currency_name' => $payment_gateway_currency->name,
                        'request_amount'        => getDynamicAmount($temData->data->amount->requested_amount).' '.$temData->data->amount->default_currency,
                        'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getDynamicAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                        'total_charge'          => getDynamicAmount($temData->data->amount->total_charge).' '.$temData->data->amount->sender_cur_code,
                        'will_get'              => getDynamicAmount($temData->data->amount->will_get).' '.$temData->data->amount->default_currency,
                        'payable_amount'        => getDynamicAmount($temData->data->amount->total_amount).' '.$temData->data->amount->sender_cur_code,
                   ];
                   $data =[
                        'gategay_type'          => $payment_gateway->type,
                        'gateway_currency_name' => $payment_gateway_currency->name,
                        'alias'                 => $payment_gateway_currency->alias,
                        'identify'              => $temData->type,
                        'payment_informations'  => $payment_informations,
                        'url'                   => @$temData->data->response->links,
                        'method'                => "get",
                   ];
                   $message =  ['success'=>['Add Money Inserted Successfully']];
                   return ApiResponse::success($message, $data);

               }else if($temData->type == PaymentGatewayConst::FLUTTER_WAVE) {
                   $payment_informations =[
                       'trx'                   => $temData->identifier,
                       'gateway_currency_name' => $payment_gateway_currency->name,
                       'request_amount'        => getDynamicAmount($temData->data->amount->requested_amount).' '.$temData->data->amount->default_currency,
                       'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getDynamicAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                       'total_charge'          => getDynamicAmount($temData->data->amount->total_charge).' '.$temData->data->amount->sender_cur_code,
                       'will_get'              => getDynamicAmount($temData->data->amount->will_get).' '.$temData->data->amount->default_currency,
                       'payable_amount'        => getDynamicAmount($temData->data->amount->total_amount).' '.$temData->data->amount->sender_cur_code,
                   ];
                   $data =[
                       'gateway_type'          => $payment_gateway->type,
                       'gateway_currency_name' => $payment_gateway_currency->name,
                       'alias'                 => $payment_gateway_currency->alias,
                       'identify'              => $temData->type,
                       'payment_informations'  => $payment_informations,
                       'url'                   => @$temData->data->response->link,
                       'method'                => "get",
                   ];
                   $message =  ['success'=>['Add Money Inserted Successfully']];
                   return ApiResponse::success($message, $data);
               }else if($temData->type == PaymentGatewayConst::RAZORPAY){
                    $payment_informations =[
                        'trx' =>  $temData->identifier,
                        'gateway_currency_name' =>  $payment_gateway_currency->name,
                        'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                        'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                        'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                        'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                        'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                    ];
                    $data =[
                        'gateway_type' => $payment_gateway->type,
                        'gateway_currency_name' => $payment_gateway_currency->name,
                        'alias' => $payment_gateway_currency->alias,
                        'identify' => $temData->type,
                        'payment_informations' => $payment_informations,
                        'url' => @$instance['response']['short_url'],
                        'method' => "get",
                    ];
                    $message =  ['success'=>['Add Money Inserted Successfully']];
                    return ApiResponse::success($message,$data);

                }else if ($temData->type == PaymentGatewayConst::QRPAY) {
                    $payment_informations = [

                        'trx' =>  $temData->identifier,
                        'gateway_currency_name' =>  $payment_gateway_currency->name,
                        'request_amount' => getAmount($temData->data->amount->requested_amount, 4) . ' ' . $temData->data->amount->default_currency,
                        'exchange_rate' => "1" . ' ' . $temData->data->amount->default_currency . ' = ' . getAmount($temData->data->amount->sender_cur_rate, 4) . ' ' . $temData->data->amount->sender_cur_code,
                        'total_charge' => getAmount($temData->data->amount->total_charge, 4) . ' ' . $temData->data->amount->sender_cur_code,
                        'will_get' => getAmount($temData->data->amount->will_get, 4) . ' ' . $temData->data->amount->default_currency,
                        'payable_amount' =>  getAmount($temData->data->amount->total_amount, 4) . ' ' . $temData->data->amount->sender_cur_code,
                    ];
                    $data = [
                        'gateway_type' => $payment_gateway->type,
                        'gateway_currency_name' => $payment_gateway_currency->name,
                        'alias' => $payment_gateway_currency->alias,
                        'identify' => $temData->type,
                        'payment_informations' => $payment_informations,
                        'url' => @$instance['response']['link'],
                        'method' => "get",
                    ];
                    $message =  ['success' => ['Add Money Inserted Successfully']];
                    return ApiResponse::success($message, $data);
                }elseif($temData->type == PaymentGatewayConst::SSLCOMMERZ) {
                    $payment_informations =[
                        'trx' =>  $temData->identifier,
                        'gateway_currency_name' =>  $payment_gateway_currency->name,
                        'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                        'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                        'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                        'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                        'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                    ];
                    $data =[
                        'gateway_type' => $payment_gateway->type,
                        'gateway_currency_name' => $payment_gateway_currency->name,
                        'alias' => $payment_gateway_currency->alias,
                        'identify' => $temData->type,
                        'payment_informations' => $payment_informations,
                        'url' => $instance['response']['link'],
                        'method' => "get",
                    ];
                    $message =  ['success'=>['Add Money Inserted Successfully']];
                    return ApiResponse::success($message,$data);
                }
           }elseif($payment_gateway->type == "MANUAL"){

                   $payment_informations =[
                       'trx'                   => $temData->identifier,
                       'gateway_currency_name' => $payment_gateway_currency->name,
                       'request_amount'        => getDynamicAmount($temData->data->amount->requested_amount).' '.$temData->data->amount->default_currency,
                       'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getDynamicAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                       'total_charge'          => getDynamicAmount($temData->data->amount->total_charge).' '.$temData->data->amount->sender_cur_code,
                       'will_get'              => getDynamicAmount($temData->data->amount->will_get).' '.$temData->data->amount->default_currency,
                       'payable_amount'        => getDynamicAmount($temData->data->amount->total_amount).' '.$temData->data->amount->sender_cur_code,
                   ];
                   $data =[
                       'gategay_type'          => $payment_gateway->type,
                       'gateway_currency_name' => $payment_gateway_currency->name,
                       'alias'                 => $payment_gateway_currency->alias,
                       'identify'              => $temData->type,
                       'details'               => $payment_gateway->desc??null,
                       'input_fields'          => $payment_gateway->input_fields??null,
                       'payment_informations'  => $payment_informations,
                       'url'                   => route('api.v1.user.add-money.manual.payment.confirmed'),
                       'method'                => "post",
                       ];
                       $message =  ['success'=>['Add Money Inserted Successfully']];
                       return ApiResponse::success($message, $data);
           }else{
               $error = ['error'=>["Something is wrong"]];
               return ApiResponse::onlyError($error);
           }

       }catch(Exception $e) {
           $error = ['error'=>[$e->getMessage()]];
           return ApiResponse::onlyError($error);
       }
       // return $instance;
   }

   public function success(Request $request, $gateway)
   {
       $requestData = $request->all();
       $token = $requestData['token'] ?? "";
       $checkTempData = TemporaryData::where("type", $gateway)->where("identifier", $token)->first();
       if (!$checkTempData){
           $message = ['error' => ["Transaction failed. Record didn\'t saved properly. Please try again."]];
           return ApiResponse::onlyError($message);
       }

       $checkTempData = $checkTempData->toArray();
       try {
           PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceiveApi();
       } catch (Exception $e) {
           $message = ['error' => [$e->getMessage()]];
           return ApiResponse::onlyError($message);
       }
       $message = ['success' => ["Payment successful, please go back your app"]];
       return ApiResponse::onlySuccess($message);
   }

   public function cancel(Request $request, $gateway)
   {
       $message = ['error' => ["Something is worng"]];
       return ApiResponse::onlyError($message);
   }

   public function flutterwaveCallback()
   {

       $status = request()->status;

       if ($status ==  'successful') {

           $transactionID = Flutterwave::getTransactionIDFromCallback();
           $data = Flutterwave::verifyTransaction($transactionID);

           $requestData = request()->tx_ref;

           $token = $requestData;

           $checkTempData = TemporaryData::where("type",'flutterwave')->where("identifier",$token)->first();

           $message = ['error' => ['Transaction faild. Record didn\'t saved properly. Please try again.']];

           if(!$checkTempData) return ApiResponse::error($message);

           $checkTempData = $checkTempData->toArray();
           try{
                PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('flutterWave');
           }catch(Exception $e) {
                $message = ['error' => [$e->getMessage()]];
                ApiResponse::error($message);
           }
            $message = ['success' => ["Payment successful"]];
            return ApiResponse::onlySuccess($message);
       }
       elseif ($status ==  'cancelled'){
            $message = ['error' => ['Payment Cancelled']];
            ApiResponse::error($message);
       }
       else{
            $message = ['error' => ['Payment Failed']];
            ApiResponse::error($message);
       }
   }

    //stripe success
    public function stripePaymentSuccess($trx){
        $token = $trx;
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::STRIPE)->where("identifier",$token)->first();
        $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];

        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        try{
            PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceiveApi('stripe');
        }catch(Exception $e) {
            $message = ['error' => ['Something Is Wrong...']];
            ApiResponse::error($message);
        }
        $message = ['success' => ["Payment Successful, Please Go Back Your App"]];
        return ApiResponse::onlysuccess($message);
    }


    //sslcommerz success
    public function sllCommerzSuccess(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if( $data['status'] != "VALID"){
            $message = ['error' => ["Added Money Failed"]];
            return ApiResponse::error($message);
        }
        try{
            PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceiveApi('sslcommerz');
        }catch(Exception $e) {
            $message = ['error' => ['Something Is Wrong...']];
            return ApiResponse::error($message);
        }
        $message = ['success' => ["Payment Successful, Please Go Back Your App"]];
        return ApiResponse::onlySuccess($message);
    }

    //sslCommerz fails
    public function sllCommerzFails(Request $request){
        $data = $request->all();

        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if( $data['status'] == "FAILED"){
            TemporaryData::destroy($checkTempData['id']);
            $message = ['error' => ["Added Money Failed"]];
            return ApiResponse::error($message);
        }

    }
    //sslCommerz canceled
    public function sllCommerzCancel(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if($data['status'] != "VALID"){
            TemporaryData::destroy($checkTempData['id']);
            $message = ['error' => ["Added Money Canceled"]];
            return ApiResponse::error($message);
        }
    }



    public function razorCallback()
    {
        $request_data = request()->all();
        //if payment is successful
        if ($request_data['razorpay_payment_link_status'] ==  'paid') {
            $token = $request_data['razorpay_payment_link_reference_id'];

            $checkTempData = TemporaryData::where("type",PaymentGatewayConst::RAZORPAY)->where("identifier",$token)->first();
            if(!$checkTempData) {
                $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
                return ApiResponse::error($message);
            }
            $checkTempData = $checkTempData->toArray();
            try{
                PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceiveApi('razorpay');
            }catch(Exception $e) {
                $message = ['error' => [$e->getMessage()]];
                return ApiResponse::error($message);
            }
            $message = ['success' => ["Payment Successful, Please Go Back Your App"]];
            return ApiResponse::onlySuccess($message);

        }
        else{
            $message = ['error' => ['Payment Failed']];
            return ApiResponse::error($message);
        }
    }


    public function qrpayCallback(Request $request)
    {
        if ($request->type ==  'success') {

            $requestData = $request->all();
            $checkTempData = TemporaryData::where("type", 'qrpay')->where("identifier", $requestData['data']['custom'])->first();
            $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
            if (!$checkTempData) return ApiResponse::error($message);
            $checkTempData = $checkTempData->toArray();
            try {
                PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('qrpay');
            } catch (Exception $e) {
                $message = ['error' => [$e->getMessage()]];
                return ApiResponse::error($message);
            }
            $message = ['success' => ["Payment Successful, Please Go Back Your App"]];
            return ApiResponse::onlySuccess($message);
        } else {
            $message = ['error' => ['Payment Failed']];
            return ApiResponse::error($message);
        }
    }

    public function qrpayCancel(Request $request, $trx_id)
    {
        $checkTempData = TemporaryData::where("identifier", $trx_id)->delete();
        $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
        return ApiResponse::error($message);
    }



}
