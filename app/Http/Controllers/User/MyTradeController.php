<?php

namespace App\Http\Controllers\User;

use DateTime;
use DateTimeZone;
use Exception;

use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WalletController;
use App\Models\TradeTransaction;
use App\Models\User;
use App\Models\UserMailLog;
use App\Models\UserNotification;
use App\Models\Message;
use App\Notifications\User\SendMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MyTradeController extends Controller
{

    /**
     * Sell List page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //Log::info("MyTradeController.index");

        $page_title = "My Trade";

        $now = new DateTime();
        $user = Auth::User();

        $query = TradeTransaction::whereIn('state', ['done', 'cancel','dispute-solved'])
            ->where(function ($q) use ($user) {
                $q->where('offer_user_id', $user->id)
                    ->orWhere('client_user_id', $user->id);
            })
            ->orderBy('created_at', 'desc');


        $listData = $query->paginate(10);

        // foreach($listData as $key => $value){
        //    Log::info("user.my-trade.index listData Key:".$key." Value:".$value);
        // }


        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        if ($request->ajax()) {
            return view('user.my-trade.partials.list', compact('listData', 'curTimeZone'))->render();
        }

        return view('user.my-trade.index', compact("page_title", "listData", "now", "curTimeZone"));
    }

    /**
     * 상대방 이체자료 및 증빙자료 업로드 완료시 메일 보낼때 보내던 링크 사용 X 
     */
    public function view($id)
    {
        // Log::info("MyTradeController.view");

        // $page_title = __("Trade Details");

        $item = TradeTransaction::findOrFail(urlSafeDecrypt($id));

        if (!$item) {
            if (request()->ajax()) {
                return response()->json(['sucess' => false, 'message' => 'Unable to find transaction item. Please try again.']);
            }
            return response()->json(['sucess' => false, 'message' => 'Unable to find transaction item. Please try again.']);
        }

        // Log::info("user.my-trade.view item :" . $item);

        // $now = new DateTime();
        // $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');
        // $isBuyer = $item->buyer_user_id == auth()->user()->id;
        // $prices = WalletController::getPrices();
        // $otherUser = $isBuyer ? User::where('id', $item->seller_user_id)->first() : User::where('id', $item->buyer_user_id)->first();
        // $lastLoginInfo = json_decode($otherUser->lastLoginInfo, true);

        // $usdtBalance = "0";
        // $user = Auth::User();

        // if ($isBuyer == false) {
        //     $usdtBalance1 = $user->usdtBalance ?? 0;
        //     $usdtPending = $user->usdtPending ?? 0;

        //     $usdtBalance = "$usdtBalance1 (pending:$usdtPending)";
        // }

        // Log::info("request()->ajax()" . request()->ajax());


        // if (request()->ajax()) {
        return response()->json([
            'item' => $item,
            // 'isBuyer' => $isBuyer,
            // 'prices' => $prices,
            // 'otherUser' => $otherUser,
            // 'usdtBalance' => $usdtBalance,
            // 'lastLoginInfo' => $lastLoginInfo,
            // 'marketPrice' => $prices['KRW'] ?? null,
            // 'offerMargin' => $item->priceType == 0 ? $item->offerMargin : 0,
            // 'qrCodeImage' => $item->qrCodeImage ?? null,
        ]);
        // }

        // return view('user.my-trade.view', compact("page_title", "item", "now", "curTimeZone", "isBuyer", "prices", "otherUser", "usdtBalance", "lastLoginInfo"));
    }

    public function details($id)
    {
        $id = urlSafeDecrypt($id);

        $item = TradeTransaction::with('offer_user', 'client_user')->findOrFail($id);

        // 접속 유저가 recevier일때 메시지 읽음 처리 
        $messageIds = Message::where([
                            ['trade_transaction_id', $id],
                            ['read_user_id', 0],
                            ['receiver_id',Auth::user()->id],
                        ])
                        ->pluck('id');

        if($messageIds->isNotEmpty()) {    
            // 벌크 업데이트
            Message::whereIn('id',$messageIds)->update(
                [
                    'read_user_id' => Auth::user()->id
                ]);
        }       

        if (!$item) {
            return response()->json(['error' => 'Unable to find transaction item.'], 404);
        }

        $isBuyer = $item->client_user_id == auth()->user()->id;
        $prices = WalletController::getPrices();

        $now = new DateTime();
        $curTimeZone = new DateTimeZone(Auth::user()->lastLoginTimeZone ?: 'Asia/Seoul');
        $now->setTimezone($curTimeZone);

        $createdAt = new DateTime($item->created_at);
        $createdAt->setTimezone($curTimeZone);

        $endedAt = new DateTime($item->ended_at);
        $endedAt->setTimezone($curTimeZone);

        $data = [
            'chat_id' => $id . '-' . $item->offer_user->id . '-' . $item->client_user->id,
            'receiver_id' => ($item->offer_user->id == Auth::id()) ? $item->client_user->id : $item->offer_user->id,
            'id' => $item->id,
            'state' => $item->state,
            'order_type' => $item->order_type,
            'offer_username' => $item->offer_user->username,
            'offer_realname' => $item->offer_user->realname,
            'offer_email' => $item->offer_user->email,
            'client_username' => $item->client_user->username,
            'client_realname' => $item->client_user->realname,
            'client_email' => $item->client_user->email,
            'price' => $item->price,
            'price_change' => $item->price - $prices['KRW'],
            'price_type'    => $item->priceType,
            'market_price' => $prices['KRW'],
            'total_amount' => $item->totalPayAmount,
            'tether_amount' => $item->tetherAmount,
            'created_at' => $createdAt->format('Y-m-d H:i'),
            'ended_at' => $endedAt->format('Y-m-d H:i'),
            'is_buyer' => $isBuyer,
            'cancel_reason' => $item->cancelReason,
            'price_type' => $item->priceType,
            'margin' => $item->margin,
            'bank_name' => $item->bankName,
            'account_number' => $item->accountNumber,
            'account_name' => $item->accountName,
            'qr_image' => $item->QRImage,
            'pay_proof' => $item->payProof,
            'buy_list_id' => $item->buyListId,
            'sell_list_id' => $item->sellListId,
            'isTransferred' => $item->isTransferred,
        ];

        return response()->json($data);
    }

    public function uploadproof(Request $request)
    {
        // $requestJson = json_encode($request, JSON_PRETTY_PRINT);
        // Log::info("MyTradeController.uploadproof #0 :" . $requestJson);

        $validated = Validator::make($request->all(), [
            'itemId'        => 'required',
            // 'imageProof'    => 'required|mimes:jpg,png,jpeg,pdf',
        ])->validate();

        $updateData = [
            'state'    => 'send',
        ];

        // if($validator->fails()){
        //     return response()->json(['success' => false, 'message' => 'Please specify an image file.']);
        // }

        //$validated = $validator->validate();
        try {
            // $validatedJson = json_encode($validated, JSON_PRETTY_PRINT);
            // Log::info("MyTradeController.uploadproof #1 : " . $validatedJson);

            if ($request->hasFile("imageProof")) {
                
                $allowedMimeTypes = ['image/jpeg', 'image/png']; // 허용된 파일 타입 목록
                $file = $request->file('imageProof');

                // 파일의 mime 타입을 체크
                if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                    throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
                }

                $image = upload_file($request->file('imageProof'), 'proof');

                if (!$image || !isset($image['dev_path'])) {
                    throw new Exception("Failed to upload file");
                }

                Log::info("MyTradeController.uploadproof #2 image:" . $image['dev_path']);

                $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'proof');
                if (!$upload_image) {
                    throw new Exception("Failed to process uploaded file");
                }

                Log::info("MyTradeController.uploadproof #3 image:" . $upload_image);
                if (File::extension($image['dev_path']) != 'pdf') {
                    delete_file($image['dev_path']);
                }
                $updateData['payProof'] = $upload_image;
            }
        } catch (Exception $e) {
            Log::info("MyTradeController.uploadproof error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }

        try {
            $itemId = urlSafeDecrypt($validated['itemId']);
            DB::table('trade_transactions')->where('id', $itemId)->update($updateData);

            $item = TradeTransaction::with('offer_user')->with('client_user')->findOrFail($itemId);

            $now = new DateTime();
            if ($item->ended_at < $now) {
                return response()->json(['success' => false, 'message' => 'This transaction has expired.']);
            }

            $authUser = auth()->user();
            $offerUser = User::where('id', $item->offer_user_id)->first();

            $notification_content = [
                'title'   => __('거래 결제 완료됨'),
                'message' => __(':nickname님이 거래에 대한 이체자료 및 증빙자료 업로드를 하고 전송을 완료했습니다.', ['nickname' => $authUser->username ?? $authUser->realname]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::PROOF_RECEIVE,
                'sender_id'       => Auth::id(),
                'user_id'       => $item->offer_user_id,
                'message'       => $notification_content,
            ]);

            UserNotification::create([
                'type'          => NotificationConst::PROOF_SENT,
                'sender_id'       => Auth::id(),
                'user_id'       => $authUser->id,
                'message'       => $notification_content,
            ]);

            // 메일 링크 제거 
            // $linkUrl = App::make('url')->to('/user/mytrade/view/' . urlSafeEncrypt($itemId));

            //Send email
            $emailParam = [
                'subject'       => __('거래 결제 완료됨'),
                'message'       =>  __(':nickname님이 거래에 대한 이체자료 및 증빙자료 업로드를 하고 전송을 완료했습니다.', ['nickname' => $authUser->username ?? $authUser->realname]) . PHP_EOL,
                'username'      => $offerUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $offerUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.uploadproof error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            $emailParam = [
                'subject'       => __('거래 결제 완료됨'),
                'message'       =>  __(':nickname님이 거래에 대한 이체자료 및 증빙자료 업로드를 하고 전송을 완료했습니다.', ['nickname' => $authUser->username ?? $authUser->realname]) . PHP_EOL,
                'username'      => $authUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $authUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.uploadproof error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            return response()->json(['success' => true, 'message' =>  'Upload successful.']);
        } catch (Exception $e) {
            Log::info("MyTradeController.uploadproof error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function updatebankinfo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'itemId'        => 'required',
            'bankName'      => 'required',
            'accountNumber' => 'required',
            'accountName'   => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with(['error' => ['Please fill in the required fields.']]);
        }

        $validated = $validator->validate();


        $updateData = [
            'bankName'      => $validated['bankName'],
            'accountNumber' => $validated['accountNumber'],
            'accountName'   => $validated['accountName'],
        ];

        try {
            DB::table('trade_transactions')->where('id', $validated['itemId'])->update($updateData);

            return back()->with(['success' => ['Bank information updated successfully.']]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
    }

    public function uploadQRImage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'itemId'        => 'required',
            'QRImage'       => "required|mimes:jpg,png,jpeg,pdf",
        ]);

        if ($validator->fails()) {
            return back()->with(['error' => ['Please specify an image file.']]);
        }

        $validated = $validator->validate();


        if ($request->hasFile("QRImage")) {
            $image = upload_file($validated['QRImage'], 'QRImage');
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'QRImage');
            delete_file($image['dev_path']);
            $validated['QRImage']     = $upload_image;
        }

        $updateData = [
            'QRImage'      => $validated['QRImage'],
        ];

        try {
            DB::table('trade_transactions')->where('id', $validated['itemId'])->update($updateData);

            return back()->with(['success' => ['QR image uploaded successfully.']]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
    }

    public function downloadpdf($id)
    {
        $id = urlSafeDecrypt($id);

        Log::info("user.my-trade.downloadpdf id :" . $id);

        $page_title = __("Trade Details");

        $item = TradeTransaction::with('offer_user')->with('client_user')->findOrFail($id);

        if (!$item) {
            return back()->with(['error' => ['Unable to find transaction item. Please try again.']]);
        }

        //Log::info("user.my-trade.view item :".$item);

        $now = new DateTime();

        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        $user =  Auth::User();

        // $sellerUser = $isBuyer ? $otherUser : $user;
        // $fee = getUSDTFee($sellerUser, $item->tetherAmount);
        $fee = $item->fee;
       
        // $margin = (int)($totalPayAmountNum / ((100 + $item->margin) / 100) * ($item->margin / 100));
        $margin = number_format(round($fee * $item->price,0)); 

        $prices = WalletController::getPrices();
        $marketPrice = $prices['KRW'];

        $marketRate = round($item->price / $marketPrice,2);

        $createdAt =  $item->created_at;
        $endedAt =  $item->ended_at;

        if ($item->order_type == 'buy') {
            $tradingDirection = 'sell';
            $sellerName = ($item->client_user->realname)? $item->client_user->realname : $item->client_user->username;
            $buyerName  = ($item->offer_user->realname)? $item->offer_user->realname : $item->offer_user->username;

        } else {
            $tradingDirection = 'buy';
            $sellerName = ($item->offer_user->realname)? $item->offer_user->realname : $item->offer_user->username; 
            $buyerName  = ($item->client_user->realname)? $item->client_user->realname : $item->client_user->username;
        }

        if ($item->offer_user_id == auth()->user()->id) {
            $tradingDirection = $item->order_type;
        }

        $logoImageData = base64_encode(file_get_contents(public_path('/pub/img/logo-basics@2x.png')));
        $logoImageSrc = 'data:image/png;base64,' . $logoImageData;
        $pdf = Pdf::loadView('user.my-trade.pdf', compact(
            "page_title", "sellerName","buyerName", "item", "now", "curTimeZone",
            "tradingDirection", "fee", "margin", "marketRate", "logoImageSrc","createdAt","endedAt"
        ))->setPaper('a4', 'portrait')->setOption('defaultFont', 'NanumGothic'); 
        return $pdf->stream('trade-details.pdf');
                 
    }

    public function send($id)
    {

        $item = TradeTransaction::with('offer_user')->with('client_user')->findOrFail($id);

        if (!$item) {
            return back()->with(['error' => ['Unable to find this transaction item. Please try again.']]);
        }

        //Log::info("user.my-trade.view item :".$item);

        if ($item->state != 0) {
            return back()->with(['error' => ['This trade has finished.']]);
        }

        $now = new DateTime();
        if ($item->ended_at < $now) {
            return back()->with(['error' => ['This transaction has expired.']]);
        }

        $amount = $item->tetherAmount;
        $fee = $item->fee;

        $authUser = auth()->user();
        $buyerUser = User::where('id', $item->client_user_id)->first();

        if ($authUser->id != $item->seller_user_id) {
            return back()->with(['error' => ['You cannot send Tether because you are not the seller.']]);
        }

        $usdtBalance = 0;
        if ($authUser->usdtBalance != null) {
            $usdtBalance = $authUser->usdtBalance;
        }
        $usdtPending = 0;
        if ($authUser->usdtPending != null) {
            $usdtPending = $authUser->usdtPending;
        }

        if ($usdtPending < $amount + $fee) {
            return back()->with(['error' => ['You cannot send Tether because your pending USDT balance is less than the amount to send.']]);
        }

        try {

            $now = new DateTime();

            DB::table('trade_transactions')->where('id', $id)->update([
                'state'             => 'close',
                'ended_at'          => $now,
            ]);

            $subtractAmount = $amount + $fee;
            $usdtBalance -= $subtractAmount;
            $usdtPending -= $subtractAmount;

            $authUser->usdtPending = $usdtPending;
            if ($authUser->usdtPending < 0) {
                $authUser->usdtPending = 0;
            }
            $authUser->usdtBalance = $usdtBalance;
            if ($authUser->usdtBalance < 0) {
                $authUser->usdtBalance = 0;
            }

            if ($authUser->totalSellCount == null) {
                $authUser->totalSellCount = 0;
                $authUser->totalSellAmount = 0;
            }
            $authUser->totalSellCount++;
            $authUser->totalSellAmount += $amount;
            $authUser->save();

            if ($buyerUser->totalBuyCount == null) {
                $buyerUser->totalBuyCount = 0;
                $buyerUser->totalBuyAmount = 0;
            }
            if ($buyerUser->usdtBalance == null) {
                $buyerUser->usdtBalance = 0;
            }
            $buyerUser->usdtBalance += $amount;
            $buyerUser->totalBuyCount++;
            $buyerUser->totalBuyAmount += $amount;
            $buyerUser->save();

            $linkUrl = App::make('url')->to('/user/mytrade/view' . $item->id);

            //Send email
            $emailParam = [
                'subject'       => 'Trade Complete.  Seller sent USDT to your wallet.',
                'message'       => $authUser->username . ' sent ".$amount." USDT to your wallet. ' . PHP_EOL . $linkUrl,
                'username'      => $buyerUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $buyerUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.send error:" . $e->getMessage());
                return back()->with(['error' => ['Failed to send email.']]);
            }


            return back()->with(['success' => ['USDT sent successfully. Trade completed.']]);
        } catch (Exception $e) {
            Log::info("MyTradeController.send error:" . $e->getMessage());
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
    }

    /**
     * 
     * 거래 상세 -> 취소 하기
     */
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itemId'              => 'required',
            'cancelReason'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required fields.']);
        }

        $validated = $validator->validate();
        $id = urlSafeDecrypt($validated['itemId']);

        $item = TradeTransaction::with('offer_user')->with('client_user')->findOrFail($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Unable to find this transaction item. Please try again.']);
        }

        if ($item->state != 'open') {
            Log::info(__('This trade has already finished.'));
            // Log::info($item);
            return response()->json(['success' => false, 'message' => __('This trade has already finished.')]);
        }

        // 취소 버튼 누른 유저 
        $authUser = Auth::User();

        if($item->order_type === 'buy'){ // 71  판매자가 clinet   
            $pendingUser = User::where('id',$item->client_user_id)->first();
            $otherUser = User::where('id',$item->offer_user_id)->first();
        } else if($item->order_type === 'sell') { // 게시글 올린 사람 펜딩  65  구매자가 clinet
            $pendingUser = User::where('id',$item->offer_user_id)->first();
            $otherUser = User::where('id',$item->client_user_id)->first();
        }

        $usdtPending = 0;
        if ($pendingUser->usdtPending != null) {
            $usdtPending = $pendingUser->usdtPending;
        }

        try {
            $reason = $validated['cancelReason'];
            $now = new DateTime();
 
            // 취소 클릭 유저 취소 카운트 + 1 
            $authUser->totalCancelCount = $authUser->totalCancelCount + 1;
            $authUser->save();
            
            // 기존 펜딩 유저가 가지고 있던 펜딩 양에서 취소 거래 펜딩 양 제거 
            $cancelPendingAmount =  $item->tetherAmount + $item->fee;
            $pendingUser->usdtPending = $usdtPending - $cancelPendingAmount;
            $pendingUser->save();

            $item->state = 'cancel';
            $item->ended_at = $now;
            $item->cancelReason = $reason;
            $item->save();

            $notification_content = [
                'title'   => __('거래 취소됨'),
                'message' => __(':nickname님이 거래를 취소했습니다.', [
                    'nickname'   => $authUser->username,
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            
            // Log::info('TRADE_CANCELLED first '.$pendingUser->id);

            UserNotification::create([
                'type'      => NotificationConst::TRADE_CANCELLED,
                'user_id'  =>  $pendingUser->id,
                'message'   => $notification_content,
            ]);

            // Log::info('TRADE_CANCELLED two '.$authUser->id);

            UserNotification::create([
                'type'      => NotificationConst::TRADE_CANCELLED,
                'user_id'  =>  $otherUser->id,
                'message'   => $notification_content,
            ]);

            // 링크 제거 
            // $linkUrl = App::make('url')->to('/user/mytrade/view/' . urlSafeEncrypt($item->id));

            //Send email
            $emailParam = [
                'subject'       => __('거래 취소됨'),
                'message'       => $authUser->username . "님이 거래를 취소했습니다." . PHP_EOL . "이유: " . $reason,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $otherUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            //Send email
            $emailParam = [
                'subject'       => __('거래 취소됨'),
                'message'       => $authUser->username . "님이 거래를 취소했습니다." . PHP_EOL . "이유:" . $reason,
                'username'      => $authUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $authUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }
        } catch (Exception $e) {
            Log::info("MyTradeController.cancel error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }

        return response()->json(['success' => true, 'message' => 'Request canceled successfully.']);
    }

    /**
     * 거래 상세에서 취소 버튼
     */
    public function cancel2(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'itemId'              => 'required',
            'cancelReason'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required fields.']);
        }

        $validated = $validator->validate();
        $id = urlSafeDecrypt($validated['itemId']);

        $item = TradeTransaction::with('offer_user')->with('client_user')->findOrFail($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Unable to find this transaction item. Please try again.']);
        }

        if ($item->state != 'open') {
            Log::info(__('This trade has already finished.'));
            // Log::info($item);
            return response()->json(['success' => false, 'message' => __('This trade has already finished.')]);
        }

        $amount = $item->tetherAmount;
        $fee = $item->fee;

        $authUser = Auth::User();
        $sellerUser = $authUser->id === $item->offer_user_id ? $authUser : User::where('id', $item->offer_user_id)->first();
        $buyerUser = $authUser->id === $item->client_user_id ? $authUser : User::where('id', $item->client_user_id)->first();
        $otherUser = $authUser->id == $sellerUser->id ? $buyerUser : $sellerUser;

        Log::info("sellerUser" . $sellerUser);
        Log::info("buyerUser" . $buyerUser);
        Log::info("otherUser:" . $otherUser);

        $usdtPending = 0;
        if ($sellerUser->usdtPending != null) {
            $usdtPending = $sellerUser->usdtPending;
        }

        try {
            $reason = $validated['cancelReason'];
            $now = new DateTime();

            $item->state = 'cancel';
            $item->ended_at = $now;
            $item->cancelReason = $reason;
            $item->save();

            $subtractAmount = $amount + $fee;
            $sellerUser->usdtPending = $usdtPending - $subtractAmount;

            if ($authUser->id === $sellerUser->id) {
                if ($sellerUser->totalCancelCount == null) {
                    $sellerUser->totalCancelCount = 0;
                }
                $sellerUser->totalCancelCount++;
            } else {

                if ($authUser->totalCancelCount == null) {
                    $authUser->totalCancelCount = 0;
                }
                $authUser->totalCancelCount++;

                $authUser->save();
            }

            $sellerUser->save();

            $notification_content = [
                'title'   => __('거래 취소됨'),
                'message' => __(':nickname님이 거래를 취소했습니다.', [
                    'nickname'   => $authUser->username,
                ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'      => NotificationConst::TRADE_CANCELLED,
                'user_id'  =>  $otherUser->id,
                'message'   => $notification_content,
            ]);

            UserNotification::create([
                'type'      => NotificationConst::TRADE_CANCELLED,
                'user_id'  =>  $authUser->id,
                'message'   => $notification_content,
            ]);

            // 링크 제거 
            // $linkUrl = App::make('url')->to('/user/mytrade/view/' . urlSafeEncrypt($item->id));

            //Send email
            $emailParam = [
                'subject'       => __('거래 취소됨'),
                'message' => __(':nickname님이 거래를 취소했습니다. :reason', [
                    'nickname'   => $authUser->username, 'reason' => $reason
                ]),
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $otherUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            //Send email
            $emailParam = [
                'subject'       => __('거래 취소됨'),
                'message'       => $authUser->username . "님이 거래를 취소했습니다." . PHP_EOL . "이유:" . $reason,
                'username'      => $authUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $authUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }
        } catch (Exception $e) {
            Log::info("MyTradeController.cancel error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }

        return response()->json(['success' => true, 'message' => 'Request canceled successfully.']);
    }

    public function checkPayProof($id)
    {
        try {
            $id = urlSafeDecrypt($id);
            $item = TradeTransaction::findOrFail($id);

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Transaction not found.']);
            }

            if ($item->state === 'send') {
                return response()->json([
                    'success' => true,
                    'pay_proof' => $item->payProof
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Pay proof not yet uploaded.']);
            }
        } catch (\Exception $e) {
            Log::error("Error in checkPayProof: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while checking pay proof.']);
        }
    }

    public function transactionFinished(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'           => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required sections.']);
        }

        $validated = $request->all();

        $itemId = urlSafeDecrypt($validated['item_id']);
        // Log::info("MyTradeController.transactionFinished itemId:" . $itemId);

        try {
            TradeTransaction::where('id', $itemId)->update([
                'state' => 'done',
            ]);

            // $notification_content = [
            //     'title'   => __('첨부파일 업로드됨'),
            //     'message' => __(
            //         ':nickname님이 새로운 첨부파일을 업로드했습니다.',
            //         ['nickname' => auth()->user()->realname ?? auth()->user()->username]
            //     ),
            //     'time'    => Carbon::now()->diffForHumans(),
            //     'image'   => files_asset_path('profile-default'),
            // ];

            // UserNotification::create([
            //     'type'          => NotificationConst::ATTACHMENT_UPLOADED,
            //     'sender_id'       => Auth::id(),
            //     'user_id'       => $request->input('receiver_id'),
            //     'message'       => $notification_content,
            // ]);
        } catch (Exception $e) {
            Log::info("MyTradeController.transactionFinished error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to finish transaction.']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction successfully finished.'
        ]);
    }

    /**
     * 분쟁 버튼 
     */
    public function dispute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itemId'              => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please fill in the required fields.']);
        }

        $validated = $validator->validate();
        $id = urlSafeDecrypt($validated['itemId']);

        $authUser = auth()->user();
        $item = TradeTransaction::findOrFail($id);
        $isBuyer = $item->buyer_user_id == auth()->user()->id;
        $otherUser = $isBuyer ? User::where('id', $item->offer_user_id)->first() : User::where('id', $item->client_user_id)->first();

        try {
            $item->state = 'dispute';
            $item->save();

            $notification_content = [
                'title'   => __('분쟁 시작됨'),
                'message' => __(
                    '진행중인 거래에서 :nickname님과 분쟁을 시작하였습니다.',
                    ['nickname' => auth()->user()->realname ?? auth()->user()->username]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'      => NotificationConst::DISPUTE_STARTED,
                'user_id'  =>  $otherUser->id,
                'message'   => $notification_content,
            ]);

            UserNotification::create([
                'type'      => NotificationConst::DISPUTE_STARTED,
                'user_id'  =>  $authUser->id,
                'message'   => $notification_content,
            ]);

            // 링크 제거
            // $linkUrl = App::make('url')->to('/user/mytrade/view/' . urlSafeEncrypt($item->id));

            //Send email
            $emailParam = [
                'subject'       => __('분쟁 시작됨'),
                'message'       => __(
                    '진행중인 거래에서 :nickname님과 분쟁을 시작하였습니다.',
                    ['nickname' => $authUser->username]
                ),
                'username'      => $otherUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $otherUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            //Send email
            $emailParam = [
                'subject'       => __('분쟁 시작됨'),
                'message'       => __(
                    '진행중인 거래에서 :nickname님과 분쟁을 시작하였습니다.',
                    ['nickname' => $authUser->username]
                ),
                'username'      => $authUser->username,
                'user_id'       => $authUser->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $authUser->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }
        } catch (Exception $e) {
            Log::info("MyTradeController.dispute error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to dispute.']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dispute successfully.'
        ]);
    }

    /**
     * 거래 상태 체크 
     */
    public function stateCheck($id){
        try {
            $id = urlSafeDecrypt($id);
            $item = TradeTransaction::findOrFail($id);

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Transaction not found.']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction status',
                'state' => $item->state
            ]);
        }catch (Exception $e) {
            Log::info("'Failed to state check Error:" . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to state check.']);
        }
    }
}
