<?php

namespace App\Http\Controllers\User;

use Exception;
use DateTime;
use DateInterval;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TradeOffer;
use App\Models\UserNotification;
use App\Constants\NotificationConst;
use Illuminate\Support\Carbon;
use App\Notifications\User\SendMail;
use App\Models\UserMailLog;
use App\Models\User;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\App;
use App\Models\ReferralPartner;

class SellListController extends Controller
{

    /**
     * Sell List page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $page_title = __("Sell Offers List");

        //$json = json_encode($request->all());
        //Log::info("SellListController.index".$json);

        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i:s');
        $nowUTC = new DateTime("now", new DateTimeZone("UTC"));

        $user = Auth::User();

        $loginTime = $request->loginTime;
        
        $query = TradeOffer::where('status', '!=', -1)
            ->where('order_type', 'sell')
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
        // Calculate the price for each offer
        $sortedData = $allData->map(function ($item) use ($prices) {
            $item->calculated_price = $item->priceType == 0
                ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100
                : $item->fixedPrice;
            return $item;
        })->sortBy('calculated_price');

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
            return view('user.sell-list.partials.list', compact('listData', 'now', 'curTimeZone', 'prices'))->render();
        }

        return view('user.sell-list.index', compact("page_title", "listData", "now", "curTimeZone", "prices"));
    }

    public function previewList(Request $request)
    {
        $page_title = __("Sell Offer List");

        //$json = json_encode($request->all());
        //Log::info("SellListController.index".$json);

        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i:s');
        $nowUTC = new DateTime("now", new DateTimeZone("UTC"));

        $loginTime = $request->loginTime;

        $query = TradeOffer::where('status', '!=', -1)
            ->where('order_type', 'sell')
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

        $listData = $query->paginate(8);

        // foreach($listData as $key => $value){
        //    Log::info("user.sell-list.index listData Key:".$key." Value:".$value);
        // }

        $prices = WalletController::getPrices();

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        if ($request->ajax()) {
            return view('user.sell-list.partials.list', compact('listData', 'now', 'curTimeZone', 'prices'))->render();
        }

        return view('user.sell-list.index', compact("page_title", "listData", "now", "curTimeZone", "prices"));
    }

    /**
     * 구매하기 상세 페이지
     */
    public function preview($id)
    {
        $page_title = __("Details of Sell Offer");
        
        $item = TradeOffer::with('user')->findOrFail(urlSafeDecrypt($id));
        
       

        if (!$item) {
            return back()->with(['error' => ['Unable to find sell list item. Please try again.']]);
        }

       
        // foreach($item as $key => $value){
        //    Log::info("user.sell-list.preview item Key:".$key." Value:".$value);
        // }

        Log::info("user.sell-list.preview item:" . $item);
       
        $now = new DateTime();

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        $isOwner = $item->user_id == Auth::User()->id;

        $ownerUser = $isOwner ? Auth::User() : User::where('id', $item->user_id)->first();
        $lastLoginInfo = json_decode($ownerUser->lastLoginInfo, true);

        $prices = WalletController::getPrices();

        return view('user.sell-list.preview', compact(
            "page_title",
            "item",
            "now",
            "curTimeZone",
            "isOwner",
            "prices",
            "ownerUser",
            "lastLoginInfo",
        ));
    }

    public function edit($id)
    {
        $page_title = "Edit Sell Offer";

        $item = TradeOffer::with('user')->findOrFail($id);

        if (!$item) {
            return back()->with(['error' => ['Transaction failed. Please try again.']]);
        }


        $prices = WalletController::getPrices();

        $item->fixedPrice = preg_replace('/[^0-9.]/', '', $item->fixedPrice);

        return view('user.sell-list.edit', compact('page_title', 'item', "prices"));
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

        // Additional validation for
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
            'priceType' => $validated[''],
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
            Log::info("user.sell-list.update exception:" . $e);
            return back()->with(['error' => ['An error occurred. Please try again.']]);
        }

        return redirect()->route('user.sell-list.preview', $id)->with('success', 'Offer updated successfully.');
    }

    /**
     * This method for buy
     * 구매하기->내역 상세-> 바로구매
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function buy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'           => 'required',
            'price'             => 'required',
            'payAmount'         => 'required|gt:0',
            'recvAmount'        => 'required|gt:0',
            'password'          => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required sections.']);
        }

        $requestData = $request->all();
        $validated = $validator->validated();

        $item = TradeOffer::with('user')->findOrFail(urlSafeDecrypt($validated['item_id']));

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Unable to find sell list item. Please try again.']);
        }

        if ($item->user_id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot buy your own trade.']);
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

        $user = Auth::User();

        //Log::info("SellListController.buy needMobileAuth:".$item->needMobileAuth." needKYCAuth:".$item->needKYCAuth." needAccountAuth:".$item->needAccountAuth);
        //Log::info("SellListController.buy sms_verified:".$user->sms_verified." kyc_verified:".$user->kyc_verified." account_verified:".$user->account_verified);

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
            if ($user->account_verified != 1) {
                return response()->json(['success' => false, 'message' => __('계좌 인증이 되지 않은 사용자는 이 거래할 수 없습니다.')]);
            }
        }
        
        // 구매 요청 코인수
        $usdtWillRecv = $validated['recvAmount'];

        $sellerUser = User::where('id', $item->user_id)->first();

        //출금 가능액 검사
        $usdtBalance = 0;
        if ($sellerUser->usdtBalance != null) {
            $usdtBalance = $sellerUser->usdtBalance;
        }

        $usdtPending = 0;
        if ($sellerUser->usdtPending != null) {
            $usdtPending = $sellerUser->usdtPending;
        }

        // 유저가 현재 가지고 있는 코인수
        $withdrawableUSDTBalance = $usdtBalance - $usdtPending;
        // Log::info("SellListController.buy withdrawableUSDTBalance:".$withdrawableUSDTBalance." usdtBalance:".$usdtBalance." usdtPending:".$usdtPending);
        if ($usdtWillRecv > $withdrawableUSDTBalance) {
            return response()->json(['success' => false, 'message' => __('The seller does not have sufficient USDT balance.')]);
        }

        if ($validated['payAmount'] < $item->tradeVolMin) {
            return response()->json(['success' => false, 'message' => __('The transaction amount is below the minimum trading limit.')]);
        }

        if ($validated['payAmount'] > $item->tradeVolMax) {
            return response()->json(['success' => false, 'message' => __('The transaction amount exceeds the maximum trading limit.')]);
        }

        $offerEndTime = new DateTime();
        $offerEndTime->add(new DateInterval('PT' . $item->offerTimeLimit . 'M'));

        $usdtPending = 0;
        if ($sellerUser->usdtPending != null) {
            $usdtPending = $sellerUser->usdtPending;
        }

         // 수수료 계산
         Log::info('SellList sellerUserId = '.$sellerUser->id);

        // 기본 수수료
        $rate = 0.8;
        $referralFeeInfo = ReferralPartner::where('user_id', $sellerUser->id)->first();  
        Log::info('referralFeeInfo = '.$referralFeeInfo);

        if($referralFeeInfo){
             $rate = $referralFeeInfo->fee_rate;
        } 

        Log::info('rate = '.$rate);

        $fee =  $usdtWillRecv * ($rate / 100);


        // $fee = getUSDTFee($sellerUser, $usdtWillRecv);
        

        //insert new trade_transactions
        $insertData = [
            'created_at'        => $now,
            'ended_at'          => $offerEndTime,
            'state'             => 'open',
            'cancelReason'      => "",
            'order_type'        => "sell",
            'offer_user_id'     => $item->user_id,
            'client_user_id'    => Auth::id(),
            'tetherAmount'      => $usdtWillRecv,
            'fee'               => $fee,
            'priceType'         => $item->priceType,
            'price'             => $validated['price'],
            'margin'            => $item->offerMargin,
            'totalPayAmount'    => $validated['payAmount'],
            'bankName'          => $item->bankName,
            'accountNumber'     => $item->accountNumber,
            'accountName'       => $item->accountName,
            'QRImage'           => $item->payQR,
            'payProof'          => "",
            'buyListId'         => -1,
            'sellListId'        => urlSafeDecrypt($validated['item_id']),
        ];

        $lastInsertedId = 0;

        DB::beginTransaction();
        try {
            $lastInsertedId = DB::table("trade_transactions")->insertGetId($insertData);
            DB::commit();

            Log::info("SellListController.buy insertDB result:" . json_encode($lastInsertedId));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("SellListController.buy insertDB error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to insert into database.']);
        }

        try {
            //에스크로 걸기
            $usdtPending += $usdtWillRecv + $fee;

            $sellerUser->usdtPending = $usdtPending;
            $sellerUser->save();
        } catch (Exception $e) {
            Log::info("SellListController.buy Fail to process excrow :" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to process escrow.']);
        }

        $prices = WalletController::getPrices();

        $notification_content = [
            'title'   => __('거래 시작됨'),
            'message' => __(
                '새 거래(:nickname)가 시작되었습니다. :nickname님이 :amount KRW 상당의 USDT를 구매하고 있습니다.',
                [
                    'nickname' => $user->realname ?? $user->username,
                    'amount' => number_format($usdtWillRecv * $prices['KRW'])
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

        $linkUrl = App::make('url')->to('/user/mytrade');

        //Send email
        $emailParam = [
            'subject'       => __('거래 시작됨'),
            'message' => __(
                '새 거래(:nickname)가 시작되었습니다. :nickname님이 :amount KRW 상당의 USDT를 구매하고 있습니다.',
                [
                    'nickname' => $user->realname ?? $user->username,
                    'amount' => number_format($usdtWillRecv * $prices['KRW'])
                ]
            ) . PHP_EOL . $linkUrl,
            'username'      => $sellerUser->username,
            'user_id'       => $user->id,
            'method'        => "SMTP",
        ];

        try {
            UserMailLog::create($emailParam);
            $sellerUser->notify(new SendMail((object) $emailParam));
        } catch (Exception $e) {
            \Log::info($e);
            return response()->json(['success' => false, 'message' => 'Failed to send email.']);
        }

        return response()->json([
            'success' => true,
            'item_id' => urlSafeEncrypt($lastInsertedId),
            'order_id' => $lastInsertedId,
            'message' => __('구매 요청하신 테더의 에스크로가 완료되었습니다. 입금 후 증빙자료를 보내주세요.')
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

        $itemId = urlSafeDecrypt($validated['item_id']);
        Log::info("SellListController.canceloffer itemId:" . $itemId);

        try {
            DB::table("trade_offers")->where('id', $itemId)->update([
                'status'     => -1,
            ]);
        } catch (Exception $e) {
            Log::info("SellListController.canceloffer error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to cancel offer.']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Offer successfully closed.')
            ]);
        }

        return redirect()->route('user.sell-list')->with('success', __('Offer successfully closed.'));
    }
}
