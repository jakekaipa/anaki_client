<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\ReferralPartner;

class UserRepository
{
    public function __construct(
        User $user,
        ReferralPartner $referralPartner,
    )
    {
        $this->user = $user;
        $this->referralPartner = $referralPartner;
    }

    /**
     * 유저 리스트 
     */
    public function getUserList(string $where){
      return  $this->user->with('getReferenceInfo')
                ->orderby('created_at','desc')
                ->whereRaw($where)
                ->paginate(10)
                ->withQueryString();
    }

    /**
     * 사용중인 사용자 카운트
     */
    public function getTotalUserCount(){
        return $this->user->where('status','=',1)->count('id');
    }

    /**
     * 유저 정보 조회
     */
    public function getUserInfo(int $id){
        return $this->user->where('id',$id)->with('getReferenceInfo')->first();
    }

    /**
     * 에이전트 유저에 속한 파트너 리스트 
     */
    public function getReferralInfo(int $id){
        return $this->referralPartner->with('getUserInfo')->where('group_id',$id)->paginate(10); 
    }

     /**
     * 유저 정보 업데이트 
     */
    public function updateUserInfo(int $userId,array $updateParam ){
        $this->user->where('id',$userId)->update($updateParam);
    }

    /**
     * 하루 전 가입자 수 조회
     */
    public function getRegistUserCount(array $where){
        return $this->user->whereBetween('created_at', [$where['start'], $where['end']])->count();
    }


}