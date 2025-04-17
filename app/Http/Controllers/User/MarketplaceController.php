<?php

namespace App\Http\Controllers\User;

use App\Models\Forexcrow;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Currency;
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

        $currencies = Currency::orderByDesc('default')->get();

        $page_title = "Marketplace";

        $query = Forexcrow::with('saleCurrency','rateCurrency','user')
                                ->whereHas('user', function($q){
                                    $q->where('status', 1);
                                })
                                ->where('status', 1);

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

        $excrows = $query->paginate(12);


        return view('user.marketplace.index',compact("page_title", "excrows","currencies"));
    }

    public function viewTrade($id){
        $page_title = "Marketplace View";
        $trade = Forexcrow::with('saleCurrency','rateCurrency')
        ->findOrFail($id);
        $currencies = Currency::orderByDesc('default')->get();
        return view('user.marketplace.view',compact("page_title", "trade","currencies"));
    }

    /**
     * My forexcrow page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function preview($id){

        $page_title = "Preview Forexcrow";

        $excrow = Forexcrow::with('saleCurrency','rateCurrency')->findOrFail($id);

        if(!$excrow){
            return back()->with(['error' => ['Transaction failed. Please try again.']]);
        }

        if($excrow->user_id == Auth::id()){
            return back()->with(['error' => ['You cannot buy your own trade.']]);
        }

        $balance = UserWallet::with('currency')->where('user_id', Auth::id())->sum('balance');

        // Charge calcualtion
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        $charges = feesAndChargeCalculation($intervals, $excrow->rate);
        $total_charge = $charges['total_charge'];

        $currency_code = $excrow->saleCurrency->code;

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

        return view('user.marketplace.preview',compact("page_title", "excrow", 'total_charge', 'balance', 'payment_gatewaies'));
    }


    /**
     * My excrow submit
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

        return redirect()->route('user.marketplace.evidance', [$validated['excrow_id'], $validated['payment_gateway']]);

    }


    /**
     * My marketplace evidence page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function evidenceView($excrow_id, $gateway_id){
        $page_title = "Evidence";

        $excrow = Forexcrow::findOrFail($excrow_id);
        $payment_gateway = PaymentGateway::findOrFail($gateway_id);

        return view('user.marketplace.avidence',compact("page_title", "excrow", "payment_gateway"));
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

        $payment_gateway = PaymentGateway::with('currency')->find($validated['gateway_id']);

        if(!$payment_gateway){
            return back()->with(['error' => ['Payment gateway not found.']]);
        }

        $excrow = Forexcrow::find($validated['excrow_id']);

        if(!$excrow){
            return back()->with(['error' => ['Trade not found.']]);
        }

        $wallet = UserWallet::where('user_id', Auth::id())->where('currency_id', $excrow->rate_currency_id)->first();

        if(!$wallet){
            return back()->with(['error' => ['Your wallet not found.']]);
        }

        $validated['rate_currency'] = $excrow->rateCurrency->code;
        $validated['payment_gateway_id'] = $payment_gateway->id;
        $validated['payment_gateway_currency_id'] = $payment_gateway->currency->id;
        $validated['will_get'] = $excrow->amount;
        $validated['sale_currency'] = $excrow->saleCurrency->code;
        $validated['amount'] = $excrow->rate;
        $validated['subtotal'] = $excrow->rate;
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;
        $charges = feesAndChargeCalculation($intervals, $validated['subtotal']);
        $validated['wallet'] = $wallet;
        $validated['fixed_charge']   = $charges['fixed_charge'];
        $validated['percent_charge'] = $charges['percent_charge'];
        $validated['total_charge']   = $charges['total_charge'];
        $validated['total_amount'] = $validated['total_charge'] + $validated['subtotal'];
        $validated['transaction_type'] = PaymentGatewayConst::MARKETPLACE;

        if($wallet->balance < $validated['total_amount']){
            return back()->with(['error' => ['Insufficient wallet balance.']]);
        }

        $payment_fields = $payment_gateway->input_fields ?? [];

        $validation_rules       = $this->generateValidationRules($payment_fields);
        $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
        $get_values             = $this->placeValueWithFields($payment_fields,$payment_field_validate);

        try {
            $trx_id = $this->insertRecordManual((object) $validated, $validated['excrow_id'],'MT',1, $get_values);
            $this->insertChargesManual((object) $validated,$trx_id,NotificationConst::MARKETPLACE_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($validated['total_amount'], $wallet);

            Forexcrow::find($validated['excrow_id'])->update([
                'status' => 6,
            ]);

            $notification_content = [
                'title'   => __('Transaction Created'),
                'message' => __('Purchase Amount :purchase_amount :purchase_currency Rate Amount :rate_amount :rate_currency', [
                    'purchase_amount'   => getDynamicAmount($excrow->amount),
                    'purchase_currency' => $excrow->saleCurrency->code,
                    'rate_amount'       => getDynamicAmount($excrow->rate),
                    'rate_currency'     => $excrow->rateCurrency->code
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
                $user->notify(new MarketplaceMail($user,$validated,$trx_id));
            }
        } catch (\Exception $e) {
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }


        return redirect()->route('user.marketplace.transactions')->with(['success' => ['Transaction created successfully.']]);

    }

    /**
     * Transactions page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function Transactions(){
        $page_title = "Transactions";
        $transactions = Transaction::with('forexcrow')->orderBy('id', 'desc')
                        ->where('type', PaymentGatewayConst::MARKETPLACE)
                        ->orWhere('user_id', Auth::id())
                        ->whereNot('type', PaymentGatewayConst::TYPEADDMONEY)
                        ->whereNot('type', PaymentGatewayConst::EXCROW)
                        ->whereHas('forexcrow', function($q){
                            $q->where('user_id', Auth::id());
                        })
                        ->paginate(9);

        return view('user.marketplace.transactions',compact("page_title", "transactions"));
    }


}
