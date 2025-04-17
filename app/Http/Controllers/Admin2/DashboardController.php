<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\PushNotifications\PushNotifications;
use App\Models\Admin\AdminNotification;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\Response;
use App\Models\Subscriber;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Dashboard";

        $last_month_start =  date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $last_month_end =  date('Y-m-31', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $this_month_start = date('Y-m-01');
        $this_month_end = date('Y-m-d');
        $this_weak = date('Y-m-d', strtotime('-1 week', strtotime(date('Y-m-d'))));
        $this_month = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $this_year = date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d'))));

        // Dashboard box data
        // Add Money
        $add_money_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->where('status', 1)->sum('request_amount');
        $add_money_total_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->sum('request_amount');
        $today_add_money =  Transaction::toBase()
                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                            ->where('status', 1)
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)
                            ->sum('request_amount');
        $last_month_add_money =  Transaction::toBase()->where('status', 1)
                                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        $last_month_add_money_p = $last_month_add_money == 0 ? 1 : $last_month_add_money;
        $add_money_percent = (($today_add_money * 100) / $last_month_add_money_p);

        if($add_money_percent > 100){
            $add_money_percent = 100;
        }

        // Pending Add Money
        $pending_add_money_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->where('status', 2)->sum('request_amount');
        $today_pending_add_money =  Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                    ->where('status', 2)
                                    ->whereDate('created_at','>=',$this_month_start)
                                    ->whereDate('created_at','<=',$this_month_end)
                                    ->sum('request_amount');
        $last_month_pending_add_money =  Transaction::toBase()->where('status', 2)
                                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        if($last_month_pending_add_money == 0){
            $pending_add_money_percent = 100;
        }else{
            $pending_add_money_percent = (($pending_add_money_balance * 100) / $last_month_pending_add_money);
        }


        // Trade
        $trade_balance = Transaction::with('forexcrow')->where('type', PaymentGatewayConst::EXCROW)->where('status', 1)->get()->map(function($item){
            return [
                'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
            ];
        })->sum('amount');

        $today_trade_balance = Transaction::with('forexcrow')
                                    ->where('type', PaymentGatewayConst::EXCROW)
                                    ->whereDate('created_at', $this_month_end)
                                    ->whereIn('status', [1,6])->get()->map(function($item){
                                        return [
                                            'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                        ];
                                    })->sum('amount');
        $this_week_trade_balance = Transaction::with('forexcrow')
                                    ->where('type', PaymentGatewayConst::EXCROW)
                                    ->whereDate('created_at', '>=', $this_weak)
                                    ->whereIn('status', [1,6])->get()->map(function($item){
                                        return [
                                            'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                        ];
                                    })->sum('amount');
        $this_month_trade_balance = Transaction::with('forexcrow')
                                    ->where('type', PaymentGatewayConst::EXCROW)
                                    ->whereDate('created_at', '>=', $this_month)
                                    ->whereIn('status', [1,6])->get()->map(function($item){
                                        return [
                                            'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                        ];
                                    })->sum('amount');
        $this_year_trade_balance = Transaction::with('forexcrow')
                                    ->where('type', PaymentGatewayConst::EXCROW)
                                    ->whereDate('created_at', '>=', $this_year)
                                    ->whereIn('status', [1,6])->get()->map(function($item){
                                        return [
                                            'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                        ];
                                    })->sum('amount');

        $trade_total_balance = Transaction::with('forexcrow')->where('type', PaymentGatewayConst::EXCROW)->get()->map(function($item){
            return [
                'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
            ];
        })->sum('amount');

        $today_trade =  Transaction::with('forexcrow')
                            ->where('type', PaymentGatewayConst::EXCROW)
                            ->whereIn('status', [1,6])
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)->get()->map(function($item){
                                return [
                                    'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                ];
                            })->sum('amount');
        $last_month_trade =  Transaction::whereIn('status', [1,6])
                                            ->with('forexcrow')
                                            ->where('type', PaymentGatewayConst::EXCROW)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)->get()->map(function($item){
                                                return [
                                                    'amount' => $item->request_amount / $item->forexcrow->saleCurrency->rate
                                                ];
                                            })->sum('amount');

        $last_month_trade_p = $last_month_trade == 0 ? 1 : $last_month_trade;
        $trade_percent = (($today_trade * 100) / $last_month_trade_p);

        if($trade_percent > 100){
            $trade_percent = 100;
        }

        // Marketplace

        $marketplace_balance = Transaction::where('type', PaymentGatewayConst::MARKETPLACE)->where('status', 1)->sum('request_amount');

        $today_marketplace =  Transaction::where('type', PaymentGatewayConst::MARKETPLACE)
                            ->where('status', 1)
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)->sum('request_amount');
        $last_month_marketplace =  Transaction::where('status', 1)->where('type', PaymentGatewayConst::MARKETPLACE)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)->sum('request_amount');

        $last_month_marketplace_p = $last_month_marketplace == 0 ? 1 : $last_month_marketplace;
        $marketplace_percent = (($today_trade * 100) / $last_month_marketplace_p);

        if($marketplace_percent > 100){
            $marketplace_percent = 100;
        }

        //User
        $total_user = User::toBase()->count();
        $unverified_user = User::toBase()->where('email_verified', 0)->count();
        $active_user = User::toBase()->where('status', 1)->count();
        $banned_user = User::toBase()->where('status', 0)->count();
        $total_user_p = $total_user == 0 ? 1 : $total_user;
        $user_percent =(($active_user * 100) / $total_user_p);

        if($user_percent > 100){
            $user_percent = 100;
        }

        // Subscriber
        $total_subscriber = Subscriber::toBase()->count();
        $today_subscriber = Subscriber::toBase()
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)
                            ->count();
        $last_month_subscriber = Subscriber::toBase()
                                        ->whereDate('created_at','>=',$last_month_start)
                                        ->whereDate('created_at','<=',$last_month_end)
                                         ->count();
        $last_month_subscriber_p = $last_month_subscriber == 0 ? 1 : $last_month_subscriber;
        $subscriber_percent =(($today_subscriber * 100) / $last_month_subscriber_p);

        if($subscriber_percent > 100){
            $subscriber_percent = 100;
        }

        // Monthly Add Money
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-31'));

        // Add Money
        $pending_data  = [];
        $success_data  = [];
        $canceled_data = [];
        $hold_data     = [];
        // Donation
        $donation_pending_data  = [];
        $donation_success_data  = [];
        $donation_canceled_data = [];
        $donation_hold_data     = [];
        $all_data    = [];

        $month_day  = [];

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);

            // Monthley add money
            $pending = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 2)
                                        ->count();
            $success = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 1)
                                        ->count();
            $canceled = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 4)
                                        ->count();
            $hold = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 3)
                                        ->count();
            $pending_data[]  = $pending;
            $success_data[]  = $success;
            $canceled_data[] = $canceled;
            $hold_data[]     = $hold;

            //Monthley Donation
            $trade_ongoing = Transaction::where('type', PaymentGatewayConst::EXCROW)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 1)
                        ->count();
            $trade_complete = Transaction::where('type', PaymentGatewayConst::EXCROW)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 6)
                        ->count();
            $trade_closed = Transaction::where('type', PaymentGatewayConst::EXCROW)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 3)
                        ->count();

            $trade_ongoing_data[]  = $trade_ongoing;
            $trade_complete_data[] = $trade_complete;
            $trade_closed_data[]    = $trade_closed;

            $trade = Transaction::where('type', PaymentGatewayConst::EXCROW)->whereDate('created_at',$start_date)->count();
            $marketplace = Transaction::where('type', PaymentGatewayConst::MARKETPLACE)->whereDate('created_at',$start_date)->count();

            $trade_data[]       = $trade;
            $marketplace_data[] = $marketplace;
            $all_data[]         = $trade + $marketplace;



            $month_day[] = date('Y-m-d', $start);
            $start = strtotime('+1 day',$start);
        }

        // Chart one
        $chart_one_data = [
            'pending_data'  => $pending_data,
            'success_data'  => $success_data,
            'canceled_data' => $canceled_data,
            'hold_data'     => $hold_data,
        ];
        // Chart two
        $chart_two_data = [
            'complete_data'  => $trade_complete_data,
            'ongoing_data'  => $trade_ongoing_data,
            'closed_data' => $trade_closed_data,
        ];


        // Chart three
        $chart_three_data = [
            'trade_data'       => $trade_data,
            'marketplace_data' => $marketplace_data,
            'all_data'         => $all_data,
        ];

        // Chart four | User analysis

        $chart_four = [$active_user, $banned_user,$unverified_user,$total_user];

        // Chart for Donation groth
        $chart_five = [round($today_trade_balance), round($this_week_trade_balance),round($this_month_trade_balance),round($this_year_trade_balance)];

        // Latest transaction
        $transactions = Transaction::with('forexcrow')->orderBy('id', 'desc')
        ->where('type', PaymentGatewayConst::MARKETPLACE)
        ->whereNot('type', PaymentGatewayConst::TYPEADDMONEY)
        ->whereHas('forexcrow', function($q){
            $q->where('user_id', Auth::id());
        })
        ->limit(3)->get();


        $data = [
            'add_money_balance'    => $add_money_balance,
            'today_add_money'      => $today_add_money,
            'last_month_add_money' => $last_month_add_money,
            'add_money_percent'    => $add_money_percent,

            'trade_balance'    => $trade_balance,
            'today_trade'      => $today_trade,
            'last_month_trade' => $last_month_trade,
            'trade_percent'    => $trade_percent,

            'marketplace_balance'    => $marketplace_balance,
            'today_marketplace'      => $today_marketplace,
            'last_month_marketplace' => $last_month_marketplace,
            'marketplace_percent'    => $marketplace_percent,

            'total_user'      => $total_user,
            'unverified_user' => $unverified_user,
            'active_user'     => $active_user,
            'user_percent'    => $user_percent,

            'total_subscriber'      => $total_subscriber,
            'today_subscriber'      => $today_subscriber,
            'last_month_subscriber' => $last_month_subscriber,
            'subscriber_percent'    => $subscriber_percent,

            'pending_add_money_balance'    => $pending_add_money_balance,
            'today_pending_add_money'      => $today_pending_add_money,
            'last_month_pending_add_money' => $last_month_pending_add_money,
            'pending_add_money_percent'    => $pending_add_money_percent,

            'chart_one_data'   => $chart_one_data,
            'chart_two_data'   => $chart_two_data,
            'chart_three_data'   => $chart_three_data,
            'chart_four_data'  => $chart_four,
            'chart_five_data'  => $chart_five,
            'month_day'        => $month_day,

            'transactions'        => $transactions
        ];

        return view('admin.sections.dashboard.index',compact(
            'page_title',
                    'data'
        ));
    }


    /**
     * Logout Admin From Dashboard
     * @return view
     */
    public function logout(Request $request) {

        $push_notification_setting = BasicSettingsProvider::get()->push_notification_config;

        // if($push_notification_setting) {
        //     $method = $push_notification_setting->method ?? false;

        //     if($method == "pusher") {
        //         $instant_id     = $push_notification_setting->instance_id ?? false;
        //         $primary_key    = $push_notification_setting->primary_key ?? false;

        //         if($instant_id && $primary_key) {
        //             $pusher_instance = new PushNotifications([
        //                 "instanceId"    => $instant_id,
        //                 "secretKey"     => $primary_key,
        //             ]);

        //             $pusher_instance->deleteUser("".Auth::user()->id."");
        //         }
        //     }

        // }

        $admin = auth()->user();
        try{
            $admin->update([
                'last_logged_out'   => now(),
                'login_status'      => false,
            ]);
        }catch(Exception $e) {
            // Handle Error
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');

    }

    /**
     * Function for clear admin notification
     */
    public function notificationsClear() {
        $admin = auth()->user();

        if(!$admin) {
            return false;
        }

        try{
            $admin->update([
                'notification_clear_at'     => now(),
            ]);
        }catch(Exception $e) {
            $error = ['error' => ['Something went wrong! Please try again.']];
            return Response::error($error,null,404);
        }

        $success = ['success' => ['Notifications clear successfully!']];
        return Response::success($success,null,200);
    }
}
