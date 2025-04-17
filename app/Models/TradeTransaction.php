<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeTransaction extends Model
{
    protected $table = 'trade_transactions';

    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                => 'integer',
        'created_at'        => 'datetime',
        'ended_at'          => 'datetime',
        'state'             => 'string',
        'cancelReason'      => 'string',
        'order_type'        => 'string',
        'offer_user_id'     => 'integer',
        'client_user_id'    => 'integer',
        'transactionId'     => 'string',
        'tetherAmount'      => 'double',
        'fee'               => 'double',
        'priceType'         => 'integer',
        'price'             => 'decimal:2',
        'margin'            => 'double',
        'totalPayAmount'    => 'decimal:2',
        'payMethod'         => 'decimal:8',
        'bankName'          => 'string',
        'accountNumber'     => 'string',
        'accountName'       => 'string',
        'QRImage'           => 'string',
        'payProof'          => 'string',
        'buyListId'         => 'integer',
        'sellListId'        => 'integer',
        'isTransferred'     => 'boolean'
    ];

    public function offer_user()
    {
        return $this->belongsTo(User::class, 'offer_user_id');
    }

    public function client_user()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    // 마지막 채팅 메시지 가져오기
    public function getLastMessage()
    {
        return $this->hasone(Message::class,'trade_transaction_id');
    }
}
