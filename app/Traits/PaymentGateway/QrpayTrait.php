<?php

namespace App\Traits\PaymentGateway;

use Exception;
use App\Models\Campaign;
use App\Traits\Transaction;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Session;
use App\Providers\Admin\BasicSettingsProvider;
use App\Notifications\User\AddMoney\AddMoneyMail;

trait QrpayTrait
{
    use Transaction;

    public function qrpayInit($output = null)
    {
        if (!$output) $output = $this->output;
        $credentials = $this->getQrpayCredetials($output);

        $access = $this->accessTokenQrpay($credentials);
        $identifier = generate_unique_string("transactions", "trx_id", 16);

        $this->QrpayJunkInsert($identifier);

        $return_url = route('user.add.money.qrpay.callback');
        $cancel_url = route('user.add.money.qrpay.cancel', $identifier);

        $token = $access->data->access_token;
        // Payment Url Request

        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount, 2, '.', '') : 0;

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }

        $response = Http::withToken($token)->post($base_url . '/payment/create', [
            'amount'     => $amount,
            'currency'   => "USD",
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'custom'   => $identifier,
        ]);


        $statusCode = $response->getStatusCode();
        $content    = json_decode($response->getBody()->getContents());

        if ($content->type == 'error') {
            $errors = implode($content->message->error);
            throw new Exception($errors);
        }

        return redirect()->away($content->data->payment_url);
    }
    // ********* For API **********
    public function qrpayInitApi($output = null)
    {
        if (!$output) $output = $this->output;

        $credentials = $this->getQrpayCredetials($output);
        $access = $this->accessTokenQrpay($credentials);
        $identifier = generate_unique_string("transactions", "trx_id", 16);

        $this->QrpayJunkInsert($identifier);


        $return_url = route('api.v1.add-money.qrpay.callback', "r-source=".PaymentGatewayConst::APP);
        $cancel_url = route('api.v1.add-money.qrpay.cancel', $identifier);

        $token = $access->data->access_token;
        // Payment Url Request

        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount, 2, '.', '') : 0;

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }


        $response = Http::withToken($token)->post($base_url . '/payment/create', [
            'amount'     => $amount,
            'currency'   => "USD",
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'custom'   => $identifier,
        ]);

        $statusCode = $response->getStatusCode();
        $content    = json_decode($response->getBody()->getContents());

        if ($content->type == 'error') {
            $errors = implode($content->message->error);
            throw new Exception($errors);
        }
        $data['link'] = $content->data->payment_url;
        $data['trx'] = $identifier;

        return $data;

    }
    public function getQrpayCredetials($output)
    {
        $gateway = $output['gateway'] ?? null;

        if (!$gateway) throw new Exception("Payment gateway not available");
        $client_id_sample = ['api key', 'api_key', 'client id', 'primary key'];
        $client_secret_sample = ['client_secret', 'client secret', 'secret', 'secret key', 'secret id'];
        $base_url_sandbox = ['base_url', 'base url', 'base-url', 'url', 'base-url-sandbox', 'sandbox', 'sendbox-base-url'];
        $base_url_production = ['base_url', 'base url', 'base-url', 'url', 'base-url-production', 'production'. 'live-base-url', 'live base url'];

        $client_id = '';
        $outer_break = false;
        foreach ($client_id_sample as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $client_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }


        $secret_id = '';
        $outer_break = false;
        foreach ($client_secret_sample as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $secret_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }

        $sandbox_url = '';
        $outer_break = false;
        foreach ($base_url_sandbox as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $sandbox_url = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }

        $production_url = '';
        $outer_break = false;
        foreach ($base_url_production as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $production_url = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }


        return (object) [
            'client_id'     => $client_id,
            'client_secret' => $secret_id,
            'base_url_sandbox' => $sandbox_url,
            'base_url_production' => $production_url,
            'mode'          => $gateway->env,

        ];
    }

    public function qrpayPlainText($string)
    {
        $string = Str::lower($string);
        return preg_replace("/[^A-Za-z0-9]/", "", $string);
    }

    public function accessTokenQrpay($credentials)
    {

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }

        $response = Http::post($base_url . '/authentication/token', [
            'client_id' => $credentials->client_id,
            'secret_id' => $credentials->client_secret,
        ]);


        $statusCode = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        if ($statusCode != 200) {
            throw new Exception("Access token capture failed");
        }

        return json_decode($content);
    }

    public function qrpayJunkInsert($response)
    {
        $output = $this->output;

        $user = auth()->guard(get_auth_guard())->user();
        $creator_table = $creator_id = $wallet_table = $wallet_id = null;

        if ($user != null) {
            $creator_table = auth()->guard(get_auth_guard())->user()->getTable();
            $creator_id = auth()->guard(get_auth_guard())->user()->id;
            $wallet_table = $output['wallet']->getTable();
            $wallet_id = $output['wallet']->id;
        }


        $data = [
            'gateway'      => $output['gateway']->id,
            'currency'     => $output['currency']->id,
            'amount'       => json_decode(json_encode($output['amount']),true),
            'response'     => $response,
            'wallet_table'  => $wallet_table,
            'wallet_id'     => $wallet_id,
            'creator_table' => $creator_table,
            'creator_id'    => $creator_id,
            'creator_guard' => get_auth_guard(),
        ];

        Session::put('identifier', $response);
        Session::put('output', $output);

        return TemporaryData::create([
            'user_id'       => Auth::id(),
            'type'          => PaymentGatewayConst::QRPAY,
            'identifier'    => $response,
            'data'          => $data,
        ]);
    }

    public function qrpaySuccess($output = null)
    {
        if (!$output) $output = $this->output;
        $token = $this->output['tempData']['identifier'] ?? "";
        if (empty($token)) throw new Exception('Transaction failed. Record didn\'t saved properly. Please try again.');
        return $this->createTransactionQrpay($output);
    }

    public function createTransactionQrpay($output)
    {
        $basic_setting = BasicSettingsProvider::get();
        $user = auth()->user();
        $trx_id = generateTrxString("transactions","trx_id",'AM',8);
        $inserted_id = $this->insertRecordQrpay($output, $trx_id);
        $this->createTransactionChargeRecord($inserted_id, $output);
        $this->removeTempDataQrpay($output);

        if ($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if ($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }

        if($basic_setting->email_notification == true){
            $user->notify(new AddMoneyMail($user,$output,$trx_id));
        }
    }

    public function updateWalletBalanceQrpay($output)
    {
        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;
        $output['wallet']->update([
            'balance'   => $update_amount,
        ]);
    }

    public function insertRecordQrpay($output, $trx_id)
    {
        DB::beginTransaction();
        try {
            $id = DB::table("transactions")->insertGetId([
                'user_id'                       => Auth::id(),
                'user_wallet_id'                => $output['wallet']->id,
                'payment_gateway_currency_id'   => $output['currency']->id,
                'type'                          => $output['type'],
                'trx_id'                        => $trx_id,
                'request_amount'                => $output['amount']->requested_amount,
                'payable'                       => $output['amount']->total_amount,
                'available_balance'             => $output['wallet']->balance + $output['amount']->requested_amount,
                'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                'details'                       => 'QrPay Payment Successful',
                'status'                        => true,
                'created_at'                    => now(),
            ]);
            $this->updateWalletBalanceQrpay($output);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    public function removeTempDataQrpay($output)
    {
        TemporaryData::where("identifier", $output['tempData']['identifier'])->delete();
    }
}
