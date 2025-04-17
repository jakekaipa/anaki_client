<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\TradeTransaction;
use App\Models\User;
use DateTime;

class AutoCancelTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto cancel Transaction';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = new DateTime();
        $nowText = $now->format('Y-m-d H:i').':59';
        // Log::info('111');
        //Storage::append('logs/auto_cancel_transactions.txt', '1111');
        // 자동 종료 처리(거래 시작, 입금 증빙 업로드)
        $expiredTransactions = TradeTransaction::whereIn('state', ['open', 'send'])
                                ->where('ended_at', '<=', $nowText)
                                ->get();
      
        // 내역 없으면 종료
        if(!$expiredTransactions) return;
        
        foreach ($expiredTransactions as $transaction) {
            try{
                if ($transaction->state == 'open') {  // 거래창 열고 액션X 거래 취소 , 펜딩 제거 
                    $now = new DateTime();
                    $transaction->state = 'cancel';
                    $transaction->updated_at = $now;
                    $pendingCharge = $transaction->tetherAmount + $transaction->fee;
                    // 유저 테더 펜딩 제거  
                    $userId = ($transaction->order_type === 'sell') ? $transaction->offer_user_id : $transaction->client_user_id;
                    $user = User::where('id',$userId)->first(); 
                    $user->usdtPending = $user->usdtPending  -  $pendingCharge;
                    $user->save();
                    //Log::info("autoCancel Log transaction id=".$transaction->id ."////pendingCharge = ".$pendingCharge."/////user->usdtPending=".$user->usdtPending."/////user_id=".$user->id);
                } else if ($transaction->state == 'send') { // 입금자료 전송 후 액션X or 허위 자료 업로드 분쟁으로 변경 펜딩 해제 X
                    $transaction->state = 'dispute';
                }
                $transaction->save();
            } catch (Exception $e) {
                //Log::info("expiredTransactions failed!".$e->getMessage());
            }
        }
    
    }
}
