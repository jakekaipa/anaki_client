<?php

namespace App\Services;

use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminLoginLogs;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\Admin;
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
use App\Repositories\DataStatsRepository;
use Illuminate\Support\Facades\Http;

class DataStatsScheduleService 
{
    // 이전 날짜 통계 스케줄러 등록용
    public function __construct(
        TransactionRepository $transaction, 
        UserRepository $user,
        DataStatsRepository $dataStat
        )
    {
        $this->transaction = $transaction;  
        $this->user        = $user;
        $this->dataStat   = $dataStat;
        $this->address     = env('MASTER_WALLET_ADDRESS');
        $this->privateKey  = env('MASTER_WALLET_PRIVATEKEY');
        $this->yesterdayRange = [
            'start' => Carbon::yesterday()->startOfDay()->format('Y-m-d'),
            'end' => Carbon::yesterday()->endOfDay()->format('Y-m-d')
        ];
    }
    
    /**
     * 마스터 월렛에 Tether,TRX 량 조회 및 저장
     */
    public function saveCurrentWalletBalance()
    {
        try {
            $result = callAPI("POST", env('GET_TETHER_BALANCE_API_URL'), ["address" => $this->address, "privateKey" => $this->privateKey]);
            $resultData = json_decode($result, true);
            // Log::info("getCurrentWalletBalance: " . json_encode($resultData));
            // echo $resultData['result']['usdt']."/////".$resultData['result']['trx'];
            $insertParam['usdt_amount'] =  $resultData['result']['usdt'];
            $insertParam['trx_amount']  =  $resultData['result']['trx'];
            $insertParam['created_at']  =  $this->yesterdayRange['end'];
            $this->dataStat->insertGeneralData($insertParam);
           
        } catch (\Exception $e) {
            // Log::info("getCurrentWalletBalance error:" . $e->getMessage());
        }
    }

    /**
     * 전날 가입자수 조회 및 저장
     */
    public function saveRegistUserCount(){
        try {    
            // balance에서 인설트했으니 항목 업데이트
             $updateParam['register_amount']  =  $this->user->getRegistUserCount($this->yesterdayRange);
            //  Log::info("RegistUserCount: ".$updateParam['register_amount']);
             $this->dataStat->updateGeneralData(
                updateParam: $updateParam,
                createdAt: $this->yesterdayRange['end'] 
            );
        } catch (\Exception $e) {
            // Log::info("getRegistUserCount error:" . $e->getMessage());
        }
    }

     /**
     * 전날 총 거래 건수 (총 건수 , 거래완료,분쟁건수,분쟁해결,취소 건수) 조회 및 저장 
     */
    public function saveTotalInternalTransactionCounts(){
        try {    
            $totalTransactionCounts = $this->transaction->getTotalInternalTransactionCounts($this->yesterdayRange);
            $insertParam['trade_count']                 = $totalTransactionCounts->totalCount;
            $insertParam['completed_trade_count']       = $totalTransactionCounts->totalCompleteCount;
            $insertParam['dipute_trade_count']          = $totalTransactionCounts->totalDisputCount;
            $insertParam['dipute_solved_trade_count']   = $totalTransactionCounts->totalDisputeSolvedCount;
            $insertParam['cancel_trade_count']          = $totalTransactionCounts->totalCancelCount;
            $insertParam['created_at']                  = $this->yesterdayRange['end'];
            $this->dataStat->insertTradeStatData($insertParam);
            // Log::info("totalTransactionCounts: ".$insertParam);
        } catch (\Exception $e) {
            // Log::info("getTotalInternalTransactionCounts error:" . $e->getMessage());
        } 
    }

     /**
     * 전날 완료된 내부 거래 (거래금액,수수료,레퍼럴 수수료)합계 조회 및 저장
     */
    public function saveCompletedInternalTransactions(){
        try {    
            $completedTransactionAmount = $this->transaction->getCompletedInternalTransactions($this->yesterdayRange);
            $updateParam['trade_amount'] = $completedTransactionAmount->amount_sum;
            $updateParam['trade_fee'] = $completedTransactionAmount->anaki_fee_sum;
            $updateParam['referral_fee'] = $completedTransactionAmount->referral_fee_sum;
            $this->dataStat->updateTradeStatData( 
                updateParam: $updateParam,
                createdAt: $this->yesterdayRange['end']
            );
            // Log::info("completedTransactionAmount: ".$updateParam);
        } catch (\Exception $e) {
            // Log::info("getCompletedInternalTransactions error:" . $e->getMessage());
        } 
    }

}
