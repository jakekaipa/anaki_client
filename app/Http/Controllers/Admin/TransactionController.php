<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminLoginLogs;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\Admin;
use App\Models\ReferralPartner;
use App\Models\InternalTransfer;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;


class TransactionController extends Controller
{
    public function __construct(TransactionRepository $transaction, UserRepository $user)
    {
        $this->transaction = $transaction;  
        $this->user        = $user; 
    }
    
    /**
     * 내부 거래내역 조회 
     */
    public function getInternalTransactionList(Request $request){
        $page = $request->input('page',1);
        $transactionCount = $this->transaction->getTransactionCount();
        
        $where ='1=1 ';
        $searchKey       = $request->input('searchKey','');
        $transactionType = $request->input('transactionType','');
        $searchType      = $request->input('searchType','all'); 
        $state           = $request->input('state','');
        $startDate       = $request->input('startDate','');
        $endDate         = $request->input('endDate','');
       
        if($searchKey){
            $where = "(offerUser.username LIKE '%" . $searchKey . "%' or offerUser.realname LIKE '%" . $searchKey . "%')";
            $where .= " or (clientUser.username LIKE '%" . $searchKey . "%' or clientUser.realname LIKE '%" . $searchKey . "%')";
        }

        if($transactionType){
             $where .=" and order_type='".$transactionType."'";
        }
    
        if($state){
            $where .=" and state='".$state."'";
        }

        if($startDate && $endDate){
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $where .=" and trade_transactions.created_at >= '".$startDate."' and trade_transactions.created_at <= '".$endDate."'";
        }

        $transactionList = $this->transaction->getTrasnactionList($where);

        // echo "<pre>";
        // print_r($transactionList->toArray());
        // echo "</pre>";
        // exit;

        return view('admin.transactionList',compact('transactionList','transactionCount','page','searchKey','searchType','transactionType','state','startDate','endDate'));
    }
    
    /**
     * 거래 내역 상세  ajax
     */
    public function view($id){
        $transactionInfo = $this->transaction->getTransactionInfo(urlSafeDecrypt($id));
        return response()->json($transactionInfo);
    }

    /**
     * 외부 전송 내역
     */
    public function getExternalTransactionList(Request $request){
        
        $page = $request->input('page',1);
        $where = '1=1';
        $searchKey       = $request->input('searchKey','');
        $startDate       = $request->input('startDate','');
        $endDate         = $request->input('endDate','');
        $transferType    = $request->input('transferType','');

        if($transferType){
            $where .=" and transfer_type=".$transferType ;
        }

        if($startDate && $endDate){
            $startDate = Carbon::parse($startDate)->startOfDay(); // 시작 날짜의 시작 시각 (00:00:00)
            $endDate = Carbon::parse($endDate)->endOfDay();
            $where .=" and created_at >= '".$startDate."' and created_at <= '".$endDate."'";
        }

        $externalTransactionList = $this->transaction->getExternalTransactionList(
            where: $where,
            name: $searchKey
        );
        $externalTransactionCount = $this->transaction->getExternalTransactionCount(); 

       
        return view('admin.externalTransactionList',compact('externalTransactionList','externalTransactionCount','page','searchKey','transferType','startDate','endDate'));
    }

    /**
     * 지갑간 전송 내역
     */
    public function getTransferWalletList(Request $request){
        $page = $request->input('page',1);
        $where = 'transfer_type = 2 ';
        $searchKey       = $request->input('searchKey','');
        $searchType      = $request->input('searchType','');
        $startDate       = $request->input('startDate','');
        $endDate         = $request->input('endDate','');

        if($startDate && $endDate){
            $startDate = Carbon::parse($startDate)->startOfDay(); 
            $endDate = Carbon::parse($endDate)->endOfDay();
            $where .= " and internal_transfers.created_at >= '".$startDate."' and internal_transfers.created_at <= '".$endDate."'";
        }
        
        if($searchKey){
            $where .= " and (";
            $where .= "(senderUser.username LIKE '%" . $searchKey . "%' or senderUser.realname LIKE '%" . $searchKey . "%')";
            $where .= " or (recevierUser.username LIKE '%" . $searchKey . "%' or recevierUser.realname LIKE '%" . $searchKey . "%')";
            $where .= ")";
        }
        
        $walletTransferList = $this->transaction->getTransferWalletList($where);
        $walletTransferCount = $this->transaction->getTransferWalletCount();
        // echo "<pre>";
        // print_r($walletTransferList->toArray());
        // echo "</pre>";
        // exit;

        return view('admin.transferWalletList',compact('walletTransferList','walletTransferCount','page','searchKey','searchType','startDate','endDate'));
    }


    /**
     * 거래 내역 상태 변경 option=1 (분쟁->분쟁 해결) , option=2 (분쟁->테더 전송 후 분쟁 해결)
     */
    public function changeTransactionState(Request $request){
        $result = 0;
        try {
            // 거래 내역 조회 
            $transactionInfo = $this->transaction->getTransactionInfo(urlSafeDecrypt($request->input('transactionId')));
            $sellerInfo = ($transactionInfo->order_type === 'buy')? $this->user->getUserInfo($transactionInfo->client_user_id) : $this->user->getUserInfo($transactionInfo->offer_user_id ) ;
            
            // 테더 전송 후 분쟁 해결  
            if($request->input('option') == 2){
                $buyerInfo = ($transactionInfo->order_type === 'buy')? $this->user->getUserInfo($transactionInfo->offer_user_id ) : $this->user->getUserInfo($transactionInfo->client_user_id ) ;
                $this->sendTetherFromDispute($sellerInfo,$buyerInfo,$transactionInfo->tetherAmount);
            } else { // 일반 분쟁 해결 -> 거래취소와 동일
                $updateParam['usdtPending'] = $sellerInfo->usdtPending - ($transactionInfo->tetherAmount + $transactionInfo->fee);
                 // 해당 유저 테더 펜딩량 제거 
                $this->user->updateUserInfo(
                    userId: $sellerInfo->id,
                    updateParam: $updateParam
                );
            }

            // 거래 상태 변경
            $this->transaction->changeTransactionState(
                transactionId: urlSafeDecrypt($request->input('transactionId')),
                state: $request->input('state')
            );
            
            $result = 1;
        } catch (Exception $e) {
            Log::info("insertComments exception:" . $e);
            $result = -1;
        }
        return $result;
    }

    // 분쟁 유저 테더 전송 후 분쟁 해결
    public function sendTetherFromDispute(User $sender,User $receiver,float $amount){
        // 판매 유저의 에이전트 파트너 여부 체크
        $referralFeeInfo = ReferralPartner::where('user_id', $sender->id)->first();  
        $referral_fee = 0;
        $rate = 0.8;
        Log::info('테더 전송후 분쟁 해결 전송한 사람 / 받는 사람  '.$sender->id."////".$receiver->id);
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

        // 송신자 잔액 차감
        $sender->usdtBalance -= $amount + $fee;
        $sender->usdtPending -= $amount + $fee;
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

    }
    
    /**
     * 거래 채팅 내역 조회
     */
    public function getChattingMessageList($id) {
        try{
             $result = $this->transaction->getChattingMessageList(urlSafeDecrypt($id));
        } catch (Exception $e) {
            Log::info("getChattingMessageList Error exception:" . $e);
            $result = -1;
        }
        return $result;
    }

}
