<?php

namespace App\Http\Controllers\User;

use Exception;
use DateTime;
use DateTimeZone;

use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WalletController;
use App\Models\Mongo\Transfer;
use App\Models\Mongo\Wallet;
use App\Models\TradeTransaction;
use App\Models\UserNotification;
use App\Models\UserSupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\InternalTransfer;

class DashboardController extends Controller
{
    public function index(Request $request, $order_id = null)
    {   
        $page_title = "Dashboard";

        $user = auth()->user();

        $usdtBalance = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance = $user->usdtBalance;
        }
     
        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i:s');

        // $query = TradeTransaction::whereIn('state', ['open', 'send', 'dispute'])
        //     ->where(function ($query) use ($user) {
        //         $query->where('offer_user_id', $user->id)
        //             ->orWhere('client_user_id', $user->id);
        //     })
        //     ->where('ended_at', '>=', $nowText)
        //     ->with(['client_user'])
        //     ->orderBy('created_at', 'desc');
        
        $query = TradeTransaction::whereIn('state', ['open', 'send', 'dispute'])
            ->where(function ($query) use ($user) {
                $query->where('offer_user_id', $user->id)
                    ->orWhere('client_user_id', $user->id);
            })
            ->where(function ($query) use ($nowText) {
                $query->whereIn('state', ['dispute'])  // dispute 상태인 경우
                    ->orWhere(function ($query) use ($nowText) {
                        $query->whereIn('state', ['open', 'send'])  // open, send 상태인 경우
                                ->where('ended_at', '>=', $nowText);  // ended_at 기준으로 필터링
                    });
            })
            ->with(['client_user'])
            ->with(['getLastMessage' => function ($query) {
                $query->orderBy('id', 'desc');  
            }])
            ->orderBy('created_at', 'desc');
            
        $listData = $query->paginate(10);

        $ongoing_trade = $query->count();

        $active_ticket = UserSupportTicket::authTickets()->toBase()->count();

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        $usdtPending = $user->usdtPending;

        if (request()->ajax()) {
            return view('user.dashboard.partials.trade-list', compact('listData', 'curTimeZone'))->render();
        }

        return view('user.dashboard', compact('page_title', 'user', 'listData', 'usdtBalance', 'usdtPending', 'ongoing_trade', 'active_ticket', 'now', 'curTimeZone', 'order_id'));
    }

    public function loadMoreTrades(Request $request)
    {
        $user = auth()->user();
        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i:s');

        $query = TradeTransaction::whereIn('state', ['open', 'send', 'dispute'])
            ->where(function ($query) use ($user) {
                $query->where('offer_user_id', $user->id)
                    ->orWhere('client_user_id', $user->id);
            })
            ->where('ended_at', '>=', $nowText)
            ->with(['client_user']);

        $listData = $query->paginate(10); // Change 10 to your desired items per page

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        if ($request->ajax()) {
            return view('user.dashboard.partials.trade-list', compact('listData', 'curTimeZone'))->render();
        }

        return back()->with(['error' => ['Invalid request']]);
    }

    public function donationHistory()
    {
        // $page_title = "Donation History";

        // $donation_history = Transaction::with('campaign')->orderBy('id', 'desc')->Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->paginate(8);

        // return view('user.donation-history',compact("page_title", "donation_history"));
        return back()->with(['error' => ['Something went wrong. Please try again.']]);
    }

    public function logout(Request $request)
    {
        //Log::info("DashboardController.logout#1");
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login')->with(['success' => ['Logout successful.']]);
    }


    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target'        => 'required',
        ]);
        $validated = $validator->validate();
        $user = auth()->user();
        try {
            $user->status = 0;
            $user->save();
            Auth::logout();
            return redirect()->route('index')->with(['success' => ['Your account deleted successfully.']]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
    }
}
