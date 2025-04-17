<?php

namespace App\Models;

use App\Models\Admin\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TetherTransaction extends Model
{
    protected $table = 'tethertransaction';

    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                => 'integer',
        'created_at'        => 'datetime',
        'ended_at'          => 'datetime',
        'state'             => 'decimal:8',
        'cancelReason'      => 'string',
        'offer_user_id'    => 'integer',
        'seller_user_id'    => 'integer',
        'buyer_user_id'     => 'integer',
        'transactionId'     => 'string',
        'tetherAmount'      => 'double',
        'priceType'         => 'decimal:8',
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
        'sellListId'        => 'integer'
    ];

    public function offer_user()
    {
        return $this->belongsTo(User::class, 'offer_user_id');
    }

    public function buyer_user()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller_user()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function isAuthUser()
    {
        $authUserId = auth()->user()->id;
        if ($this->seller_user_id === $authUserId || $this->buyer_user_id === $authUserId) return true;
        return false;
    }
}
