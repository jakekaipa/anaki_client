<?php

namespace App\Traits\PaymentGateway;

use Exception;
use App\Traits\Transaction;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Http\Helpers\Api\Helpers;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Session;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Providers\Admin\BasicSettingsProvider;
use App\Notifications\User\AddMoney\ManualMail;
use App\Notifications\User\AddMoney\AddMoneyMail;
use App\Models\Admin\PaymentGateway as PaymentGatewayModel;

trait Manual
{
use ControlDynamicInputFields, Transaction;

    public function manualInit($output = null) {
        if(!$output) $output = $this->output;
        $gatewayAlias = $output['gateway']['alias'];
        $identifier = generate_unique_string("transactions","trx_id",16);
        $this->manualJunkInsert($identifier);
        Session::put('identifier',$identifier);
        Session::put('output',$output);
        return redirect()->route('user.add.money.manual.payment');
    }

    public function manualJunkInsert($response) {

        $output = $this->output;

        $data = [
            'gateway'   => $output['gateway']->id,
            'currency'  => $output['currency']->id,
            'amount'    => json_decode(json_encode($output['amount']),true),
            'response'  => $response,
        ];

        return TemporaryData::create([
            'user_id'    => Auth::id(),
            'type'       => PaymentGatewayConst::MANUA_GATEWAY,
            'identifier' => $response,
            'data'       => $data,
        ]);
    }

    public function manualPaymentConfirmed(Request $request){
        $output = session()->get('output');
        $tempData = Session::get('identifier');
        $hasData = TemporaryData::where('identifier', $tempData)->first();
        $gateway = PaymentGatewayModel::manual()->where('slug',PaymentGatewayConst::add_money_slug())->where('id',$hasData->data->gateway)->first();
        $payment_fields = $gateway->input_fields ?? [];

        $validation_rules = $this->generateValidationRules($payment_fields);
        $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
        $get_values = $this->placeValueWithFields($payment_fields,$payment_field_validate);

        try{
            $trx_id = generateTrxString("transactions","trx_id",'AM',8);
            $user = auth()->user();
            $basic_settings = BasicSettingsProvider::get();
            if($basic_settings->email_notification == true){
                $user->notify(new AddMoneyMail($user,$output,$trx_id));
            }
            $inserted_id = $this->insertRecordManual($output,$get_values,$trx_id);
            $this->createTransactionChildRecords($inserted_id, $output);
            $this->removeTempDataManual($output);

            return redirect()->route("user.add.money.index")->with(['success' => ['Add money request sent to admin successfully.']]);
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }

    public function insertRecordManual($output,$get_values,$trx_id) {
        $trx_id = $trx_id;
        $token = $this->output['tempData']['identifier'] ?? "";
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'user_id'                       => auth()->user()->id,
                'user_wallet_id'                => $output['wallet']->id,
                'payment_gateway_currency_id'   => $output['currency']->id,
                'type'                          => PaymentGatewayConst::TYPEADDMONEY,
                'trx_id'                        => $trx_id,
                'request_amount'                => $output['amount']->requested_amount,
                'payable'                       => $output['amount']->total_amount,
                'available_balance'             => $output['wallet']->balance,
                'remark'                        => ucwords(remove_speacial_char(PaymentGatewayConst::TYPEADDMONEY," ")) . " With " . $output['gateway']->name,
                'details'                       => json_encode($get_values),
                'status'                        => 2,
                'attribute'                      =>PaymentGatewayConst::SEND,
                'created_at'                    => now(),
            ]);

            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    public function removeTempDataManual($output) {
        $token = session()->get('identifier');
        TemporaryData::where("identifier",$token)->delete();
    }

     //for api
     public function manualInitApi($output = null) {
        if(!$output) $output = $this->output;
        $gatewayAlias = $output['gateway']['alias'];
        $identifier = generate_unique_string("transactions","trx_id",16);
        $this->manualJunkInsert($identifier);
        $response=[
            'trx' => $identifier,
        ];
        return $response;
    }

    public function manualPaymentConfirmedApi(Request $request){
        $validator = Validator::make($request->all(), [
            'track' => 'required',
        ]);
        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return Helpers::validation($error);
        }
        $track = $request->track;
        $hasData = TemporaryData::where('identifier', $track)->first();
        if(!$hasData){
            $error = ['error'=>["Sorry, your payment information is invalid"]];
            return Helpers::error($error);
        }
        $gateway = PaymentGatewayModel::manual()->where('slug',PaymentGatewayConst::add_money_slug())->where('id',$hasData->data->gateway)->first();
        $payment_fields = $gateway->input_fields ?? [];

        $validation_rules = $this->generateValidationRules($payment_fields);
        $validator2 = Validator::make($request->all(), $validation_rules);

        if ($validator2->fails()) {
            $message =  ['error' => $validator2->errors()->all()];
            return Helpers::error($message);
        }
        $validated = $validator2->validate();
        $get_values = $this->placeValueWithFields($payment_fields, $validated);
        $payment_gateway_currency = PaymentGatewayCurrency::where('id', $hasData->data->currency)->first();
        $gateway_request = ['currency' => $payment_gateway_currency->alias, 'amount'  => $hasData->data->amount->requested_amount];
        $output = PaymentGateway::init($gateway_request)->gateway()->get();

        try{
            $trx_id = generateTrxString("transactions","trx_id",'AM',8);
            $user = auth()->user();
            $basic_settings = BasicSettingsProvider::get();
            if($basic_settings->email_notification == true){
                $user->notify(new AddMoneyMail($user,$output,$trx_id));
            }
            $inserted_id = $this->insertRecordManual($output,$get_values,$trx_id);
            $this->createTransactionChildRecords($inserted_id, $output);

            $hasData->delete();
            $message =  ['success'=>['Add money request sent to admin successfully.']];
            return Helpers::onlySuccess( $message);
        }catch(Exception $e) {
                $error = ['error'=>[$e->getMessage()]];
                return Helpers::error($error);
        }
    }

}
