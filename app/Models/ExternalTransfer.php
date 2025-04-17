<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ExternalTransfer extends Model
{
    protected $table = 'external_transfers';
    protected $fillable = ['user_id', 'amount', 'network_fee','to'];

      // 유저정보 가져오기
    public function getUserInfo(){
        return  $this->belongsTo(User::class,'user_id','id');
    }

}
