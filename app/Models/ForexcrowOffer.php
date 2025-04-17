<?php

namespace App\Models;

use App\Models\User;
use App\Models\Forexcrow;
use App\Models\Admin\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForexcrowOffer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type'             => 'string',
        'receiver_id'      => 'integer',
        'rate_currency_id' => 'integer',
        'creator_id'       => 'integer',
        'forexcrow_id'     => 'integer',
        'for_user_id'      => 'integer',
        'amount'           => 'decimal:16',
        'rate'             => 'decimal:16',
        'sale_currency_id' => 'integer',
        'status'           => 'integer',
    ];

    public  function forexcrow()
    {
        return $this->belongsTo(Forexcrow::class, 'forexcrow_id');
    }

    public function saleCurrency(){
        return $this->belongsTo(Currency::class, 'sale_currency_id');
    }

    public function rateCurrency(){
        return $this->belongsTo(Currency::class, 'rate_currency_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function Seller(){
        return $this->belongsTo(User::class, 'for_user_id');
    }

}
