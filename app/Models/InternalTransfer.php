<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalTransfer extends Model
{
    use HasFactory;
    protected $fillable = ['sender_email', 'receiver_email', 'amount', 'anaki_fee','referral_fee','referral_user_id'];

    public function getSenderUserInfo(){
        return $this->hasone(User::class,'email','sender_email')->select('email','username','realname');
    }

    public function getReceiverUserInfo(){
        return $this->hasone(User::class,'email','receiver_email')->select('email','username','realname');
    }
}
