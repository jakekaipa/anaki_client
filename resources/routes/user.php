<?php

use App\Http\Controllers\ForexcrowOfferController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\User\AddMoneyController;
use App\Http\Controllers\User\AuthorizationController;
use App\Http\Controllers\User\BuyListController;
use App\Http\Controllers\User\CSController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ExcrowController;
use App\Http\Controllers\User\FeesCalculator;
use App\Http\Controllers\User\MakeOfferController;
use App\Http\Controllers\User\MarketplaceController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\MoneyOutController;
use App\Http\Controllers\User\MyTradeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\SellListController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\NoticeController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::controller(SellListController::class)->prefix('preview/sell-list')->name('preview.sell-list.')->group(function () {
    Route::get('/', 'previewList')->name('index');
});

Route::controller(BuyListController::class)->prefix('preview/buy-list')->name('preview.buy-list.')->group(function () {
    Route::get('/', 'previewList')->name('index');
});

Route::prefix("user")->name("user.")->group(function () {

    Route::controller(SiteController::class)->group(function () {
        Route::post('language/switch', 'languageSwitch')->name('language.switch');
    });
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard/{order_id?}', 'index')->name('dashboard');
        Route::get('donation-history', 'donationHistory')->name('donation.history');
        Route::post('logout', 'logout')->name('logout');
        Route::delete('delete/account', 'deleteAccount')->name('delete.account')->middleware('app.mode');
    });

    // Transaction
    Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/{slug}', 'index')->name('index')->whereIn('slug', ['add-money', 'withdraw']);
        Route::post('search', 'search')->name('search');
    });

    Route::controller(AddMoneyController::class)->prefix("add-money")->name("add.money.")->group(function () {
        Route::get('/', 'index')->name("index");
        Route::post('submit', 'submit')->name('submit');
        Route::get('success/response/{gateway}', 'success')->name('payment.success');
        Route::get("cancel/response/{gateway}", 'cancel')->name('payment.cancel');
        Route::post("callback/response/{gateway}", 'callback')->name('payment.callback')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);
        Route::get('payment/{gateway}', 'payment')->name('payment');

        Route::get('stripe/payment/success/{trx}', 'stripePaymentSuccess')->name('stripe.payment.success');

        // FlutterWave Gateway
        Route::post('stripe/payment/confirm', 'paymentConfirmed')->name('stripe.payment.confirmed');
        Route::get('/flutterwave/callback', 'flutterwaveCallback')->name('flutterwave.callback');
        //manual gateway
        Route::get('manual/payment', 'manualPayment')->name('manual.payment');
        Route::post('manual/payment/confirmed', 'manualPaymentConfirmed')->name('manual.payment.confirmed');

        // Razor
        Route::get('razor/callback', 'razorCallback')->name('razor.callback');

        // Qrpay gateway
        Route::get('qrpay/callback', 'qrpayCallback')->name('qrpay.callback');
        Route::get('qrpay/cancel/{trx_id}', 'qrpayCancel')->name('qrpay.cancel');
    });

    //Withdraw Money
    Route::controller(MoneyOutController::class)->prefix('withdraw')->name('withdraw.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('insert', 'paymentInsert')->name('insert')->middleware('kyc.verification.guard');
        Route::get('preview', 'preview')->name('preview')->middleware('kyc.verification.guard');
        Route::post('confirm', 'confirmMoneyOut')->name('confirm')->middleware('kyc.verification.guard');
    });

    //Buy-List
    Route::controller(BuyListController::class)->prefix('buy-list')->name('buy-list.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/preview/{id}', 'preview')->name('preview');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::post('/sell', 'sell')->name('sell');
        Route::post('/canceloffer', 'canceloffer')->name('canceloffer');
    });

    //Sell-List
    Route::controller(SellListController::class)->prefix('sell-list')->name('sell-list.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/preview/{id}', 'preview')->name('preview');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::post('/buy', 'buy')->name('buy');
        Route::post('/canceloffer', 'canceloffer')->name('canceloffer');
    });

    Route::controller(MessageController::class)->prefix('messages')->name('messages.')->group(function () {
        Route::get('/getReceiverName', 'getReceiverName')->name('getReceiverName');
        Route::post('/sendMessage', 'sendMessage')->name('sendMessage');
        Route::get('/getMessages', 'getMessages')->name('getMessages');
        Route::post('/upload-file',  'uploadFile')->name('uploadFile');
        Route::get('/download-file/{id}', 'downloadFile')->name('downloadFile');
        Route::get('/notifications', 'getNotifications')->name('getNotifications');
        Route::post('/messageRead', 'messageReadUserUpdate')->name('messageRead');
    });

    //Make-Offer
    Route::controller(MakeOfferController::class)->prefix('make-offer')->name('make-offer.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/submit', 'submit')->name('submit')->middleware('app.mode');
        Route::get('/moveUpdateForm/{id}', 'moveUpdateForm')->name('moveForm');
        Route::post('/update','updateMakeOffer')->name('updateMakeOffer'); 
    });

    //My Trade
    Route::controller(MyTradeController::class)->prefix('mytrade')->name('mytrade.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/view/{id}', 'view')->name('view');
        Route::get('/details/{id}', 'details')->name('details');

        Route::put('/uploadproof', 'uploadproof')->name('uploadproof')->middleware('app.mode');
        Route::put('/updatebankinfo', 'updatebankinfo')->name('updatebankinfo')->middleware('app.mode');
        Route::put('/uploadQRImage', 'uploadQRImage')->name('uploadQRImage')->middleware('app.mode');
        Route::get('/send/{id}', 'send')->name('send');
        Route::put('/cancel', 'cancel')->name('cancel')->middleware('app.mode');
        Route::put('/dispute', 'dispute')->name('dispute')->middleware('app.mode');
        Route::get('/downloadpdf/{id}', 'downloadpdf')->name('downloadpdf');
        Route::get('/checkPayProof/{id}', 'checkPayProof')->name('checkPayProof');
        Route::post('/transactionFinished', 'transactionFinished')->name('transactionFinished');
        Route::get('/check-state/{id}', 'stateCheck')->name('stateCheck');
    });

    //Wallet
    Route::controller(WalletController::class)->prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/qrCode-reader', 'moveQrCodeReaderForm')->name('qrCodeReaderForm');
        Route::get('checkRecentDeposit', 'checkRecentDeposit')->name('checkRecentDeposit');
        Route::put('sendUSDT', 'sendUSDT')->name('sendUSDT')->middleware('app.mode'); // 테더 외부 전송
        Route::put('transferUSDTInternally', 'transferUSDTInternally')->name('transferUSDTInternally')->middleware('app.mode'); // 내부 전송 수수료 O
        Route::put('transferUSDTInternallyWalletToWallet', 'transferUSDTInternallyWalletToWallet')->name('transferUSDTInternallyWalletToWallet')->middleware('app.mode'); // 지갑 내부전송 수수료 X
        Route::post('/check-user-tetheAmount', 'checkUserDbTetheAmount')->name('checkUserDbTetheAmount');    
    });

    // My Forexcrow
    Route::controller(ExcrowController::class)->prefix('my-excrow')->name('my-excrow.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/transaction', 'transaction')->name('transaction');
        Route::post('/submit', 'submit')->name('submit')->middleware('kyc.verification.guard');
        Route::get('/complete/{id}', 'transactionComplete')->name('complete');
        Route::post('/cancel', 'cancelExcrow')->name('cancel');
        Route::get('/edit/{id}', 'editExcrow')->name('edit');
        Route::post('/update', 'updateExcrow')->name('update')->middleware('kyc.verification.guard');
    });

    // Marketplace
    Route::controller(MarketplaceController::class)->prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/view/{id}', 'viewTrade')->name('view');
        Route::get('/preview/{id}', 'preview')->name('preview');
        Route::post('/buy', 'buyExcrow')->name('buy')->middleware('kyc.verification.guard');
        Route::get('/evidence/{excrow_id}/{gateway_id}', 'evidenceView')->name('evidance');
        Route::post('/evidence/submit', 'evidenceSubmit')->name('evidance.submit');
        Route::get('/complete', 'transactionComplete')->name('complete');
        Route::get('/transactions', 'Transactions')->name('transactions');
    });

    // Marketplace
    Route::controller(ForexcrowOfferController::class)->prefix('offer')->name('offer.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/submit', 'offerSubmit')->name('submit')->middleware('kyc.verification.guard');
        Route::post('/status', 'offerStatus')->name('status')->middleware('kyc.verification.guard');
        Route::get('/preview/{id}', 'preview')->name('preview');
        Route::post('/buy', 'buyExcrow')->name('buy')->middleware('kyc.verification.guard');
        Route::get('/evidence/{excrow_id}/{gateway_id}', 'evidenceView')->name('evidence');
        Route::post('/evidence/submit', 'evidenceSubmit')->name('evidence.submit')->middleware('kyc.verification.guard');
        Route::get('/complete', 'transactionComplete')->name('complete');
    });

    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('update', 'update')->name('update')->middleware('app.mode');
        Route::put('usernameUpdate', 'usernameUpdate')->name('usename.update')->middleware('app.mode');
        Route::put('password/update', 'passwordUpdate')->name('password.update')->middleware('app.mode');
        Route::put('accountUpdate', 'accountUpdate')->name('account.update')->middleware('app.mode');
        Route::delete('accountDelete', 'accountDelete')->name('account.delete')->middleware('app.mode');
        Route::put('qrUpdate', 'qrUpdate')->name('qr.update')->middleware('app.mode');
        Route::delete('qrDelete', 'qrDelete')->name('qr.delete')->middleware('app.mode');
        Route::post('kycInfoUpdate', 'kycInfoUpdate')->name('kycinfo.update')->middleware('app.mode');
        Route::get('getKYCToken', 'getKYCToken')->name('kycinfo.token')->middleware('app.mode');
        Route::put('sendUSDT', 'sendUSDT')->name('sendUSDT')->middleware('app.mode');
    });

    Route::controller(FeesCalculator::class)->prefix("calculator")->name('calculator.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::controller(AuthorizationController::class)->prefix("authorize")->name('authorize.')->group(function () {
        Route::get('kyc', 'showKycFrom')->name('kyc');
        Route::post('kyc/submit', 'kycSubmit')->name('kyc.submit');
    });

    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function () {
        Route::get('google/2fa', 'google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update', 'google2FAStatusUpdate')->name('google.2fa.status.update')->middleware('app.mode');
        Route::post('google/2fa/tether-transfer', 'tetherTransfer2FAUpdate')->name('google.2fa.tether-transfer')->middleware('app.mode');
    });

    Route::controller(SupportTicketController::class)->prefix("support-ticket")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}', 'conversation')->name('conversation');
        Route::post('message/send', 'messageSend')->name('messaage.send');
    });

    Route::controller(CSController::class)->prefix("cs")->name("cs.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('view/{id}/{commentsLastId?}', 'view')->name('view');
        Route::post('/insert-comment', 'insertComments')->name('insertComments');
    });

    Route::controller(NoticeController::class)->prefix("notice")->name("notice.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/form/{id?}', 'moveForm')->name('moveForm');
    });

});
