<?php

namespace App\Http\Controllers\User;

use DateTime;
use DateTimeZone;
use Exception;

use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Models\InternalTransfer;
use App\Models\Mongo\Transfer;
use App\Models\Mongo\Tx;
use App\Models\Mongo\Wallet;
use App\Models\TradeTransaction;
use App\Models\User;
use App\Models\UserMailLog;
use App\Models\UserNotification;
use App\Notifications\User\SendMail;
use App\Providers\Admin\BasicSettingsProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ExternalTransfer;
use App\Models\ReferralPartner;
use Illuminate\Support\Facades\Cache;

class WalletController extends Controller
{
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    public static function callAPI($method, $url, $data = [], $header = [])
    {
        $headers['Content-Type'] = 'application/json';

        return Http::withHeaders($header ?: [])
            ->$method($url, $data ?: [])
            ->body();
    }

    public static function syncTransactions($wallet)
    {
        try {
            $result = self::callAPI("POST", env('SYNC_WALLET_API_URL'), ["address" => $wallet]);
            $resultData = json_decode($result, true);

            Log::info("WalletController:syncTransactions:" . json_encode($resultData));
            return $resultData;
        } catch (\Exception $e) {
            Log::info("syncTransactions error:" . $e->getMessage());
        }
    }

    public static function getBalance($address, $privateKey)
    {
        try {
            $result = self::callAPI("POST", env('GET_TETHER_BALANCE_API_URL'), ["address" => $address, "privateKey" =>$privateKey]);
            $resultData = json_decode($result, true);

            // Log::info("WalletController:getBalance:" . json_encode($resultData));
            return $resultData;
        } catch (\Exception $e) {
            Log::info("getBalance error:" . $e->getMessage());
        }
    }

    public static function getMasterWalletBalance()
    {
        try {
            $wallet = new Wallet();
            $masterWallet = $wallet->where('userId', 'master-wallet')->first();

            $result = self::getBalance($masterWallet->address, $masterWallet->privateKey);
            
            // Log::info("WalletController:getMasterWalletBalance:" . $usdt);
            return $result;
        } catch (\Exception $e) {
            Log::info("getBalance error:" . $e->getMessage());
        }
    }

    public static function getPrices()
    {
        $api_key = env('COIN_MARKET_CAP_API_KEY');    //coin marketcap api key
        $url = env('COIN_MARKET_CAP_API_URL');

        $headers = [
            "X-CMC_PRO_API_KEY" => $api_key,
            "Accept" => "application/json"
        ];

        $params = [
            "convert" => 'KRW',
            "CMC_PRO_API_KEY" => $api_key,
            "symbol" => 'USDT',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(3)
                ->retry(3, 100)
                ->get($url, $params);

            $responseData = $response->json();

            $KRWPrice = 1350; // 기본값

            if ($response->successful()) {
                $KRWPrice = intval($responseData['data']['USDT']['quote']['KRW']['price']);
            } else {
                Log::warning("WalletController.getPrices API request failed: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("WalletController.getPrices error: " . $e->getMessage());
            $KRWPrice = 1350; // 에러 발생 시 기본값 사용
        }

        return [
            'KRW' => $KRWPrice,
        ];
    }

    public static function getUsdtPending($user_id)
    {
        $pendingAmount = TradeTransaction::where('order_type', 'sell')
            ->where('offer_user_id', $user_id)
            ->where('state', 'open')
            ->sum(DB::raw('tetherAmount + fee'));

        return $pendingAmount;
    }

    /**
     * Display a listing of the resource.
     * 내 지갑
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Wallet";
        $user = auth()->user();

        // $usdtPending = self::getUsdtPending($user->id);
        // $user->usdtPending = $usdtPending;
        // $user->save();

        $usdtPending  = $user->usdtPending;

       $usdtBalance = 0;
       if ($user->usdtBalance != null) {
           $usdtBalance = $user->usdtBalance;
       }

        $trxBalance = 0;
        $prices = WalletController::getPrices();

        $krwBalance = number_format($usdtBalance * $prices['KRW']);
        $limit = 100;
        $page = 1;
        $perPage = 10;

        $now = new DateTime();
        $curTimeZone = new DateTimeZone(Auth::User()->lastLoginTimeZone ?: 'Asia/Seoul');

        $transfer = new Transfer();
        $tx = new Tx();
        $internalTransfer = new InternalTransfer();

        $transfers = $transfer->where('sender', $user->walletAddress)
            ->select('created_at', 'amount', 'txid')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item['type'] = __('출금');
                return $item;
            });
       
        $deposits = $tx->where('to', $user->walletAddress)
            ->select('timestamp', 'amount', 'txid')
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item['created_at'] = $this->convertTimestampToDateTime($item->timestamp);
                $item['type'] = __('입금');
                return $item;
            });


        $internalSend = $internalTransfer->where('sender_email', $user->email)
            ->select('created_at', 'amount', 'receiver_email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item['txid'] = $item->receiver_email;
                $item['type'] = __('출금');
                return $item;
            });

        $internalReceive = $internalTransfer->where('receiver_email', $user->email)
            ->select('created_at', 'amount', 'sender_email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item['txid'] = $item->sender_email;
                $item['type'] = __('입금');
                return $item;
            });

        $combined = collect()
            ->concat($transfers)
            ->concat($deposits)
            ->concat($internalSend)
            ->concat($internalReceive)
            ->sortByDesc('created_at');

        $total = $combined->count();
        $items = $combined->forPage($page, $perPage);

        $listData = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('user.wallet.index', compact("page_title", "user", 'usdtBalance', 'usdtPending', 'trxBalance', 'listData', 'now', 'curTimeZone', 'krwBalance', 'prices'));
    }

    private function convertTimestampToDateTime($timestamp)
    {
        // timestamp가 밀리초 단위일 경우
        if (strlen($timestamp) === 13) {
            return Carbon::createFromTimestampMs($timestamp);
        }
        // timestamp가 초 단위일 경우
        return Carbon::createFromTimestamp($timestamp);
    }
    
    /**
     * 내부 테더 전송(구매하기,판매하기)
     */
    public function transferUSDTInternally(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->merge($data);

        $validated = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'amounts' => 'required|numeric|min:0.000001',
            'twoFactorCode' => 'nullable|string',
        ])->validate();

        $sender = auth()->user();
        $receiver = User::where('email', $validated['email'])->first();
        
        // 중복 호출 방지
        $lockKey = "transferUSDTInternally_".$sender->id.$receiver->id;
        if (!Cache::add($lockKey, true, 10)) {
            return response()->json(['success' => false,'message' => '이미 처리 중입니다. 잠시 후 다시 시도해주세요.',], 400);
        }

        if (!$receiver) {
            return response()->json(['success' => false, 'message' => 'Recipient user not found.'], 404);
        }

        if ($sender->email === $receiver->email) {
            return response()->json(['success' => false, 'message' => 'You cannot transfer to yourself.'], 400);
        }

        // 2FA 확인이 필요한 경우
        if (isset($validated['twoFactorCode']) && $sender->two_factor_tether_transfer) {
            // Google 2FA 코드 확인
            if (!google_2fa_verify($sender->two_factor_secret, $validated['twoFactorCode'])) {
                return response()->json(['success' => false, 'message' => 'Invalid two-factor authentication code.']);
            }
        }

        $amount = floatval($validated['amounts']);
        
        // $fee = getUSDTFee($sender , $amount);


        Log::info('전송 사람 / 받는사람  '.$sender->id."////".$receiver->id);
        // 기본 수수료 설정
        $rate = 0.8;
        
        $referralFeeInfo = ReferralPartner::where('user_id', $sender->id)->first();  
        $referral_fee = 0;
        Log::info('referralFeeInfo = '.$referralFeeInfo);

        if($referralFeeInfo){
    
            if($referralFeeInfo->group_id == 0) { //레퍼럴 에이전트 일때 수수료 할인만 적용
                $rate = $referralFeeInfo->fee_rate;
            } else { // 레퍼럴 일반 파트너 일때
                 //소속 에이전트 조회
                $agentInfo =  ReferralPartner::where('id', $referralFeeInfo->group_id)->first();
                Log::info('agentInfo = '.$agentInfo);
                // 파트너의 수수료 
                $rate =  $referralFeeInfo->fee_rate;
                Log::info('rate = '.$rate);
                // 레퍼럴 이익 =  파트너 수수료 - 에이전트 수수료 
                $referral_fee = $amount * (($rate - $agentInfo->fee_rate ) / 100); 

                Log::info('referral_fee = '.$referral_fee);
            }
              // 파트너 혹은 에이전트 유저 id
              $referral_user_id = $sender->id;
              Log::info('referral_user_id = '.$referral_user_id);
        } 

        $fee =  $amount * ($rate / 100);        

        try {  
            DB::beginTransaction();

            // 송신자 잔액 차감
            $sender->usdtBalance -= $amount + $fee;
            if (!isset($validated['twoFactorCode'])) { // 거래시 전송
                $sender->usdtPending -= $amount + $fee;
            }
            // 해당 유저 총 판매액 증가
            $sender->totalSellAmount += $amount + $fee;
            $sender->save();

            // 수신자 잔액 증가
            $receiver->usdtBalance += $amount;
            // 해당 유저 총 구매액 증가 
            $receiver->totalBuyAmount += $amount;
            $receiver->save();

            // 전송 내역 기록
            InternalTransfer::create([
                'sender_email'      =>  $sender->email,
                'receiver_email'    =>  $receiver->email,
                'amount'            =>  $amount,
                'anaki_fee'         =>  ($referral_fee != 0)? $fee - $referral_fee : $fee,
                'referral_fee'      =>  $referral_fee,
                'referral_user_id'  =>  ($referral_user_id)??0
            ]);

            if(isset($agentInfo)){
                // 에이전트 아래 파트너에 거래가 일어난 경우 수수료만큼 에이전트 잔액 증가 
                $agentUserInfo  = User::where('id',$agentInfo->user_id)->first();
                $agentUserInfo->usdtBalance += $referral_fee;
                $agentUserInfo->save();
                Log::info('agentUserInfo->usdtBalance = '.$agentUserInfo->usdtBalance);
                //  에이전트 수수료 총합 증가
                $agentInfo->referral_benefit_total += $referral_fee;
                $agentInfo->save(); 
                // 파트너에 referral_benefit 증가
                $referralFeeInfo->referral_benefit += $referral_fee;
                $referralFeeInfo->save();
                Log::info('agentInfo->referral_benefit = '.$agentInfo->referral_benefit);
            }

            DB::commit();

            $itemId = $request->input('itemId');
            if ($itemId) {
                $transaction = TradeTransaction::find(urlSafeDecrypt($itemId));
                $transaction->isTransferred = true;
                $transaction->state = 'done';
                $transaction->save();
            }

            $notification_content = [
                'title'   => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 판매했습니다.',
                    ['nickname' => $sender->username ?? $sender->realname, 'amount' => $amount]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_SENT,
                'sender_id'       => Auth::id(),
                'user_id'       => $sender->id,
                'message'       => $notification_content,
            ]);

            $notification_content = [
                'title'   => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 구매했습니다.',
                    ['nickname' => $receiver->username ?? $receiver->realname, 'amount' => $amount]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_RECEIVED,
                'sender_id'       => Auth::id(),
                'user_id'       => $receiver->id,
                'message'       => $notification_content,
            ]);    

            $emailParam = [
                'subject'       => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 판매했습니다.',
                    ['nickname' => $sender->username ?? $sender->realname, 'amount' => $amount]
                ),
                'username'      => $receiver->username,
                'user_id'       => $sender->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $receiver->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            $emailParam = [
                'subject' => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 구매했습니다.',
                    ['nickname' => $receiver->username ?? $receiver->realname, 'amount' => $amount]
                ),
                'username'      => $sender->username,
                'user_id'       => $receiver->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $sender->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }
    
            return response()->json(['success' => true, 'message' => 'USDT transferred successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Internal USDT transfer error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during the transfer. Please try again.'], 500);
        }
    }

    /**
     * 내지갑 내부 테더 전송
     */
    public function transferUSDTInternallyWalletToWallet(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->merge($data);

        $validated = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'amounts' => 'required|numeric|min:0.000001',
            'twoFactorCode' => 'nullable|string',
        ])->validate();

        $sender = auth()->user();
        $receiver = User::where('email', $validated['email'])->first();
        
         // 중복 호출 방지
         $lockKey = "transferUSDTInternallyWalletToWallet_".$sender->id.$receiver->id;
         if (!Cache::add($lockKey, true, 10)) {
             return response()->json(['success' => false,'message' => '이미 처리 중입니다. 잠시 후 다시 시도해주세요.',], 400);
         }

        if (!$receiver) {
            return response()->json(['success' => false, 'message' => 'Recipient user not found.'], 404);
        }

        if ($sender->email === $receiver->email) {
            return response()->json(['success' => false, 'message' => 'You cannot transfer to yourself.'], 400);
        }

        // 2FA 확인이 필요한 경우
        if (isset($validated['twoFactorCode']) && $sender->two_factor_tether_transfer) {
            // Google 2FA 코드 확인
            if (!google_2fa_verify($sender->two_factor_secret, $validated['twoFactorCode'])) {
                return response()->json(['success' => false, 'message' => 'Invalid two-factor authentication code.']);
            }
        }

        $amount = floatval($validated['amounts']);
        
        $withdrawableUSDTBalance = $sender->usdtBalance - $sender->usdtPending;
        if ($amount > $withdrawableUSDTBalance) {
            return response()->json(['success' => false, 'message' => 'Insufficient USDT balance.'], 400);
        }

        try {
            DB::beginTransaction();
            
            // 전송 내역 기록
            InternalTransfer::create([
                'sender_email'          => $sender->email,
                'receiver_email'        => $receiver->email,
                'amount'                => $amount,
                'anaki_fee'             =>  0,  // 지갑에서 지갑으로 수수료 0원
                'transfer_type'         =>  2   // 지갑간 거래 = 2 , 내부 전송거래 = 1
            ]);

            // 송신자 잔액 차감
            $sender->usdtBalance -= $amount;
            $sender->save();

            // 수신자 잔액 증가
            $receiver->usdtBalance += $amount;
            $receiver->save();

            DB::commit();

            $itemId = $request->input('itemId');
            if ($itemId) {
                $transaction = TradeTransaction::find(urlSafeDecrypt($itemId));
                $transaction->isTransferred = true;
                $transaction->save();
            }

            $notification_content = [
                'title'   => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 판매했습니다.',
                    ['nickname' => $sender->username ?? $sender->realname, 'amount' => $amount]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_RECEIVED,
                'sender_id'       => Auth::id(),
                'user_id'       => $receiver->id,
                'message'       => $notification_content,
            ]);

            $notification_content = [
                'title'   => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 구매했습니다.',
                    ['nickname' => $receiver->username ?? $receiver->realname, 'amount' => $amount]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_SENT,
                'sender_id'       => Auth::id(),
                'user_id'       => $sender->id,
                'message'       => $notification_content,
            ]);

            $emailParam = [
                'subject'       => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 판매했습니다.',
                    ['nickname' => $sender->username ?? $sender->realname, 'amount' => $amount]
                ),
                'username'      => $receiver->username,
                'user_id'       => $sender->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $receiver->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            $emailParam = [
                'subject' => __('거래 완료됨'),
                'message' => __(
                    '성공! :nickname님이 :amount USDT를 구매했습니다.',
                    ['nickname' => $receiver->username ?? $receiver->realname, 'amount' => $amount]
                ),
                'username'      => $sender->username,
                'user_id'       => $receiver->id,
                'method'        => "SMTP",
            ];

            try {
                UserMailLog::create($emailParam);
                $sender->notify(new SendMail((object) $emailParam));
            } catch (Exception $e) {
                Log::info("MyTradeController.cancel error:" . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email.']);
            }

            return response()->json(['success' => true, 'message' => 'USDT transferred successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Internal USDT transfer error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during the transfer. Please try again.'], 500);
        }
    }

    /**
     * 내 지갑 -> 외부 전송
     */    
    public function sendUSDT(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->merge($data);

        $validated = Validator::make($request->all(), [
            'walletAddr'    => 'required|string|max:60',
            'amounts'       => 'required',
            'networkFee'    => 'required|int',
            'twoFactorCode' => 'required_if:two_factor_tether_transfer,true|nullable|string',
        ])->validate();
        
        Log::info('내 지갑 외부전송 data:');
        Log::info($validated);
       
        $user = auth()->user();
        
        // 중복 호출 방지
        $lockKey = "sendUSDT_".$user->id."to".$validated['walletAddr'];
        if (!Cache::add($lockKey, true, 10)) {
            return response()->json(['success' => false,'message' => '이미 처리 중입니다. 잠시 후 다시 시도해주세요.',], 400);
        }
            
        // 2FA 확인이 필요한 경우
        if ($user->two_factor_tether_transfer) {
            if (!isset($validated['twoFactorCode'])) {
                return response()->json(['success' => false, 'message' => 'Two-factor authentication code is required.']);
            }

            // Google 2FA 코드 확인
            if (!google_2fa_verify($user->two_factor_secret, $validated['twoFactorCode'])) {
                return response()->json(['success' => false, 'message' => 'Invalid two-factor authentication code.']);
            }
        }

        $amountText = $validated['amounts'];

        $amounts_float = floatval($amountText);
        if ($amountText != (string)(float)$amountText) {
            return response()->json(['success' => false, 'message' => 'Invalid amount value.']);
        }

        // 네트워크 피로 사용
        $networkFee =  $validated['networkFee'];

        $usdtBalance = 0;
        $usdtPending = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance = $user->usdtBalance;
        }
        if ($user->usdtPending != null) {
            $usdtPending = $user->usdtPending;
        }

        $withdrawableUSDTBalance = $usdtBalance - $usdtPending;
        if ($amounts_float > $withdrawableUSDTBalance) {
            return response()->json(['success' => false, 'message' => 'You don\'t have enough USDT balance.']);
        }

        // 실제 전송량 = 보낸수량 - 네트워크 피
        $realSendAmount = $amountText - $networkFee;
      
        try {
            $wallet = new Wallet();

            $masterWallet = $wallet->where('userId', 'master-wallet')->first();
            
            $transfer = new Transfer();
            $transfer->sender = $user->walletAddress;
            $transfer->from = $masterWallet->address;
            $transfer->to = $validated['walletAddr'];
            $transfer->amount = $realSendAmount;
            $transfer->immediate = true;
            $transfer->uid = $user->id;
            $transfer->created_at = new DateTime();
            $transfer->type = 'USDT';
            $transfer->save();
            
            $user->usdtBalance -= $amountText;
            $user->save();
            
            // transfer 저장 ID 값
            $transferId = $transfer->_id;
            
            
            // 외부 전송 내역 저장
            ExternalTransfer::create([
                'user_id'           => $user->id,
                'amount'            => $amountText,
                'network_fee'       => $networkFee,
                'transfer_type'     => 1,  // 전송은 1
                'to'                => $validated['walletAddr']
            ]); 
           
            // 테더 외부전송 API호출 마스터-> 외부 지갑 
            $this->sendTetherToExternal(
                masterWallet: $masterWallet,
                receiverAddress: $validated['walletAddr'],
                amount: $realSendAmount,
                transferId: $transferId
            );

            $notification_content = [
                'title'   => __('테더 전송완료'),
                'message' => __(':send_amount USDT 전송 완료되었습니다.', ['send_amount' => $realSendAmount]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_SENT,
                'sender_id'       => Auth::id(),
                'user_id'       => $user->id,
                'message'       => $notification_content,
            ]);
            return response()->json(['success' => true, 'message' => 'USDT sent successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function checkRecentDeposit()
    {
        try {
            $user = auth()->user();
            $receiverEmail = $user->email;

            $recentTransfer = InternalTransfer::where('receiver_email', $receiverEmail)
                ->where('created_at', '>', Carbon::now()->subMinute())
                ->latest()
                ->first();

            if ($recentTransfer) {
                $sender = User::where("email", $recentTransfer->sender_email)->first();

                return [
                    'success' => true,
                    'sender_realname' => $sender->realname ?? $sender->username,
                    'sender_email' => $recentTransfer->sender_email,
                    'amount' => $recentTransfer->amount,
                    'created_at' => $recentTransfer->created_at
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No matching deposit found in the last 1 minutes'
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error checking recent deposits: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error occurred while checking deposits'
            ];
        }
    }

    /**
     * qr코드 리더 페이지로 이동
     */
    public function moveQrCodeReaderForm(){
        $page_title = 'qrcord-Reader';
        return view('user.wallet.qrcode-reader',compact("page_title"));
    }

    /**
     * 노드js에서 호출 API
     * 유저 db에 테더량 수정
     */
    public function checkUserDbTetheAmount(Request $request){
        Log::info("checkUserDbTetheAmount " . $request->all());
        try{
            $userInfo = User::where('walletAddress',$request->address)->first();
            $userInfo->usdtBalance = $userInfo->usdtBalance +  $request->newUsdtDeposit;
            $userInfo->save();

            $notification_content = [
                'title'   => __('테더 입금완료'),
                'message' => __(':new_deposit USDT 확인 완료되어 잔액에 추가되었습니다.', ['new_deposit' => $request->newUsdtDeposit ]),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::USDT_DEPOSIT_COMPLETED,
                'sender_id'       => Auth::id(),
                'user_id'       => $userInfo->id,
                'message'       => $notification_content,
            ]);
            $result = 1;
        } catch (\Exception $e) {
            $result =0;
            Log::error("Error checkUserDbTetheAmount: " . $e->getMessage());
        }
        return $result;
    }

    // 내지갑 테더 외부 전송 코인 API 호출
    public function sendTetherToExternal(Wallet $masterWallet,string $receiverAddress, float $amount,string $transferId){
        Log::info("sendTetherToExternal params= ". $masterWallet->address."/////". $receiverAddress."////".$amount);
        try {
            $result = self::callAPI("POST", env('SEND_TETHER_API_URL'), [
                "senderPrivateKey"  => $masterWallet->privateKey, 
                "senderAddress"     => $masterWallet->address,
                "receiverAddress"   => $receiverAddress,
                "amounts"           => $amount
            ]);
            $resultData = json_decode($result, true);
            Log::info("sendTetherToExternal - resultData: " . print_r($resultData, true));
            Log::info("sendTetherToExternal - txid: " . print_r( $resultData['result']['txid'], true));

            Transfer::where('_id', $transferId)->update([
                'txid' => $resultData['result']['txid']
            ]);
           
        } catch (\Exception $e) {
            Log::info("sendTetherToExternal error:" .$e->getMessage());
        }
    }

}
