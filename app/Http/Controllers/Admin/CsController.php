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
use App\Repositories\CsRepository;
use App\Events\Admin\SupportTicketEvent;

class CsController extends Controller
{
    public function __construct(CsRepository $cs)
    {
        $this->cs = $cs;  
    }

    /**
     * 고객문의 리스트
     */
    public function index(Request $request)
    {   
        $page = $request->input('page',1);
        $csList = $this->cs->getCsList();
        return view('admin.csList',compact('csList','page')); 
    }

    /**
     * 고객문의 상세 내역
     */
    public function view($id,$commentsLastId = null){
        $csInfo = $this->cs->getCsInfo(urlSafeDecrypt($id));
        // 마지막 댓글 있으면 읽음 처리
        if($commentsLastId){
            $this->cs->updateComments(urlSafeDecrypt($commentsLastId),Auth::guard('admin')->id());
        }
        return view('admin.csView',compact('csInfo'));
    }

    /**
     * 고객문의 댓글 입력(관리자)
     */
    public function insertComments(Request $request){
        $csInfo = $this->cs->getCsInfo(urlSafeDecrypt($request->input('id')));
        $maxDeeps = ($this->cs->getCsCommentMaxDeeps($csInfo->id))??0;
        $type = $request->input('type','admin');

        $insertCommentsParam = [
            'deeps' =>  $maxDeeps + 1,  
            'write_user_id' =>Auth::guard('admin')->id(),  
            'comments' => $request->comments,  
            'user_support_ticket_id' => $csInfo->id,
            'created_at'=> now()  
        ];
         
        // broadcast(new SupportTicketEvent($request->comments,1))->toOthers();
        // broadcast(new SupportTicketEvent($request->comments,71))->toOthers();
        $redirectUrl = '/admin/manage-cs/view/'.$request->input('id');

        try {
            $this->cs->insertComments($insertCommentsParam);
            return redirect($redirectUrl)
            ->with('success', 'Registration successful.');  
        } catch (Exception $e) {
            Log::info("insertComments exception:" . $e);
            return redirect($redirectUrl)
            ->with('error', 'An error occurred. Please try again.'); 
        }
        
    }
    
    /**
     * 고객 문의 상태 업데이트
     */
    public function changeStatus(Request $request){
        $result = 0;
        try{    
            $this->cs->updateTicketStatus(
                ticketId:(int)urlSafeDecrypt($request->input('ticketId')),
                status: $request->input('status')
            );
            $result = 1;
        } catch (Exception $e) {
            Log::info("insertComments exception:" . $e);
            $result = -1;
        }
        return $result;
    }

}
