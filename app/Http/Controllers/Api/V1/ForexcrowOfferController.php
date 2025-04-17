<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Forexcrow;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Models\ForexcrowOffer;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\TransactionSetting;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Notifications\User\MarketplaceMail;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Traits\PaymentGateway\ManualCrowPayment;

class ForexcrowOfferController extends Controller
{
    use ControlDynamicInputFields, ManualCrowPayment;

    /**
     * Get offer page show
     *
     * @method GET
     * @return Illuminate\Http\Request
     */
    public function index(){

        $user_id = Auth::guard(get_auth_guard())->user()->id;

        $get_offers = ForexcrowOffer::with('forexcrow','saleCurrency','rateCurrency','creator','receiver')
                                    ->whereHas('creator',function($q){
                                        $q->where('status', 1);
                                    })
                                    ->whereHas('receiver',function($q){
                                        $q->where('status', 1);
                                    })
                                    ->where('creator_id', $user_id)
                                    ->orWhere('receiver_id', $user_id)
                                    ->orderBy('id', 'desc')
                                    ->get()
        ->map(function ($item){
            $status_info = [
                '1' => 'Accept',
                '2' => 'Pending',
                '3' => 'Sold',
                '4' => 'Rejected',
            ];
            $trade_status_info = [
                "Ongoing"         => 1,
                "Pending"         => 2,
                "Rejected"        => 4,
                "Payment Pending" => 5,
                "Complete"        => 6,
                'Close Requested' => 7,
                'Closed'          => 8,
            ];

            return[
                'id'                 => $item->id,
                'type'               => $item->type,
                'amount'             => getDynamicAmount($item->amount),
                'sale_currency_code' => $item->saleCurrency->code,
                'rate'               => getDynamicAmount($item->rate),
                'rate_currency_code' => $item->rateCurrency->code,
                'creator_id'         => $item->creator_id,
                'for_user_id'        => $item->for_user_id,
                'receiver_id'        => $item->receiver_id,
                'trade_id '          => $item->forexcrow_id,
                'status_info'        => $status_info,
                'status'             => $item->status,
                'offer_created'      => $item->created_at,
                'creator_image'      => $item->creator->image,
                'email_verified'     => $item->creator->email_verified,
                'kyc_verified'       => $item->creator->kyc_verified,
                'creator_name'       => $item->creator->firstname.' '. $item->creator->lastname,
                'trade_amount'       => getDynamicAmount($item->forexcrow->amount),
                'trade_rate'         => getDynamicAmount($item->forexcrow->rate),
                'trade_status_info'  => $trade_status_info,
                'trade_status'       => $item->forexcrow->status,
            ];
        });


        $data = [
            'default_image' => "public/frontend/images/default/profile-default.webp",
            "image_path"    => "public/frontend/user",
            'get_offers'    => $get_offers,
        ];



        $message = ['success' => ['Data fetch successfully!']];
        return ApiResponse::success($message, $data);
    }

    /**
     * offerSubmit
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function offerSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'rate'        => 'required',
            'trade_id'    => 'required',
            'type'        => 'required',
        ]);

        $user_id = Auth::guard(get_auth_guard())->user()->id;

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $trade = Forexcrow::find($validated['trade_id']);

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        try {

            if($validated['type'] == 'COUNTER_OFFER'){
                if($validated['receiver_id'] == $user_id){
                    $receiver_id = $validated['creator_id'];
                }else{
                    $receiver_id = $validated['receiver_id'];
                }
            }else{
                $receiver_id = $trade->user_id;
                if($trade->user_id == $user_id){
                    return ApiResponse::onlyError(['error' => ['Can not send offer request for your trade']]);
                }
            }

            ForexcrowOffer::create([
                'type'             => $validated['type'],
                'forexcrow_id'     => $trade->id,
                'creator_id'       => $user_id,
                'receiver_id'      => $receiver_id,
                'for_user_id'      => $trade->user_id,
                'sale_currency_id' => $trade->currency_id,
                'amount'           => $trade->amount,
                'rate'             => $validated['rate'],
                'rate_currency_id' => $trade->rate_currency_id,
                'status'           => 2,
            ]);


            $notification_content = [
                'title'   => __('Offer'),
                'message' => __('Offer Price: :offer_amount :sale_currency @ :offer_rate :rate_currency. Selling Price: :sell_amount :sale_currency @ :sell_rate :rate_currency', [
                    'offer_amount'   => getDynamicAmount($trade->amount),
                    'sale_currency'  => $trade->saleCurrency->code,
                    'offer_rate'     => getDynamicAmount($validated['rate']),
                    'rate_currency'  => $trade->rateCurrency->code,
                    'sell_amount'    => getDynamicAmount($trade->amount),
                    'sell_rate'      => getDynamicAmount($trade->rate)
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'      => NotificationConst::OFFER,
                'user_id'  =>  $receiver_id,
                'message'   => $notification_content,
            ]);


        } catch (\Exception $th) {
            return ApiResponse::onlyError(['error' => 'Something went wrong!. Please try again.']);
        }

        return ApiResponse::onlySuccess(['success' => ['Your counter offer sent successfully!']]);
    }

     /**
     * offerSubmit
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function counterSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'rate'        => 'required',
            'trade_id'    => 'required',
            'type'        => 'required',
            'receiver_id' => 'required',
            'creator_id'  => 'required',
        ]);

        $user_id = Auth::guard(get_auth_guard())->user()->id;

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $trade = Forexcrow::find($validated['trade_id']);

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        try {

            if($validated['type'] == 'COUNTER_OFFER'){
                if($validated['receiver_id'] == $user_id){
                    $receiver_id = $validated['creator_id'];
                }else{
                    $receiver_id = $validated['receiver_id'];
                }
            }else{
                $receiver_id = $trade->user_id;
                if($trade->user_id == $user_id){
                    return ApiResponse::onlyError(['error' => ['Can not send offer request for your trade']]);
                }
            }

            ForexcrowOffer::create([
                'type'             => $validated['type'],
                'forexcrow_id'     => $trade->id,
                'creator_id'       => $user_id,
                'receiver_id'      => $receiver_id,
                'for_user_id'      => $trade->user_id,
                'sale_currency_id' => $trade->currency_id,
                'amount'           => $trade->amount,
                'rate'             => $validated['rate'],
                'rate_currency_id' => $trade->rate_currency_id,
                'status'           => 2,
            ]);

            $notification_content = [
                'title'   => __('Counter Offer'),
                'message' => __('Offer Price: :offer_amount :sale_currency @ :offer_rate :rate_currency. Selling Price: :sell_amount :sale_currency @ :sell_rate :rate_currency', [
                    'offer_amount'   => getDynamicAmount($trade->amount),
                    'sale_currency'  => $trade->saleCurrency->code,
                    'offer_rate'     => getDynamicAmount($validated['rate']),
                    'rate_currency'  => $trade->rateCurrency->code,
                    'sell_amount'    => getDynamicAmount($trade->amount),
                    'sell_rate'      => getDynamicAmount($trade->rate)
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'      => NotificationConst::COUNTER_OFFER,
                'user_id'  =>  $receiver_id,
                'message'   => $notification_content,
            ]);


        } catch (\Exception $th) {
            return ApiResponse::onlyError(['error' => 'Something went wrong!. Please try again.']);
        }

        return ApiResponse::onlySuccess(['success' => ['Your counter offer sent successfully!']]);
    }

     /**
     * offer status change
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function offerStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'target' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();


        if($validated['type'] == 'Reject'){
            $status = 4;
            $message = ['success' => ['Trade offer reject successfully!']];
        }elseif($validated['type'] == 'Accept')
            $status = 1;
            $message = ['success' => ['Trade offer accept successfully!']];
        try {
            $forexcrow_offer = ForexcrowOffer::find($validated['target']);

            if(!$forexcrow_offer){
                return ApiResponse::error(['error' => ['Trade offer is not found!']]);
            }

            $forexcrow_offer->update([
                'status' => $status,
            ]);

        } catch (\Exception $th) {
            return ApiResponse::error(['error' => ['Something went wrong, Please try again!']]);
        }

        return ApiResponse::onlySuccess($message);
    }

    /**
     * Offer buy
     *
     * @method POST
     * @return Illuminate\Http\Response
     */

     public function buy(Request $request){

        $validator = Validator::make($request->all(),[
            'target'   => 'required',
        ]);

        $user_id = Auth::guard(get_auth_guard())->user()->id;

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $trade = ForexcrowOffer::with('saleCurrency','rateCurrency','forexcrow')->where('id', $validated['target'])->get()->map(function ($item){
            $sale_currency = [
                'id'     => $item->saleCurrency->id,
                'code'   => $item->saleCurrency->code,
                'symbol' => $item->saleCurrency->symbol,
                'flag'   => $item->saleCurrency->flag ?? '',
                'rate'   => getDynamicAmount($item->saleCurrency->rate),
            ];
            $rate_currency = [
                'id'     => $item->rateCurrency->id,
                'code'   => $item->rateCurrency->code,
                'symbol' => $item->rateCurrency->symbol,
                'flag'   => $item->rateCurrency->flag ?? '',
                'rate'   => getDynamicAmount($item->rateCurrency->rate),
            ];
            return[
                'id'            => $item->id,
                'amount'        => getDynamicAmount($item->amount),
                'rate'          => getDynamicAmount($item->rate),
                'sale_currency' => $sale_currency,
                'rate_currency' => $rate_currency,
                'user_id'       => $item->forexcrow->user_id,
            ];
        })->first();

        $trade = (object) $trade;

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        if($trade->user_id == $user_id){
            return ApiResponse::onlyError(['error' => ['Can not buy your Trade!']]);
        }

        $wallet = UserWallet::where('user_id', $user_id)->get()->map(function ($item){
            return [
                'id' => $item->id,
                'balance' => $item->balance,
            ];
        });

        // Charge calcualtion
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        $charges = feesAndChargeCalculation($intervals, $trade->rate);
        $total_charge = $charges['total_charge'];

        $currency_code = $trade->sale_currency['code'];
        $payment_gatewaies = PaymentGateway::with('currencies')
                                            ->where('type', PaymentGatewayConst::MANUAL)
                                            ->where('slug', PaymentGatewayConst::receiving_method_slug())
                                            ->whereHas('currencies', function($q) use ($currency_code) {
                                                $q->where('currency_code', $currency_code);
                                            })
                                            ->get()
                                            ->map(function($item){
                                                return[
                                                    'id' => $item->id,
                                                    'name' => $item->name,
                                                ];
                                            });

        if($payment_gatewaies->isEmpty()){
            return ApiResponse::onlyError(['error' => ['Payment gateway not found!']]);
        }


        $data = [
            'payment_gatewaies' => $payment_gatewaies,
            'wallet'            => $wallet,
            'total_charge'      => $total_charge,
            'trade'             => $trade,
            'target'            => $validated['target'],
        ];

        return ApiResponse::success(['success' => ['Data fetch successfully!']], $data);

    }


     /**
     * My marketplace confirm
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function confirm(Request $request){

        $validator = Validator::make($request->all(),[
            'target'     => 'required',
            'gateway_id' => 'required',
        ]);

        $user_id = Auth::guard(get_auth_guard())->user()->id;

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $payment_gateway = PaymentGateway::find($validated['gateway_id']);

        if(!$payment_gateway){
            return ApiResponse::onlyError(['error' => ['Payment gateway not found!']]);
        }

        $trade = ForexcrowOffer::find($validated['target']);

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        $wallet = UserWallet::where('user_id', $user_id)->first();

        if(!$wallet){
            return ApiResponse::onlyError(['error' => ['Your Wallet not found!']]);
        }

        $validated['trade_id']           = $trade->forexcrow->id;
        $validated['rate_currency']      = $trade->rateCurrency->code;
        $validated['payment_gateway_id'] = $payment_gateway->id;
        $validated['payment_gateway_currency_id'] = $payment_gateway->currency->id;
        $validated['will_get']           = getDynamicAmount($trade->amount);
        $validated['sale_currency']      = $trade->saleCurrency->code;
        $validated['amount']             = getDynamicAmount($trade->rate);
        $validated['subtotal']           = getDynamicAmount($trade->rate);
        $charges                         = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals                       = $charges->intervals;
        $charges                         = feesAndChargeCalculation($intervals, $validated['subtotal']);
        $validated['wallet']             = $wallet;
        $validated['fixed_charge']       = $charges['fixed_charge'];
        $validated['percent_charge']     = $charges['percent_charge'];
        $validated['total_charge']       = $charges['total_charge'];
        $validated['total_amount']       = $validated['total_charge'] + $validated['subtotal'];
        $validated['transaction_type']   = PaymentGatewayConst::MARKETPLACE;

        if($wallet->balance < $validated['total_amount']){
            return ApiResponse::onlyError(['error' => ['Insufficient wallet balance!']]);
        }

        $payment_fields = $payment_gateway->input_fields ?? [];

        try {
            TemporaryData::where('user_id', $user_id)->where('type', PaymentGatewayConst::MARKETPLACE)->delete();
            $identifier = generate_unique_string("transactions","trx_id",16);

            TemporaryData::create([
                'user_id'       => Auth::id(),
                'type'          => PaymentGatewayConst::MARKETPLACE,
                'identifier'    => $identifier,
                'data'          => $validated,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::onlyError(['success' => ['Something went wrong, Please try again!']]);
        }

        $data = [
            'trx_id'         => $identifier,
            'payment_fields' => $payment_fields,
            'data'           => $validated,
        ];

        return ApiResponse::success(['success' => ['Transaction insert successfully!']], $data);
    }

     /**
     * My forexcrow confirm
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function evidenceSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'trx_id'           => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $identifier = TemporaryData::where('identifier', $validated['trx_id'])->where('type', PaymentGatewayConst::MARKETPLACE)->first();

        if(!$identifier){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        $data = $identifier->data;

        $payment_gateway = PaymentGateway::find($data->payment_gateway_id);

        $payment_fields = $payment_gateway->input_fields ?? [];
        $validation_rules       = $this->generateValidationRules($payment_fields);
        $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
        $get_values             = $this->placeValueWithFields($payment_fields,$payment_field_validate);

        try {
            $trx_id = $this->insertRecordManual($data, $data->trade_id,'MT',1, $get_values);
            $this->insertChargesManual($data,$trx_id,NotificationConst::MARKETPLACE_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($data->total_amount, $data->wallet);
            $this->removeTempDataManual($validated['trx_id']);

            Forexcrow::find($data->trade_id)->update([
                'status' => 6,
            ]);

            $notification_content = [
                'title'   => __('Transaction Created'),
                'message' => __('Purchase Amount: :purchase_amount :sale_currency. Rate Amount: :rate_amount :rate_currency', [
                    'purchase_amount' => getDynamicAmount($data->will_get),
                    'sale_currency'   => $data->sale_currency,
                    'rate_amount'     => getDynamicAmount($data->amount),
                    'rate_currency'   => $data->rate_currency
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'      => NotificationConst::TRANSACTION_CREATED,
                'user_id'  =>  Auth::guard(get_auth_guard())->user()->id,
                'message'   => $notification_content,
            ]);

            // Mail send
            $user = auth()->user();
            $basic_settings = BasicSettingsProvider::get();
            if($basic_settings->email_notification == true){
                $user->notify(new MarketplaceMail($user, (array) $data, $validated['trx_id']));
            }
        } catch (\Exception $e) {
            return ApiResponse::onlyError(['error' => ['Something went wrong, Please try again!!']]);
        }

        return ApiResponse::onlySuccess(['success' => ['Transaction create successfully!']]);
    }

}
