<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Forexcrow;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Models\Admin\Currency;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Notifications\User\ExcrowMail;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Traits\PaymentGateway\ManualCrowPayment;

class ExcrowController extends Controller
{
    use ManualCrowPayment;
    /**
     * My excrpw data render
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(){
        $transactions = Transaction::with('forexcrow')->orderBy('id', 'desc')->auth()->excrowTransaction()->get()->map(function ($item){
            $statusInfo = [
                "Ongoing"       => 1,
                "Pending"       => 2,
                "Rejected"      => 4,
                "Complete"      => 6,
                "Close Request" => 7,
                "Closed"        => 8,
            ];

            return [
                'id'               => $item->id,
                'forexcrow_id'     => $item->forexcrow->id,
                'trx'              => $item->trx_id,
                'transactin_type'  => $item->type,
                'request_amount'   => getDynamicAmount($item->request_amount),
                'payable'          => getDynamicAmount($item->payable),
                'total_charge'     => getDynamicAmount($item->charge->total_charge),
                'buyer_will_pay'   => getDynamicAmount($item->forexcrow->rate),
                'buyer_will_get'   => getDynamicAmount($item->forexcrow->amount),
                'rate_currency'    => $item->forexcrow->rateCurrency->code,
                'sale_currency'    => $item->forexcrow->saleCurrency->code,
                'status'           => $item->scrowStringStatus->value,
                'status_info'      => (object)$statusInfo,
                'rejection_reason' => $item->reject_reason ?? "",
                'created_at'        => $item->created_at ?? "",
            ];
        });

        $message = ['success' => ['My Forexcrow data fetch successfully!']];

        $data = [
            'my_trade'    => $transactions,
        ];

        return ApiResponse::success($message, $data);
    }

     /**
     * My forexcrow submit
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function submit(Request $request){

        $validator = Validator::make($request->all(), [
            'currency'           => 'required',
            'rate_currency'      => 'required',
            'amount'             => 'required|gt:0',
            'rate'               => 'required|gt:0',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $validated['transaction_type'] = PaymentGatewayConst::EXCROW;

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

        $currency = Currency::find($validated['currency']);
        $rate_currency = Currency::find($validated['rate_currency']);

        $validated['subtotal'] = $validated['amount'] / $currency->rate;

        if($min_limit > $validated['subtotal'] || $max_limit < $validated['subtotal']){
            return ApiResponse::onlyError(['error' => ['Please follow the transaction limit!']]);
        }

        $charges = feesAndChargeCalculation($intervals, $validated['subtotal']);

        $validated['rate_currency_code'] = $rate_currency->code;
        $validated['sale_currency_code'] = $currency->code;
        $validated['fixed_charge']       = $charges['fixed_charge'];
        $validated['percent_charge']     = $charges['percent_charge'];
        $validated['total_charge']       = $charges['total_charge'];
        $validated['total_amount'] = $validated['total_charge'] + $validated['subtotal'];
        $wallet = UserWallet::where('user_id', Auth::id())->first();

        if(!$wallet){
            return ApiResponse::onlyError(['error' => ['Your wallet not found!']]);
        }

        $validated['wallet'] = $wallet;

        if($wallet->balance < $validated['total_amount']){
            return ApiResponse::onlyError(['error' => ['Insufficient wallet balance!']]);
        }

        try {
            TemporaryData::where('user_id', Auth::guard(get_auth_guard())->user()->id)->where('type', PaymentGatewayConst::EXCROW)->delete();
            $identifier = generate_unique_string("transactions","trx_id",16);

            TemporaryData::create([
                'user_id'       => Auth::id(),
                'type'          => PaymentGatewayConst::EXCROW,
                'identifier'    => $identifier,
                'data'          => $validated,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::onlyError(['success' => ['Something went wrong, Please try again!']]);
        }

        $data = [
            'trx_id' => $identifier,
            'data'   => $validated,
        ];

        return ApiResponse::success(['success' => ['Trade insert successfully!']], $data);
    }
    /**
     * My forexcrow confirm
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function confirm(Request $request){
        $validator = Validator::make($request->all(), [
            'trx_id'           => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validated();

        $identifier = TemporaryData::where('identifier', $validated['trx_id'])->where('type', PaymentGatewayConst::EXCROW)->first();

        if(!$identifier){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        $data = $identifier->data;

        try {
            $id = $this->forexCrowInsert($data);
            $trx_id = $this->insertRecordManual($data,$id,'MT', 1, 'Balance Payment');
            $this->insertChargesManual($data,$trx_id,NotificationConst::EXCROW_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($data->total_amount, $identifier->data->wallet);
            $this->removeTempDataManual($validated['trx_id']);

            $notification_content = [
                'title'   => __('Trade Created'),
                'message' => __('Selling Amount :sell_amount :sell_currency Asking Amount :ask_amount :ask_currency', [
                    'sell_amount'   => getDynamicAmount($data->amount),
                    'sell_currency' => $data->sale_currency_code,
                    'ask_amount'    => getDynamicAmount($data->rate),
                    'ask_currency'  => $data->rate_currency_code
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'      => NotificationConst::TRADE_CREATED,
                'user_id'  =>  Auth::guard(get_auth_guard())->user()->id,
                'message'   => $notification_content,
            ]);

            // Mail send
            $user = Auth::guard(get_auth_guard())->user();
            $basic_settings = BasicSettingsProvider::get();
            if($basic_settings->email_notification == true){
                $user->notify(new ExcrowMail($user,(array)$data,$trx_id));
            }
        } catch (\Exception $e) {
            return ApiResponse::onlyError(['error' => ['Something went wrong, Please try again!']]);
        }

        $url = route('user.marketplace.view', $id);
        $qrcode = generateQr($url);


        $data = [
            'qrcode' => $qrcode,
            'url'    => $url,
            'id'     => $id,
        ];

        return ApiResponse::success(['success' => ['Trade created successfully!']], $data);
    }

    public function closeRequest(Request $request){

        $validator = Validator::make($request->all(), [
            'target'           => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $transaction = Transaction::with('forexcrow')->find($request->target);

        if(!$transaction){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

       try {
            $transaction->update([
                'status' => 7,
            ]);
            $transaction->forexcrow->update([
                'status' => 7,
            ]);
       } catch (\Exception $th) {
            return ApiResponse::onlyError(['error' => ['Something went wrong, Please try again!']]);
       }
       return ApiResponse::onlySuccess(['success' => ['Trade close request send to admin successfully, Please wait for admin response!']]);
    }

    /**
     * My forexcrow edit
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'target'           => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $trade = Forexcrow::with('saleCurrency','rateCurrency')->where('id',$request->target)->get()->map(function($item) {
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
                'id' => $item->id,
                'amount' => getDynamicAmount($item->amount),
                'rate' => getDynamicAmount($item->rate),
                'sale_currency' => $sale_currency,
                'rate_currency' => $rate_currency,
            ];
        })->first();

        if(!$trade){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

        $data = [
            'trade' => $trade,
        ];

        return ApiResponse::success(['success' => ['Data fetch successfully!']], $data);
    }

    /**
     * My forexcrow update
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function updateTrade(Request $request){
        $validator = Validator::make($request->all(), [
            'rate' => 'required|gt:0',
            'target' => 'required',
        ]);

        if($validator->fails()){
            return ApiResponse::onlyValidation(['error' => $validator->errors()->all()]);
        }

        $validated = $validator->validate();

        $excrow = Forexcrow::findOrFail($validated['target']);

        if(!$excrow){
            return ApiResponse::onlyError(['error' => ['Invalid request!']]);
        }

       try {
            $excrow->update([
                'rate'       => $request->rate,
                'updated_at' => Carbon::now(),
            ]);
       } catch (\Throwable $th) {
            return ApiResponse::onlyError(['error' => ['Something went wrong, Please try again!']]);
       }

       return ApiResponse::onlySuccess(['success' => ['Trade updated successfully!']]);
    }
}
