<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ReferralPartner extends Model
{
    protected $table = 'referral_partner';
    protected $fillable = ['user_id', 'group_id', 'referral_code','fee_rate','referral_benefit','referral_benefit_total'];

    // 유저정보 가져오기
    public function getUserInfo(){
        return  $this->belongsTo(User::class,'user_id','id');
    }
}
