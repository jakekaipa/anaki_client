<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\TradeTransaction;
use App\Models\ExternalTransfer;
use App\Models\InternalTransfer;
use App\Models\Message;



class TransactionRepository
{
    public function __construct(
        User $user,
        TradeTransaction $tradeTransaction,
        ExternalTransfer $externalTransfer,
        InternalTransfer $internalTransfer,
        Message          $message
    )
    {
        $this->user = $user;
        $this->tradeTransaction = $tradeTransaction;
        $this->externalTransfer = $externalTransfer;
        $this->internalTransfer = $internalTransfer;
        $this->message          = $message;
    }

    /**
     * 내부 거래 내역
     */
    public function getTrasnactionList(string $where)
    {
        $query = $this->tradeTransaction
                        ->select('trade_transactions.*','offerUser.username as offerUserName','offerUser.realname as offerUserRealName','clientUser.username as clientUserName','clientUser.realname as clientUserRealName')
                        ->leftjoin('users as offerUser','offerUser.id','=','trade_transactions.offer_user_id')
                        ->leftjoin('users as clientUser','clientUser.id','=','trade_transactions.client_user_id')
                        ->orderby('trade_transactions.created_at', 'desc')
                        ->whereRaw($where);
        
        return $query->paginate(10);
    }

    /**
     * 전체 거래 개수 조회
     */
    public function getTransactionCount(){
        return $this->tradeTransaction->count('id');
    }

    /**
     * 거래 정보 조회
     */
    public function getTransactionInfo(int $id){
        return $this->tradeTransaction
                        ->with('offer_user')
                        ->with('client_user')
                        ->where('id',$id)
                        ->first();
    }

    /**
     * 외부 전송 내역
     */
    public function getExternalTransactionList(string $where,?string $name ){
        $query = $this->externalTransfer
                    ->orderby('created_at', 'desc')
                    ->whereRaw($where);
                    
        // 이름 검색시        
        if (!empty($name)) {
            $query->whereHas('getUserInfo', function ($query) use ($name) {
                $query->where('username', 'like', '%' . $name . '%')
                ->orwhere('realname', 'like', '%' . $name . '%');
            });
        } 
        
        return $query->with('getUserInfo')
                     ->paginate(10)
                     ->withQueryString();
    }

    /**
     * 외부 전송 개수 조회 
     */
    public function getExternalTransactionCount(){
        return $this->externalTransfer->count('id');
    }

    /**
     * 거래 상태 변경
     */
    public function changeTransactionState(int $transactionId,string $state){
        return $this->tradeTransaction->where('id',$transactionId)->update([
            'state' =>  $state
        ]);
    }

    /**
     * 지갑 간 전송 내역 
     */
    public function getTransferWalletList(string $where)
    {
        $query = $this->internalTransfer
            ->select('internal_transfers.*', 'senderUser.username as senderUsername', 'senderUser.realname as senderRealName', 
                     'recevierUser.username as receiverUsername', 'recevierUser.realname as receiverRealName')
                ->leftjoin('users as senderUser','senderUser.email','=','internal_transfers.sender_email')
                ->leftjoin('users as recevierUser','recevierUser.email','=','internal_transfers.receiver_email')
                ->orderby('internal_transfers.created_at', 'desc')
                ->whereRaw($where);

        return $query->paginate(10);
    }

    /**
     * 지갑 간 전송 갯수
     */
    public function getTransferWalletCount(){
        return $this->internalTransfer->where('transfer_type',2)->count('id');
    }

    /**
     * 내부 전송 완료된 거래 금액,수수료,레퍼럴 수수료 조회 
     */
    public function getCompletedInternalTransactions(array $where){
        return $this->internalTransfer
                        ->whereBetween('created_at', [$where['start'], $where['end']])
                        ->selectRaw('COALESCE(SUM(amount), 0) as amount_sum, 
                                     COALESCE(SUM(anaki_fee), 0) as anaki_fee_sum, 
                                     COALESCE(SUM(referral_fee), 0) as referral_fee_sum')
                        ->first();
    }

    /**
     * 거래 채팅 내역 조회
     */
    public function getChattingMessageList($id){
        return $this->message->where('trade_transaction_id',$id)->with('sender')->orderBy('id','desc')->get(); 
    }
    

    /**
     * 전날 총 거래 건수 (총 거래수, 완료건수,분쟁건수, 분쟁해결건수,취소 건수)
     */
    public function getTotalInternalTransactionCounts(array $where){
        return $this->tradeTransaction
                        ->whereBetween('created_at', [$where['start'], $where['end']])
                        ->selectRaw('
                                        count(id) as totalCount, 
                                        COALESCE(SUM(CASE WHEN state = "done" THEN 1 ELSE 0 END), 0) as totalCompleteCount,
                                        COALESCE(SUM(CASE WHEN state = "disput" THEN 1 ELSE 0 END), 0) as totalDisputCount,
                                        COALESCE(SUM(CASE WHEN state = "dispute-solved" THEN 1 ELSE 0 END), 0) as totalDisputeSolvedCount,
                                        COALESCE(SUM(CASE WHEN state = "cancel" THEN 1 ELSE 0 END), 0) as totalCancelCount
                                    ')  
                        ->first();
    }
}