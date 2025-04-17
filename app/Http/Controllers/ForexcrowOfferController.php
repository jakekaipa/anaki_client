<?php

namespace App\Http\Controllers;

use App\Models\Forexcrow;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\ForexcrowOffer;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\TransactionSetting;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Notifications\User\MarketplaceMail;
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
        $page_title = "Get Offer";
        $get_offers = ForexcrowOffer::with('forexcrow','saleCurrency','rateCurrency','creator','receiver')
                                    ->whereHas('creator',function($q){
                                        $q->where('status', 1);
                                    })
                                    ->whereHas('receiver',function($q){
                                        $q->where('status', 1);
                                    })
                                    ->where('creator_id', Auth::id())
                                    ->orWhere('receiver_id', Auth::id())
                                    ->orderBy('id', 'desc')
                                    ->paginate();
        $default_currency = get_default_currency_code();
        return view('user.offer.index',compact("page_title", "get_offers", 'default_currency'));
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
            'rate'         => 'required',
            'forexcrow_id' => 'required',
            'type'         => 'required',
            'receiver_id'  => 'nullable',
            'creator_id'  =>  'nullable',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput()->with('modal','category-add');
        }

        $validated = $validator->safe()->all();

        $forexcrow = Forexcrow::find($validated['forexcrow_id']);

        try {

            if($validated['type'] == 'COUNTER_OFFER'){
                if($validated['receiver_id'] == Auth::id()){
                    $receiver_id = $validated['creator_id'];
                }else{
                    $receiver_id = $validated['receiver_id'];
                }
            }else{
                $receiver_id = $forexcrow->user_id;
                if($forexcrow->user_id == Auth::id()){
                    return back()->with(['error' => ['Unable to send offer request for your trade.']]);
                }
            }

            ForexcrowOffer::create([
                'type'             => $validated['type'],
                'forexcrow_id'     => $forexcrow->id,
                'creator_id'       => Auth::id(),
                'receiver_id'      => $receiver_id,
                'for_user_id'      => $forexcrow->user_id,
                'sale_currency_id' => $forexcrow->currency_id,
                'amount'           => $forexcrow->amount,
                'rate'             => $validated['rate'],
                'rate_currency_id' => $forexcrow->rate_currency_id,
                'status'           => 2,
            ]);


            if($validated['type'] == 'COUNTER_OFFER'){
                $notification_content = [
                    'title'   => __('Counter Offer'),
                    'message' => __('Offer Price :offer_amount :sale_currency @ :offer_rate :rate_currency Selling Price :selling_amount :sale_currency @ :selling_rate :rate_currency', [
                        'offer_amount'    => getDynamicAmount($forexcrow->amount),
                        'sale_currency'   => $forexcrow->saleCurrency->code,
                        'offer_rate'      => getDynamicAmount($validated['rate']),
                        'rate_currency'   => $forexcrow->rateCurrency->code,
                        'selling_amount'  => getDynamicAmount($forexcrow->amount),
                        'selling_rate'    => getDynamicAmount($forexcrow->rate)
                    ]),
                    'time'    => Carbon::now()->diffForHumans(),
                    'image'   => files_asset_path('profile-default'),
                ];
                UserNotification::create([
                    'type'      => NotificationConst::COUNTER_OFFER,
                    'user_id'  =>  $receiver_id,
                    'message'   => $notification_content,
                ]);
            }else{
                $notification_content = [
                    'title'   => __('Offer'),
                    'message' => __('Offer Price :offer_amount :sale_currency @ :offer_rate :rate_currency Selling Price :selling_amount :sale_currency @ :selling_rate :rate_currency', [
                        'offer_amount'    => getDynamicAmount($forexcrow->amount),
                        'sale_currency'   => $forexcrow->saleCurrency->code,
                        'offer_rate'      => getDynamicAmount($validated['rate']),
                        'rate_currency'   => $forexcrow->rateCurrency->code,
                        'selling_amount'  => getDynamicAmount($forexcrow->amount),
                        'selling_rate'    => getDynamicAmount($forexcrow->rate)
                    ]),
                    'time'    => Carbon::now()->diffForHumans(),
                    'image'   => files_asset_path('profile-default'),
                ];
                UserNotification::create([
                    'type'      => NotificationConst::OFFER,
                    'user_id'  =>  $receiver_id,
                    'message'   => $notification_content,
                ]);
            }

        } catch (\Exception $th) {
            $error = ['error' => 'Something went wrong!. Please try again.'];
            return back()->with($error);
        }

        $success = ['success' => ['Your offer sent successfully!']];

        return back()->with($success);
    }


    /**
     * offer status change
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function offerStatus(Request $request){
        $validated = Validator::make($request->all(), [
            'target' => 'required',
            'type' => 'required',
        ])->validated();

        if($validated['type'] == 'Reject'){
            $status = 4;
            $message = ['success' => ['Trade offer reject successfully!']];
        }elseif($validated['type'] == 'Accept')
            $status = 1;
            $message = ['success' => ['Trade offer accept successfully!']];
        try {
            $forexcrow_offer = ForexcrowOffer::find($validated['target']);

            if(!$forexcrow_offer){
                return back()->with(['error' => ['Trade offer not found.']]);
            }

            $forexcrow_offer->update([
                'status' => $status,
            ]);

        } catch (\Exception $th) {
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }

        return back()->with($message);
    }

    /**
     * Buy Preview
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function preview($id){

        $page_title = "Preview Trade";
        $excrow_offer = ForexcrowOffer::with('saleCurrency','rateCurrency')->findOrFail($id);

        if(!$excrow_offer){
            return back()->with(['error' => ['Transaction failed. Please try again.']]);
        }

        $user = Auth::user();
        $balance = UserWallet::with('currency')->where('user_id', Auth::id())->sum('balance');

        // Charge calcualtion
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        $charges = feesAndChargeCalculation($intervals, $excrow_offer->rate);
        $total_charge = $charges['total_charge'];

        $currency_code = $excrow_offer->saleCurrency->code;

        $payment_gatewaies = PaymentGateway::with('currencies')
                                            ->where('type', PaymentGatewayConst::MANUAL)
                                            ->where('slug', PaymentGatewayConst::receiving_method_slug())
                                            ->whereHas('currencies', function($q) use ($currency_code) {
                                                $q->where('currency_code', $currency_code);
                                            })
                                            ->get();

        if(!isset($payment_gatewaies)){
            return back()->with(['error' => ['Payment gateway not found.']]);
        }

        return view('user.offer.preview',compact("page_title", "user", "balance", "excrow_offer", 'total_charge', 'payment_gatewaies'));
    }

     /**
     * My forexcrow submit
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function buyExcrow(Request $request){

        $validator = Validator::make($request->all(),[
            'excrow_id'   => 'required',
            'payment_gateway' => 'required',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        return redirect()->route('user.offer.evidence', [$validated['excrow_id'], $validated['payment_gateway']]);
    }


    /**
     * Forexcrow evidence page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */

    public function evidenceView($excrow_id, $gateway_id){

        $page_title = "Evidence";
        $excrow = ForexcrowOffer::findOrFail($excrow_id);
        $payment_gateway = PaymentGateway::findOrFail($gateway_id);

        return view('user.offer.avidence',compact("page_title", "excrow", "payment_gateway"));
    }


    /**
     * My marketplace evidence submit
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function evidenceSubmit(Request $request){

        $validator = Validator::make($request->all(),[
            'excrow_id'   => 'required',
            'gateway_id' => 'required',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $payment_gateway = PaymentGateway::find($validated['gateway_id']);

        if(!$payment_gateway){
            return back()->with(['error' => ['Payment gateway not found.']]);
        }

        $excrow_offer = ForexcrowOffer::find($validated['excrow_id']);

        if(!$excrow_offer){
            return back()->with(['error' => ['Escrow offer not found.']]);
        }

        $wallet = UserWallet::where('user_id', Auth::id())->where('currency_id', $excrow_offer->rate_currency_id)->first();

        if(!$wallet){
            return back()->with(['error' => ['Your wallet not found.']]);
        }

        $validated['rate_currency']    = $excrow_offer->rateCurrency->code;
        $validated['payment_gateway_currency_id'] = $payment_gateway->currency->id;
        $validated['will_get']         = $excrow_offer->amount;
        $validated['sale_currency']    = $excrow_offer->saleCurrency->code;
        $validated['amount']           = $excrow_offer->rate;
        $validated['subtotal']         = $excrow_offer->rate;
        $charges                       = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals                     = $charges->intervals;
        $charges                       = feesAndChargeCalculation($intervals, $validated['subtotal']);
        $validated['wallet']           = $wallet;
        $validated['fixed_charge']     = $charges['fixed_charge'];
        $validated['percent_charge']   = $charges['percent_charge'];
        $validated['total_charge']     = $charges['total_charge'];
        $validated['total_amount']     = $validated['total_charge'] + $validated['subtotal'];
        $validated['transaction_type'] = PaymentGatewayConst::MARKETPLACE;

        if($wallet->balance < $validated['total_amount']){
            return back()->with(['error' => ['Insufficient wallet balance.']]);
        }

        $payment_fields = $payment_gateway->input_fields ?? [];

        $validation_rules       = $this->generateValidationRules($payment_fields);
        $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
        $get_values             = $this->placeValueWithFields($payment_fields,$payment_field_validate);

        try {
            $trx_id = $this->insertRecordManual((object) $validated, $excrow_offer->forexcrow->id,'MT',1, $get_values);
            $this->insertChargesManual((object) $validated,$trx_id,NotificationConst::MARKETPLACE_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($validated['total_amount'], $wallet);

            Forexcrow::find($excrow_offer->forexcrow->id)->update([
                'status' => 6,
            ]);

            $notification_content = [
                'title'   => __('Transaction Created'),
                'message' => __('Purchase Amount :amount :currency Rate Amount :rate :rate_currency', [
                    'amount'        => getDynamicAmount($excrow_offer->amount),
                    'currency'      => $excrow_offer->saleCurrency->code,
                    'rate'          => getDynamicAmount($excrow_offer->rate),
                    'rate_currency' => $excrow_offer->rateCurrency->code
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
            $user->notify(new MarketplaceMail($user,$validated,$trx_id));
        } catch (\Exception $e) {
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }


        return redirect()->route('user.marketplace.transactions')->with(['success' => ['Transaction created successfully.']]);
    }
}
