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
        $page = $request->input('page',1);
        $noticeList = $this->notice->getNoticeList();
        return view('admin.noticeList',compact('noticeList','page')); 
    }

    /**
     *  공지사항 상세보기 폼 or 저장하기 폼 
     */
    public function moveForm($id = null)
    {
        $noticeInfo = '';
        if ($id) {
            $noticeInfo = $this->notice->view(urlSafeDecrypt($id));
        }
        // 결과를 뷰에 전달
        return view('admin.noticeForm', compact('noticeInfo'));
    }

     /**
     * 공지사항 저장
     */
    public function store(Request $request){
        $insertParam = [
            'title' => $request->input('title'),
            'contents' => $request->input('contents'),
        ];
      
        try {
            $this->notice->store($insertParam);
            return redirect('/admin/manage-notice/')
            ->with('success', '등록되었습니다!'); 
        } catch (Exception $e) {
            Log::info("notice update exception:" . $e);
            return redirect('/admin/manage-notice/')
            ->with('error', '등록 중 오류가 발생하였습니다.'); 
        }
    }

    /**
     * 공지사항 이미지 업로드
     */
    public function uploadNoticeImage(Request $request)
    {
        if(!$request->hasFile('upload')){
            return response()->json([
                'uploaded' => false,
                'error' => ['message' => '파일이 없습니다.']
            ]); 
        }
        
        if ($request->hasFile('upload') && $request->file('upload')->isValid()) {
            $file = $request->file('upload');
            
            // 파일을 'images' 디렉토리에 저장
            $path = $file->store('images', 'public');
    
            // 성공적으로 업로드된 파일의 URL 반환
            return response()->json([
                'uploaded' => true,
                'url' => asset('storage/' . $path)
            ]);
        } 

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => '파일을 업로드할 수 없습니다.']
        ]);  
    }

    /**
     * 공지사항 수정
     */
    public function update(Request $request){
        $updateParam = [
            'title' => $request->input('title'),
            'contents' => $request->input('contents'),
            'status' => $request->input('status'),
        ];
      
        try {
            $this->notice->update(urlSafeDecrypt($request->input('id')),$updateParam);
            return redirect('/admin/manage-notice/form/'.$request->input('id'))
            ->with('success', '수정되었습니다!'); 
        } catch (Exception $e) {
            Log::info("notice update exception:" . $e);
            return redirect('/admin/manage-notice/form/'.$request->input('id'))
            ->with('error', '수정 중 오류가 발생하였습니다.'); 
        }
    }



}
