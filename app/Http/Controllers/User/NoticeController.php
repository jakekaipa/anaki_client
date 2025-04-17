<?php

namespace App\Http\Controllers\User;

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
use App\Repositories\NoticeRepository;


class NoticeController extends Controller
{
    public function __construct(NoticeRepository $notice)
    {
        $this->notice = $notice;  
    }

    /**
     * 공지사항 리스트
     */
    public function index(Request $request)
    {   
        $page_title = 'Notice';
        $page = $request->input('page',1);
        $noticeList = $this->notice->getNoticeList();
        return view('user.notice.index',compact('noticeList','page','page_title')); 
    }

    /**
     *  공지사항 상세보기 폼 or 저장하기 폼 
     */
    public function moveForm($id = null)
    {
        $page_title ='Notice View';
        $noticeInfo = '';
        if ($id) {
            $noticeInfo = $this->notice->view(urlSafeDecrypt($id));
        }
        // 결과를 뷰에 전달
        return view('user.notice.noticeView', compact('noticeInfo','page_title'));
    }


}
