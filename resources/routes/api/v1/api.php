<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FeeCalculator;
use App\Http\Controllers\Api\MoneyOutController;
use App\Http\Controllers\Api\V1\ExcrowController;
use App\Http\Controllers\Api\V1\GlobalController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\AddMoneyController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AppSettingsController;
use App\Http\Controllers\Api\V1\MarketplaceController;
use App\Http\Controllers\Api\V1\ForexcrowOfferController;
use App\Http\Controllers\Api\V1\Auth\AuthorizationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;

Route::name('api.v1.')->group(function () {
    Route::get('basic/settings', [AppSettingsController::class, "basicSettings"]);
    Route::get('languages', [AppSettingsController::class, "languages"]);

    Route::controller(AddMoneyController::class)->prefix('add-money')->name('add-money.')->group(function () {
        Route::get('success/response/{gateway}', 'success')->name('payment.success');
        Route::get("cancel/response/{gateway}", 'cancel')->name('payment.cancel');
        Route::get('/flutterwave/callback', 'flutterwaveCallback')->name('flutterwave.callback');
        Route::get('stripe/payment/success/{trx}', 'stripePaymentSuccess')->name('stripe.payment.success');

        //sslcommerz
        Route::post('sslcommerz/success', 'sllCommerzSuccess')->name('ssl.success');
        Route::post('sslcommerz/fail', 'sllCommerzFails')->name('ssl.fail');
        Route::post('sslcommerz/cancel', 'sllCommerzCancel')->name('ssl.cancel');

        // Qrpay gateway
        Route::get('qrpay/callback', 'qrpayCallback')->name('qrpay.callback');
        Route::get('qrpay/cancel/{trx_id}', 'qrpayCancel')->name('qrpay.cancel');

        // Razor
        Route::get('razor/callback', 'razorCallback')->name('api.razor.callback');
    });

    // User
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::middleware('web')->group(function () {
            Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
            Route::get('login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
        });

        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::get('dashboard', [DashboardController::class, 'dashboard']);

        Route::group(['prefix' => 'forgot/password'], function () {
            Route::post('send/otp', [ForgotPasswordController::class, 'sendCode']);
            Route::post('verify',  [ForgotPasswordController::class, 'verifyCode']);
            Route::post('reset', [ForgotPasswordController::class, 'resetPassword']);
        });

        Route::middleware('auth:api')->group(function () {
            Route::get('logout', [AuthorizationController::class, 'logout']);
            Route::post('email/otp/verify', [AuthorizationController::class, 'verifyEmailCode']);
            Route::post('email/resend/code', [AuthorizationController::class, 'emailResendCode']);
            Route::post('google-2fa/otp/verify', [AuthorizationController::class, 'verify2FACode']);

            Route::middleware('checkStatusApiUser')->group(function () {

                // Currency list
                Route::get('currency/list', [GlobalController::class, 'currencyList']);
                Route::get('notification/list', [GlobalController::class, 'notificationList']);
                // Add Money
                Route::controller(AddMoneyController::class)->prefix('add-money')->name('add-money.')->group(function () {
                    Route::get('information', 'addMoneyInformation');
                    Route::post('submit-data', 'submitData');
                    // Automatic
                    Route::post('stripe/payment/confirm', 'paymentConfirmedApi')->name('stripe.payment.confirmed');
                    // Manual
                    Route::post('manual/payment/confirmed', 'manualPaymentConfirmedApi')->name('manual.payment.confirmed');
                });

                //Withdraw Money
                Route::controller(MoneyOutController::class)->prefix('withdraw')->group(function () {
                    Route::get('info', 'moneyOutInfo');
                    Route::post('insert', 'moneyOutInsert');
                    Route::post('manual/confirmed', 'moneyOutConfirmed')->name('withdraw.manual.confirmed');
                });

                // Trader Profile
                Route::controller(ProfileController::class)->prefix('profile')->group(function () {
                    Route::get('/', 'profile');
                    Route::post('update', 'profileUpdate');
                    Route::post('password/update', 'passwordUpdate');
                    Route::post('delete/account', 'deleteAccount');
                    Route::get('/google-2fa', 'google2FA');
                    Route::post('/google-2fa/status/update', 'google2FAStatusUpdate');

                    Route::controller(AuthorizationController::class)->prefix('kyc')->group(function () {
                        Route::get('input-fields', 'getKycInputFields');
                        Route::post('submit', 'KycSubmit');
                    });
                });

                // My Forexcrow
                Route::controller(ExcrowController::class)->prefix('my-excrow')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/submit', 'submit');
                    Route::post('/confirm', 'confirm');
                    Route::post('/close', 'closeRequest');
                    Route::post('/edit', 'edit');
                    Route::post('/update', 'updateTrade');
                });

                // Marketplace
                Route::controller(MarketplaceController::class)->prefix('marketplace')->group(function () {
                    Route::get('/', 'index');
                    Route::get('/transactions', 'transactions');
                    Route::post('/buy', 'buy');
                    Route::post('/confirm', 'confirm');
                    Route::post('/evidence/submit', 'evidenceSubmit');
                });

                // Offer
                Route::controller(ForexcrowOfferController::class)->prefix('offer')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/submit', 'offerSubmit');
                    Route::post('/counter/submit', 'counterSubmit');
                    Route::post('/status', 'offerStatus');
                    Route::post('/buy', 'buy');
                    Route::post('/confirm', 'confirm');
                    Route::post('/evidence/submit', 'evidenceSubmit');
                });

                // Fee calculator
                Route::controller(FeeCalculator::class)->prefix("calculator")->group(function () {
                    Route::get('/', 'index')->name('index');
                });
            });
        });
    });
});
