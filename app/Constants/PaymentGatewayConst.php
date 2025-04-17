<?php
namespace App\Constants;
use Illuminate\Support\Str;

class PaymentGatewayConst {

    const AUTOMATIC = "AUTOMATIC";
    const MANUAL    = "MANUAL";
    const ADDMONEY  = "Add Money";
    const DONATION  = "Donation";
    const MONEYOUT  = "Money Out";
    const RECEIVING_METHOD  = "Receiving Method";
    const ACTIVE    =  true;

    const FIAT          = "FIAT";
    const CRYPTO        = "CRYPTO";

    const TYPEADDMONEY      = "ADD-MONEY";
    const EXCROW   = "EXCROW";
    const MARKETPLACE = "MARKETPLACE";
    const TYPEDONATION      = "DONATION";
    const TYPEMONEYOUT      = "MONEY-OUT";
    const TYPEWITHDRAW      = "WITHDRAW";
    const TYPECOMMISSION    = "COMMISSION";
    const TYPEBONUS         = "BONUS";
    const TYPETRANSFERMONEY = "TRANSFER-MONEY";
    const TYPEMONEYEXCHANGE = "MONEY-EXCHANGE";
    const TYPEADDSUBTRACTBALANCE = "ADD-SUBTRACT-BALANCE";

    const STATUSSUCCESS     = 1;
    const STATUSPENDING     = 2;
    const STATUSHOLD        = 3;
    const STATUSREJECTED    = 4;

    const EXCROW_STATUSONGOING        = 1;
    const EXCROW_STATUSPENDING        = 2;
    const EXCROW_STATUSCANCELLED      = 4;
    const EXCROW_STATUSPAYMENTPENDING = 5;
    const EXCROW_STATUSCOMPLETE       = 6;
    const EXCROW_CANCEL_REQUEST       = 7;
    const EXCROW_CANCEL_BY_USER       = 8;

    const PAYPAL = 'paypal';
    const STRIPE = 'stripe';
    const MANUA_GATEWAY = 'manual';
    const FLUTTER_WAVE = 'flutterwave';
    const RAZORPAY = 'razorpay';
    const SSLCOMMERZ = 'sslcommerz';
    const QRPAY = 'qrpay';
    const BALANCE = 'balance';
    const COIN_GATE = 'coingate';

    const ENV_SANDBOX       = "SANDBOX";
    const ENV_PRODUCTION    = "PRODUCTION";


    const SEND = "SEND";
    const RECEIVED = "RECEIVED";

    public static function add_money_slug() {
        return Str::slug(self::ADDMONEY);
    }
    public static function donation_slug() {
        return Str::slug(self::DONATION);
    }

    public static function money_out_slug() {
        return Str::slug(self::MONEYOUT);
    }

    public static function receiving_method_slug() {
        return Str::slug(self::RECEIVING_METHOD);
    }

    public static function registerWallet() {
        return [
            'web'       => UserWallet::class,
            'api'       => UserWallet::class,
        ];
    }

    public static function register($alias = null) {
        $gateway_alias  = [
            self::PAYPAL => "paypalInit",
            self::STRIPE => "stripeInit",
            self::MANUA_GATEWAY => "manualInit",
            self::FLUTTER_WAVE => 'flutterwaveInit',
            self::COIN_GATE => 'coinGateInit',
            self::RAZORPAY => 'razorInit',
            self::SSLCOMMERZ => 'sslcommerzInit',
            self::QRPAY => "qrpayInit",
        ];

        if($alias == null) {
            return $gateway_alias;
        }

        if(array_key_exists($alias,$gateway_alias)) {
            return $gateway_alias[$alias];
        }
        return "init";
    }

    public static function registerRedirection() {
        return [
            'web'       => [
                'return_url'    => 'user.add.money.payment.success',
                'cancel_url'    => 'user.add.money.payment.cancel',
                'callback_url'  => 'user.add.money.payment.callback',
            ],
            'api'       => [
                'return_url'    => 'api.user.add.money.payment.success',
                'cancel_url'    => 'api.user.add.money.payment.cancel',
                'callback_url'  => 'user.add.money.payment.callback',
            ],
        ];
    }

    const APP       = "APP";
    public static function apiAuthenticateGuard() {
            return [
                'api'   => 'web',
            ];
        }
}
