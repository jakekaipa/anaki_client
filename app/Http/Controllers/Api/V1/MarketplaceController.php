<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Forexcrow;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use function PHPUnit\Framework\isEmpty;
use App\Models\Admin\TransactionSetting;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Notifications\User\MarketplaceMail;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Traits\PaymentGateway\ManualCrowPayment;

class MarketplaceController extends Controller
{
    use ControlDynamicInputFields, ManualCrowPayment;
     /**
     * My marketplace page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request){

        $query = Forexcrow::where('status', 1);

        if(isset($request->id) && !empty($request->id)){
            $query->where('id', $request->id);
        }else{
            if(!isset($request->order_by) && empty($request->order_by)){
                $query->orderBy('id', 'desc');
            }
            if(isset($request->max_amount) && !empty($request->max_amount)){
                $query->where('amount', '<=' ,$request->max_amount);
            }
            if(isset($request->min_amount) && !empty($request->min_amount)){
                $query->where('amount', '>=' ,$request->min_amount);
            }
            if(isset($request->max_rate) && !empty($request->max_rate)){
                $query->where('rate', '<=' ,$request->max_rate);
            }
            if(isset($request->min_rate) && !empty($request->min_rate)){
                $query->where('rate', '>=' ,$request->min_rate);
            }
            if(isset($request->currency) && !empty($request->currency)){
                $query->where('currency_id', $request->currency);
            }
            if(isset($request->sort_by) && !empty($request->sort_by)){
                $query->orderBy('rate', $request->sort_by);
            }else{
                $query->orderBy('rate', 'desc');
            }
        }

        $trads = $query->with('saleCurrency','rateCurrency','user')->whereHas('user', function($q){
            $q->where('status', 1);
        })->paginate(12)->through(function($item) {
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
            $user = [
                'id'             => $item->user->id,
                'image'          => $item->user->image,
                'name'           => $item->user->firstname.' '.$item->user->lastname,
                'email_verified' => $item->user->email_verified,
                'kyc_verified'   => $item->user->kyc_verified,
            ];
            return[
                'image_path'    => get_files_public_path('user-profile'),
                'default_image' => get_files_public_path('default'),
                "base_ur"       => url('/'),
                'id'            => $item->id,
                'exchange_rate' => "1 ". $item->saleCurrency->symbol ." = ".  getDynamicAmount(($item->rate / $item->amount)).' '. $item->rateCurrency->symbol,
                'amount'        => getDynamicAmount($item->amount),
                'rate'          => getDynamicAmount($item->rate),
                'sale_currency' => $sale_currency,
                'rate_currency' => $rate_currency,
                'user'          => $user,
            ];
        });

        $data = [
            'trads'      => $trads,
        ];

        return ApiResponse::success(['success' => ['Marketplace data fetch successfully!']], $data);
    }

    public function transactions(){
        $latest_transaction = Transaction::with('forexcrow')->orderBy('id', 'desc')
        ->where('type', PaymentGatewayConst::MARKETPLACE)
        ->orWhere('user_id', Auth::id())
        ->whereNot('type', PaymentGatewayConst::TYPEADDMONEY)
        ->whereNot('type', PaymentGatewayConst::EXCROW)
        ->whereHas('forexcrow', function($q){
            $q->where('user_id', Auth::id());
        })->get()->map(function ($item){
            $statusInfo = [
                "Ongoing"         => 1,
                "Pending"         => 2,
                "Rejected"        => 4,
                "Payment Pending" => 5,
                "Complete"        => 6,
                "Close Request"   => 7,
                "Closed"          => 8,
            ];

            return [
                'id'               => $item->id,
                'trx'              => $item->payment_fields,
                'transactin_type'  => $item->type,
                'request_amount'   => getDynamicAmount($item->request_amount),
                'payable'          => getDynamicAmount($item->payable),
                'total_charge'     => getDynamicAmount($item->charge->total_charge),
                'buyer_will_pay'   => getDynamicAmount($item->forexcrow->rate),
                'buyer_will_get'   => getDynamicAmount($item->forexcrow->amount),
                'rate_currency'    => $item->forexcrow->rateCurrency->code,
                'sale_currency'    => $item->forexcrow->saleCurrency->code,
                'status'           => $item->stringMarketplaceStatus->value,
                'status_info'      => (object)$statusInfo,
                'rejection_reason' => $item->reject_reason ?? "",
                'created_at'       => $item->created_at,
            ];
        });


        $data = [
            'latest_transaction'   => $latest_transaction,
        ];


        return ApiResponse::success(['success' => ['Data fetch successfully']], $data);

    }

    /**
     * Marketplace buy
     *
     * @method POST
     * @return Illuminate\Http\Response
     */

    public function buy(Request $request){

        $validator = Validator::make($request->all(),[
            'target'   => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $trade = Forexcrow::with('saleCurrency','rateCurrency')->where('id', $validated['target'])->get()->map(function ($item){
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
                'user_id'       => $item->user_id,
            ];
        })->first();

        $trade = (object) $trade;

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        if($trade->user_id == Auth::guard(get_auth_guard())->user()->id){
            return ApiResponse::onlyError(['error' => ['Can not buy your Trade!']]);
        }

        $wallet = UserWallet::where('user_id', Auth::guard(get_auth_guard())->user()->id)->get()->map(function ($item){
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

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $payment_gateway = PaymentGateway::with('currency')->find($validated['gateway_id']);

        if(!$payment_gateway){
            return ApiResponse::onlyError(['error' => ['Payment gateway not found!']]);
        }

        $trade = Forexcrow::find($validated['target']);

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        $wallet = UserWallet::where('user_id', Auth::guard(get_auth_guard())->user()->id)->first();

        if(!$wallet){
            return ApiResponse::onlyError(['error' => ['Your Wallet not found!']]);
        }

        $validated['trade_id']           = $trade->id;
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
            TemporaryData::where('user_id', Auth::guard(get_auth_guard())->user()->id)->where('type', PaymentGatewayConst::MARKETPLACE)->delete();
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
            $trx_id = $this->insertRecordManual($data, $data->target,'MT',1, $get_values);
            $this->insertChargesManual($data,$trx_id,NotificationConst::MARKETPLACE_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($data->total_amount, $data->wallet);
            $this->removeTempDataManual($validated['trx_id']);

            Forexcrow::find($data->trade_id)->update([
                'status' => 6,
            ]);

            $notification_content = [
                'title'   => __('Transaction Created'),
                'message' => __('Purchase Amount :purchase_amount :sale_currency Rate Amount :rate_amount :rate_currency', [
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
            return ApiResponse::onlyError(['error' => ['Something went wrong, Please try again!']]);
        }

        return ApiResponse::onlySuccess(['success' => ['Transaction create successfully!']]);
    }


}
