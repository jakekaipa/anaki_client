<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

class TradeOffer extends Model
{
    protected $table = 'trade_offers';

    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'user_id'           => 'integer',
        'order_type'        => 'string',
        'token_type'        => 'string',
        'payMethod'         => 'decimal:8',
        'bankName'          => 'string',
        'accountNumber'     => 'string',
        'accountName'       => 'string',
        'payQR'             => 'string',
        'priceType'         => 'decimal:8',
        'tradeVolMin'       => 'double',
        'tradeVolMax'       => 'double',
        'offerMargin'       => 'float',
        'fixedPrice'        => 'string',
        'offerTimeLimit'    => 'integer',
        'offerEndTime'      => 'datetime',
        'offerTag'          => 'string',
        'offerLabel'        => 'string',
        'offerCondition'    => 'string',
        'transGuide'        => 'string',
        'checkboxAuth'      => 'bool',
        'checkboxShowName'  => 'bool',
        'needMobileAuth'    => 'bool',
        'needKYCAuth'       => 'bool',
        'needAccountAuth'   => 'bool',
        'status'            => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAuth($query)
    {
        return $query->where('user_id', auth(get_auth_guard())->user()->id);
    }

    public function scopeBuyOrders($query)
    {
        return $query->where('order_type', 'buy');
    }

    public function scopeSellOrders($query)
    {
        return $query->where('order_type', 'sell');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
