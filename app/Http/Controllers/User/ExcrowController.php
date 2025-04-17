<?php

namespace App\Http\Controllers\User;

use Web3\Web3;
use Web3\Providers\WsProvider;
use Web3\Contract;
use Illuminate\Support\Facades\Log;

use Exception;
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
use App\Notifications\User\ExcrowMail;
use App\Models\Admin\TransactionSetting;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use App\Traits\PaymentGateway\ManualCrowPayment;

class ExcrowController extends Controller
{

    use ControlDynamicInputFields, ManualCrowPayment;

    /**
     * My forexcrow page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(){
        $page_title = "My Trade";
        $transactions = Transaction::with('forexcrow')->orderBy('id', 'desc')->where('type', PaymentGatewayConst::EXCROW)->where('user_id', Auth::user()->id)->paginate(12);
        return view('user.my-excrow.index',compact("page_title", "transactions"));
    }

    /**
     * My transaction page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function transaction(){

        $page_title = "Trade";
        $currency = Currency::active()->orderBy('default', 'asc')->get();
        $wallet = walletBalance();
        $default_currency = Currency::default();
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;
        $payment_gateway =  PaymentGateway::with('currencies')->addMoney()->manual()->first();
        return view('user.my-excrow.transactioin',compact("page_title",'currency','default_currency', 'intervals','payment_gateway','wallet'));
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
            return back()->withErrors($validator)->withInput();
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
            return back()->with(['error' => ['Please adhere to the transaction limit.']]);
        }

        $charges = feesAndChargeCalculation($intervals, $validated['subtotal']);

        $validated['rate_currency_code'] = $rate_currency->code;
        $validated['sale_currency_code'] = $currency->code;
        $validated['fixed_charge']   = $charges['fixed_charge'];
        $validated['percent_charge'] = $charges['percent_charge'];
        $validated['total_charge']   = $charges['total_charge'];

        $validated['total_amount'] = $validated['total_charge'] + $validated['subtotal'];

        $wallet = UserWallet::where('user_id', Auth::id())->where('currency_id', $validated['rate_currency'])->first();

        if(!$wallet){
            return back()->with(['error' => ['Your wallet not found.']]);
        }

        if($wallet->balance < $validated['total_amount']){
            return back()->with(['error' => ['Insufficient wallet balance.']]);
        }

        $validated['wallet'] = $wallet;

        try {
            $id = $this->forexCrowInsert((object) $validated);
            $trx_id = $this->insertRecordManual((object) $validated,$id,'MT', 1, 'Balance Payment');
            $this->insertChargesManual((object) $validated,$trx_id,NotificationConst::EXCROW_TRANSACTION_ADDED);
            $this->insertDeviceManual($trx_id);
            $this->userWalletUpdate($validated['total_amount'], $wallet);

            $notification_content = [
                'title'   => __('Trade Created'),
                'message' => __('Selling Amount :selling_amount :selling_currency Asking Amount :asking_amount :asking_currency', [
                    'selling_amount'   => getDynamicAmount($validated['amount']),
                    'selling_currency' => $currency->code,
                    'asking_amount'    => getDynamicAmount($validated['rate']),
                    'asking_currency'  => $rate_currency->code
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
            $user = auth()->user();
            $basic_settings = BasicSettingsProvider::get();
            if($basic_settings->email_notification == true){
                $user->notify(new ExcrowMail($user,$validated,$trx_id));
            }

        } catch (\Exception $e) {
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }

        return redirect()->route('user.my-excrow.complete', $id)->with(['success' => ['Transaction completed successfully.']]);

    }

    public function transactionComplete($id){

        $url = route('user.marketplace.view', $id);
        $qrcode = generateQr($url);

        $page_title = "Treansaction Complete";

        return view('user.my-excrow.completed',compact("page_title", 'qrcode', 'url'));
    }

    public function cancelExcrow(Request $request){
        $transaction = Transaction::with('forexcrow')->find($request->target);

        if(!$transaction){
            return back()->with(['error' => ['Invalid request.']]);
        }

       try {
            $transaction->update([
                'status' => 7,
            ]);
            $transaction->forexcrow->update([
                'status' => 7,
            ]);
       } catch (\Exception $th) {
        return back()->with(['error' => ['An error occurred. Please try again.']]);
       }

       return back()->with(['success' => ['Trade close request sent to admin successfully. Please wait for admin response.']]);
    }

    /**
     * My forexcrow edit
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function editExcrow($id){
        $excrow = Forexcrow::with('saleCurrency','rateCurrency')->findOrFail($id);
        $page_title = "Edit Trade";
        $currency = Currency::active()->orderBy('default', 'asc')->get();
        $wallet = walletBalance();
        $default_currency = Currency::default();
        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;
        return view('user.my-excrow.eidt-transactioin', compact('page_title','excrow', 'currency','default_currency', 'intervals','wallet'));
    }


    /**
     * My forexcrow edit
     *
     * @method POST
     * @return Illuminate\Http\Response
     */
    public function updateExcrow(Request $request){
        $validator = Validator::make($request->all(), [
            'rate' => 'required|gt:0',
            'target' => 'required',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validate();

        $excrow = Forexcrow::findOrFail($validated['target']);


        $excrow->update([
            'rate'       => $request->rate,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('user.my-excrow.index')->with(['success' => ['Trade updated successfully.']]);
    }


}
