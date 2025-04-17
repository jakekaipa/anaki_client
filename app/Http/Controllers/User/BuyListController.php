<?php

namespace App\Http\Controllers\User;

use Exception;
use DateTime;
use DateInterval;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TradeOffer;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use Illuminate\Support\Carbon;
use App\Notifications\User\SendMail;
use App\Models\UserMailLog;
use App\Models\User;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\App;
use App\Models\ReferralPartner;

class BuyListController extends Controller
{

    /**
     * Buy List page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $page_title = __("Buy Offer List");

        //$json = json_encode($request->all());
        //Log::info("BuyListController.index".$json);
        
        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i:s');

        $user = Auth::User();

        $loginTime = $request->loginTime;
        
        $query = TradeOffer::where('status', '!=', -1)
            ->where('order_type', 'buy')
            ->with('user');

        if ($loginTime == "In30Min") {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1)->whereDate('updated_at', '>=', (new DateTime("now", new DateTimeZone("UTC")))->sub(DateInterval::createFromDateString('30 minutes')));
            });
        } else if ($loginTime == "In1Hour") {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1)->whereDate('updated_at', '>=', (new DateTime("now", new DateTimeZone("UTC")))->sub(DateInterval::createFromDateString('1 hours')));
            });
        } else if ($loginTime == "In5Hour") {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1)->whereDate('updated_at', '>=', (new DateTime("now", new DateTimeZone("UTC")))->sub(DateInterval::createFromDateString('5 hours')));
            });
        } else if ($loginTime == "In10Hour") {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1)->whereDate('updated_at', '>=', (new DateTime("now", new DateTimeZone("UTC")))->sub(DateInterval::createFromDateString('10 hours')));
            });
        } else if ($loginTime == "In24Hour") {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1)->whereDate('updated_at', '>=', (new DateTime("now", new DateTimeZone("UTC")))->sub(DateInterval::createFromDateString('24 hours')));
            });
        } else {
            $query->whereHas('user', function ($q) {
                $q->where('status', 1);
            });
        }


        if ($request->myOffer != "") {
            $query->where('user_id', $user->id);
        }

        if ($request->priceType != "") {

            $query->where('priceType', $request->priceType);
        }
        if ($request->offerTag != "") {

            $query->where('offerTag', $request->offerTag);
        }
        if (isset($request->sort_by) && !empty($request->sort_by)) {

            if ($request->sort_by == "MostRecent") {

                $query->orderBy('created_at', 'desc');
            } else if ($request->sort_by == "Oldest") {

                $query->orderBy('created_at', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $prices = WalletController::getPrices();
        
        $allData = $query->get();
        // Log::info('buyList');
        // Log::info($allData->toarray());
        // Calculate the price for each offer
        $sortedData = $allData->map(function ($item) use ($prices) {
            $item->calculated_price = $item->priceType == 0
                ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100
                : $item->fixedPrice;
            return $item;
        })->sortByDesc('calculated_price');

        $page = $request->input('page', 1);
        $perPage = 8;
        $listData = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedData->forPage($page, $perPage),
            $sortedData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );


        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        if ($request->ajax()) {
            return view('user.buy-list.partials.list', compact('listData', 'now', 'curTimeZone', 'prices'))->render();
        }

        return view('user.buy-list.index', compact("page_title", "listData", "now", "curTimeZone", "prices"));
    }

    /**
     * My forexcrow page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function preview($id)
    {
        $page_title = __("Details of Buy Offer");

        $item = TradeOffer::with('user')->findOrFail(urlSafeDecrypt($id));

        if (!$item) {
            return back()->with(['error' => ['Transaction failed. Please try again.']]);
        }

        // foreach($item as $key => $value){
        //    Log::info("user.buy-list.preview item Key:".$key." Value:".$value);
        // }

        //Log::info("user.buy-list.preview item:".$item);

        $now = new DateTime();

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        $isOwner = $item->user_id == Auth::User()->id;

        $ownerUser = $isOwner ? Auth::User() : User::where('id', $item->user_id)->first();
        $lastLoginInfo = json_decode($ownerUser->lastLoginInfo, true);

        $prices = WalletController::getPrices();

        $user = Auth::User();
        $usdtBalance = 0;
        $usdtPending = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance = $user->usdtBalance;
        }
        if ($user->usdtPending != null) {
            $usdtPending = $user->usdtPending;
        }

        return view('user.buy-list.preview', compact(
            "page_title",
            "item",
            "now",
            "curTimeZone",
            "isOwner",
            "prices",
            "ownerUser",
            "lastLoginInfo",
            "usdtBalance",
            "usdtPending"
        ));
    }

    public function edit($id)
    {
        $page_title = "Edit Buy Offer";

        $item = TradeOffer::with('user')->findOrFail($id);

        if (!$item) {
            return back()->with(['error' => ['Transaction failed. Please try again.']]);
        }

        $prices = WalletController::getPrices();

        $item->fixedPrice = preg_replace('/[^0-9.]/', '', $item->fixedPrice);

        return view('user.buy-list.edit', compact('page_title', 'item', "prices"));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priceType' => 'required|in:0,1',
            'tradeVolMin' => 'required|numeric|min:0',
            'tradeVolMax' => 'required|numeric|min:0|gt:tradeVolMin',
            'offerTimeLimit' => 'required|integer|min:1',
            'offerLabel' => 'required|string|min:2|max:1024',
            'offerCondition' => 'required|string|min:2|max:1024',
            'transGuide' => 'required|string|min:2|max:1024',
            'offerTag' => 'required|in:needID,specApproval,noNeedReceipt,onlySameBank,receiptRequired,noThirdParty,noAuthRequired',
            'secretTrade' => 'nullable',
            'password' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->with(['error' => ['Please fill in the required sections correctly.']]);
        }

        $item = TradeOffer::findOrFail($id);

        // Check if the authenticated user is the owner of the offer
        if (Auth::id() !== $item->user_id) {
            return back()->with('error', 'You are not authorized to edit this offer.');
        }

        $validated = $validator->validated();
        $requestData = $request->all();

        // Additional validation for PriceType
        if ($validated['priceType'] == 0) { // Market Price
            $offerMarginValidator = Validator::make($request->all(), [
                // 'offerMargin' => 'required|numeric|gt:4'
                'offerMargin' => 'required|numeric'
            ]);
            if ($offerMarginValidator->fails()) {
                return back()->with(['error' => ['Offer margin should be over 5%.']]);
            }
        } else { // Fixed Price
            $fixedPriceValidator = Validator::make($request->all(), [
                'fixedPrice' => 'required|numeric|gt:0'
            ]);
            if ($fixedPriceValidator->fails()) {
                return back()->with(['error' => ['Fixed price is incorrect.']]);
            }
        }

        $updateData = [
            'priceType' => $validated['PriceType'],
            'tradeVolMin' => $validated['tradeVolMin'],
            'tradeVolMax' => $validated['tradeVolMax'],
            'offerTimeLimit' => $validated['offerTimeLimit'],
            'offerLabel' => $validated['offerLabel'],
            'offerCondition' => $validated['offerCondition'],
            'transGuide' => $validated['transGuide'],
            'offerTag' => $validated['offerTag'],
            'password' => isset($validated['secretTrade']) && $validated['secretTrade'] ? $validated['password'] : null,
            'offerMargin' => $validated['priceType'] == 0 ? $requestData['offerMargin'] : 0,
            'fixedPrice' => $validated['priceType'] == 1 ? $requestData['fixedPrice'] : null,
            'needMobileAuth' => $request->has('checkboxMobileAuth'),
            'needKYCAuth' => $request->has('checkboxKYCAuth'),
            'needAccountAuth' => $request->has('checkboxAccountAuth'),
        ];

        try {
            $item->update($updateData);
        } catch (Exception $e) {
            Log::info("user.buy-list.update exception:" . $e);
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }

        return redirect()->route('user.buy-list.preview', $id)->with('success', 'Offer updated successfully.');
    }

    /**
     * This method for sell
     * 판매하기->내역 상세->바로 판매
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function sell(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'           => 'required',
            'price'             => 'required',
            'paidAmount'        => 'required|gt:0',
            'sendAmount'        => 'required|gt:0',
            'password'          => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required sections.']);
        }


        $requestData = $request->all();
        $validated = $validator->validated();

        $item = TradeOffer::with('user')->findOrFail(urlSafeDecrypt($validated['item_id']));

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Unable to find buy list item. Please try again.']);
        }

        if ($item->user_id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot sell your own trade.']);
        }

        if ($item->status == -1) {
            return response()->json(['success' => false, 'message' => 'This offer has been closed.']);
        }

        $now = new DateTime();


        if ($item->password != null) {
            if (array_key_exists('password', $validated) == false || $validated['password'] != $item->password) {
                return response()->json(['success' => false, 'message' => 'You entered an invalid password.']);
            }
        }

        $sendAmount = $validated['sendAmount'];

        $user = Auth::User();

        if ($item->needMobileAuth > 0) {
            if ($user->sms_verified != 1) {
                return response()->json(['success' => false, 'message' => __('전화번호 인증이 되지 않은 사용자는 이 거래할 수 없습니다.')]);
            }
        }

        if ($item->needKYCAuth > 0) {
            if ($user->kyc_verified != 1) {
                return response()->json(['success' => false, 'message' => __('KYC 인증이 되지 않은 사용자는 이 거래할 수 없습니다.')]);
            }
        }

        if ($item->needAccountAuth > 0) {
            if ($user->account_verified) {
                return response()->json(['success' => false, 'message' => __('계좌 인증이 되지 않은 사용자는 이 거래할 수 없습니다.')]);
            }
        }


           // 수수료 계산
        Log::info('BuyList sellerUserId = '.$user->id);

        // 기본 수수료
        $rate = 0.8;
        $referralFeeInfo = ReferralPartner::where('user_id', $user->id)->first();  
        Log::info('referralFeeInfo = '.$referralFeeInfo);
   
        if($referralFeeInfo){
            $rate = $referralFeeInfo->fee_rate;
        } 
   
        Log::info('rate = '.$rate);
   
        $fee =  $sendAmount * ($rate / 100);

        //$fee = getUSDTFee($user, $sendAmount);

        //출금 가능액 검사
        $usdtBalance = 0;
        $usdtPending = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance = $user->usdtBalance;
        }
        if ($user->usdtPending != null) {
            $usdtPending = $user->usdtPending;
        }

        $withdrawableUSDTBalance = $usdtBalance - $usdtPending;
        if ($sendAmount + $fee > $withdrawableUSDTBalance) {
            return response()->json(['success' => false, 'message' => __('You don\'t have enough USDT balance.')]);
        }


        if ($validated['paidAmount'] < $item->tradeVolMin) {
            return response()->json(['success' => false, 'message' => __('The transaction amount is below the minimum trading limit.')]);
        }

        if ($validated['paidAmount'] > $item->tradeVolMax) {
            return response()->json(['success' => false, 'message' => __('The transaction amount exceeds the maximum trading limit.')]);
        }

        $offerEndTime = new DateTime();
        $offerEndTime->add(new DateInterval('PT' . $item->offerTimeLimit . 'M'));

        //insert new trade_transactions
        $insertData = [
            'created_at'        => $now,
            'ended_at'          => $offerEndTime,
            'state'             => 'open',
            'cancelReason'      => "",
            'order_type'        => "buy",
            'offer_user_id'     => $item->user_id,
            'client_user_id'    => Auth::id(),
            'tetherAmount'      => $validated['sendAmount'],
            'fee'               => $fee,
            'priceType'         => $item->priceType,
            'price'             => $validated['price'],
            'margin'            => $item->offerMargin,
            'totalPayAmount'    => $validated['paidAmount'],
            'bankName'          => $item->bankName,
            'accountNumber'     => $item->accountNumber,
            'accountName'       => $item->accountName,
            'QRImage'           => $item->payQR,
            'payProof'          => "",
            'buyListId'         => urlSafeDecrypt($validated['item_id']),
            'sellListId'        => -1,
        ];

        $lastInsertedId = 0;

        DB::beginTransaction();
        try {
            $lastInsertedId = DB::table("trade_transactions")->insertGetId($insertData);
            DB::commit();

            Log::info("BuyListController.buy insertDB result:" . json_encode($lastInsertedId));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("BuyListController.buy insertDB error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to insert into database.']);
        }


        try {

            //에스크로 걸기
            $usdtPending = 0;

            if ($user->usdtPending != null) {
                $usdtPending = $user->usdtPending;
            }

            $usdtPending += $sendAmount + $fee;

            $user->usdtPending = $usdtPending;
            $user->save();
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to process escrow.']);
        }

        $prices = WalletController::getPrices();

        $notification_content = [
            'title'   => __('거래 시작됨'),
            'message' => __(
                '새 거래(:nickname)가 시작되었습니다. :nickname님이 :amount KRW 상당의 USDT를 판매하고 있습니다. ',
                [
                    'nickname' => $user->realname ?? $user->username,
                    'amount' => number_format($sendAmount * $prices['KRW'])
                ]
            ),
            'time'    => Carbon::now()->diffForHumans(),
            'image'   => files_asset_path('profile-default'),
        ];

        UserNotification::create([
            'type'          => NotificationConst::TRADE_STARTED,
            'sender_id'       => Auth::id(),
            'user_id'       => $item->user_id,
            'order_id'      => $lastInsertedId,
            'message'       => $notification_content,
        ]);

        $buyerUser = User::where('id', $item->user_id)->first();

        // 링크 제거
        // $linkUrl = App::make('url')->to('/user/mytrade');

        //Send email
        $emailParam = [
            'subject'       =>  __('거래 시작됨'),
            'message'       => __(
                '새 거래(:nickname)가 시작되었습니다. :nickname님이 :amount KRW 상당의 USDT를 판매하고 있습니다.',
                [
                    'nickname' => $user->realname ?? $user->username,
                    'amount' => number_format($sendAmount * $prices['KRW'])
                ]
            ),
            'username'      => $buyerUser->username,
            'user_id'       => $user->id,
            'method'        => "SMTP",
        ];

        try {
            UserMailLog::create($emailParam);
            $buyerUser->notify(new SendMail((object) $emailParam));
        } catch (Exception $e) {
            \Log::info($e);
            return response()->json(['success' => false, 'message' => 'Failed to send email.']);
        }

        $client_email = $buyerUser->email;

        return response()->json([
            'success' => true,
            'item_id' => urlSafeEncrypt($lastInsertedId),
            'order_id' => $lastInsertedId,
            'client_email' => $client_email,
            'message' => __('거래 요청으로 테더의 에스크로가 완료되었습니다. 증빙 자료를 확인해주세요')
        ]);
    }

    public function canceloffer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'item_id'           => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required sections.']);
        }

        $validated = $request->all();

        try {
            DB::table("trade_offers")->where('id', urlSafeDecrypt($validated['item_id']))->update([
                'status'     => -1,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to cancel offer.']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Offer successfully closed.')
            ]);
        }

        return redirect()->route('user.buy-list')->with('success', __('Offer successfully closed.'));
    }
}
