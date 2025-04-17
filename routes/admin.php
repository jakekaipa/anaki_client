<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\CsController;
use App\Http\Controllers\Admin\DataStatsController;


// All Admin Route Is Here
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard',[DashboardController::class,"index"])->name('dashboard');
   
    Route::controller(UserController::class)->prefix('manage-user')->name('manage.user.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/view/{id}', 'view')->name('view');
        Route::post('/user-update', 'updateUserInfo')->name('updateUserInfo');
        Route::get('/referral-list/{id}', 'getReferralList')->name('getReferralList');
    });

    Route::controller(TransactionController::class)->prefix('manage-trade')->name('manage.trade.')->group(function () {
        Route::get('/internal-list', 'getInternalTransactionList')->name('internalList');
        Route::get('/external-list', 'getExternalTransactionList')->name('externalList');
        Route::get('/transferWallet-list', 'getTransferWalletList')->name('transferWalletList');
        Route::get('/view/{id}', 'view')->name('view');
        Route::post('/change-state', 'changeTransactionState')->name('changeTransactionState');
        Route::get('/chatting-message-list/{id}', 'getChattingMessageList')->name('getChattingMessageList');
    });

    Route::controller(NoticeController::class)->prefix('manage-notice')->name('manage.notice.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/form/{id?}', 'moveForm')->name('moveForm');
        Route::post('/upload', 'uploadNoticeImage')->name('uploadNoticeImage');
        Route::post('/store', 'store')->name('store');
        Route::post('/update', 'update')->name('update');
    });

    Route::controller(CsController::class)->prefix('manage-cs')->name('manage.cs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/view/{id}/{commentsLastId?}', 'view')->name('view');
        Route::post('/insert-comment', 'insertComments')->name('insertComments');
        Route::post('/change-status', 'changeStatus')->name('changeStatus');
    });

    Route::controller(DataStatsController::class)->prefix('data')->name('data.')->group(function () {
        Route::get('/regist-user', 'getRegistUserCount')->name('registUserCount');
        Route::get('/trade-amount', 'getTradeAmount')->name('tradeAmount');
        Route::get('/trade-count', 'getTradeCount')->name('tradeCount');
    });

    // Push Notification Setup Section
    Route::controller(PushNotificationController::class)->prefix('push-notification')->name('push.notification.')->group(function () {
        Route::get('config', 'configuration')->name('config');
        Route::put('update', 'update')->name('update');
        Route::get('/', 'index')->name('index');
        Route::post('send', 'send')->name('send');
    });

    // Support Ticked Section
    Route::controller(SupportTicketController::class)->prefix('support-ticket')->name('support.ticket.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('active', 'active')->name('active');
        Route::get('pending', 'pending')->name('pending');
        Route::get('solved', 'solved')->name('solved');
        Route::get('conversation/{ticket_id}', 'conversation')->name('conversation');
        Route::post('message/reply', 'messageReply')->name('messaage.reply');
        Route::post('solve', 'solve')->name('solve');
    });
});

Route::get('pusher/beams-auth', function (Request $request) {
    if (Auth::check() == false) {
        return response(['Inconsistent request'], 401);
    }
    $userID = Auth::user()->id;

    $basic_settings = BasicSettingsProvider::get();
    if (!$basic_settings) {
        return response('Basic setting not found!', 404);
    }

    $notification_config = $basic_settings->push_notification_config;

    if (!$notification_config) {
        return response('Notification configuration not found!', 404);
    }

    $instance_id    = $notification_config->instance_id ?? null;
    $primary_key    = $notification_config->primary_key ?? null;
    if ($instance_id == null || $primary_key == null) {
        return response('Sorry! You have to configure first to send push notification.', 404);
    }
    $beamsClient = new PushNotifications(
        array(
            "instanceId" => $notification_config->instance_id,
            "secretKey" => $notification_config->primary_key,
        )
    );
    $publisherUserId = "admin-" . $userID;
    try {
        $beamsToken = $beamsClient->generateToken($publisherUserId);
    } catch (Exception $e) {
        return response(['Server Error. Faild to generate beams token.'], 500);
    }

    return response()->json($beamsToken);
})->name('pusher.beams.auth');
