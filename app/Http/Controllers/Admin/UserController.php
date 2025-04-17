<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminLoginLogs;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\Admin;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    public function __construct(UserRepository $user)
    {
        $this->user = $user;  
    }

    /**
     * 유저 리스트
     */
    public function index(Request $request)
    {
        $page = $request->input('page',1);
        $searchType = $request->input('searchType');
        $searchKey = trim($request->input('searchKey',''));
        $where ="1=1 ";

        if($searchKey){
            if( $searchType === 'name'){
                $where .= " and username like '%" . $searchKey . "%' or realname like '%" . $searchKey . "%'";
            } else {
                $where .=" and ".$searchType." like ".'"%'.$searchKey.'%"';
            }
        }

        // 유저리스트
        $userList = $this->user->getUserList($where);
        // 사용중인 전체 유저 개수
        $userTotalCount = $this->user->getTotalUserCount();
        
        return view('admin.userList',compact("userList","userTotalCount","page",'searchType','searchKey'));
    }

    /**
     * 유저 리스트 상세 
     */
    public function view($id)
    {
        $userInfo = $this->user->getUserInfo(urlSafeDecrypt($id));
        return view('admin.userView',compact("userInfo"));
    }

    /**
     * 유저 정보 업데이트
     */
    public function updateUserInfo(Request $request){
        $userInfo = $this->user->getUserInfo(urlSafeDecrypt($request->input('userId')));
        $userInfo->status = $request->input('status');
        if($request->input('password'))
        {
            $userInfo->password = bcrypt($request->input('password'));
        }
        $userInfo->save();
        return redirect('/admin/manage-user/view/'.$request->input('userId'))
                ->with('success', '정보가 성공적으로 업데이트되었습니다!'); 
    }

    /**
     *에이전트 유저의 하위 파트너 별 수익금
     */
    public function getReferralList($id){
       $referralList  = $this->user->getReferralInfo(urlSafeDecrypt($id));
    //    echo "<pre>";
    //     print_r($referralList->toArray());
    //    echo "</pre>";
       return view('admin.referralView',compact("referralList"));
    }


}
