<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Mongo\Wallet;
use App\Models\Mongo\Tx;
use App\Models\Mongo\TrxTransfer;
use App\Models\TradeTransaction;
use App\Models\Mongo\Transfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Models\TradeOffer;
use App\Models\User;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramService;

class TestController extends Controller
{
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;  
    }

    public function test(){
    
        if(Auth::id() === 71 )
        {   
            $tx = new Tx();
            $wallet = new Wallet();
            $user  = new User();
            $trx = new TrxTransfer();
            $transfer = new Transfer();
            $tradeTransaction = new TradeTransaction();
            echo "<pre>"; 
            //TEkbZsedBzBqC2GtwZhDBeaVG6Y5oSUKqG
             print_r($wallet->get()->toArray());
            //print_r($transfer->get()->toArray());
            exit;
            // $result = self::callAPI("POST", "http://anaki_backend:3760/sendTrx", [
            //     "senderPrivateKey"  => '4014963be587bd6d3fe10d9424968547063002739bb8acccdec33fb5c04a37b6', 
            //     "senderAddress"     => 'TEkbZsedBzBqC2GtwZhDBeaVG6Y5oSUKqG',
            //     "receiverAddress"   => "TMXep77LEJCGxwP3oGLTu4AcAnYCY4Mbr9",
            //     "amounts"           => "16.5"
            // ]);
            // $resultData = json_decode($result, true);
            // print_r($resultData);
            // exit;

            // $result = self::callAPI("POST", "http://anaki_backend:3760/sendTether", [
            //     "senderPrivateKey"  => '', 
            //     "senderAddress"     => '',
            //     "receiverAddress"   => "",
            //     "amounts"           => ""
            // ]);
            // $resultData = json_decode($result, true);
            // print_r($resultData);  
            // exit;

            // 0d6adbe19cc0838a2a5cd3339b1f947ee4b61b44affd97ed1ba4db0ae281bcee
            //TMXep77LEJCGxwP3oGLTu4AcAnYCY4Mbr9
            echo "</pre>";
            exit;
        }else{
            echo "<script type='text/javascript'>alert('잘못된 접근입니다.');</script>";
            exit; 
        }

        $currentTime = now();  // 또는 Carbon::now()
        $page_title = '테스트';
        return view('user.test',compact("page_title","prices","tradeInfo","tradeOfferId"));
    }

    public function test2(Request $request){
        return view('user.sections.kyc.kyc-iframe');
    }
    
    public function test3(Request $request){
       
        // 로그에 수정된 데이터 출력
        Log::info("Iframe에서 넘겨 받은 KYC 인증 데이터: " . json_encode($request->all()));
     
        // API EndPoint URL
        $url = '';
        $result = self::callAPI("POST", $url, [
                "result_type"        => $request->input('result_type'), 
                "accountName"        => $request->input('accountName'), 
                "accountNumber"      => $request->input('accountNumber'), 
                "bankName"           => $request->input('bankName'), 
                "realname"           => $request->input('realname'), 
                "datas"              => $request->input('datas'), 
        ]);

        return  $result;

    }

    public static function callAPI($method, $url, $data = [], $header = [])
    {
        $headers['Content-Type'] = 'application/json'; 

        return Http::withHeaders($header ?: [])
            ->$method($url, $data ?: [])
            ->body();
    }

    function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
    }

    public function test4()
    {
        $groupName = "테스트그룹";

        // 개인 그룹 생성
        $groupResponse = $this->telegramService->createGroup($groupName);

        if (!isset($groupResponse['result']['id'])) {
            return response()->json(['error' => '그룹 생성 실패'], 500);
        }

        $groupChatId = $groupResponse['result']['id'];
        echo 'groupChatId='.$groupChatId;

        // 봇을 관리자 추가
        $this->telegramService->promoteBotToAdmin($groupChatId, env('TELEGRAM_BOT_NAME'));

        // 테스트 유저 (@cwne12) 추가
        $this->telegramService->addUserToGroup($groupChatId, "@cwne12");

        return response()->json([
            'message' => '테스트 그룹 생성 및 유저 추가 완료!',
            'group_chat_id' => $groupChatId
        ]);
    }
}