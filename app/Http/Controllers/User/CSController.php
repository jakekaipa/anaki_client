<?php

namespace App\Http\Controllers\User;

use Exception;

use App\Http\Controllers\Controller;
use App\Models\UserSupportTicket;
use App\Models\UserSupportTicketAttachment;
use App\Notifications\User\SupportTicketNotification;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CsRepository;

class CSController extends Controller
{

    public function __construct(CsRepository $cs)
    {
        $this->cs = $cs;  
    }

    /**
     * Sell List page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("Customer Service");
        $support_tickets = $this->cs->getCsList(Auth::user()->id); 
        return view('user.cs.index', compact("page_title", 'support_tickets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to create a support ticket.');
        }

        $user = Auth::user();
        if (!$user || !$user->id) {
            return back()->with('error', 'Invalid user information. Please try logging out and back in.');
        }

        $validator = Validator::make($request->all(), [
          'subject' => 'required|string|max:255|regex:/^[A-Za-z0-9\s\-\.,\?!]+$/',
          'desc' => 'required|string|max:5000|regex:/^[A-Za-z0-9\s\-\.,\?!]+$/', 
          'attachment.*' => "nullable|file|mimes:jpg,jpeg,png|max:10240",
        ]);

        $validated = $validator->validate();
        $validated['name'] = $user->fullname;
        $validated['email'] = $user->email;
        $validated['token'] = generate_unique_string('user_support_tickets', 'token');
        $validated['user_id'] = $user->id;
        $validated['status'] = 3;
        $validated['created_at'] = now();
        $validated = Arr::except($validated, ['attachment']);

        \Log::info('Attempting to create support ticket for user: ' . $user->id);

        DB::beginTransaction();
        try {
            $support_ticket_id = UserSupportTicket::insertGetId($validated);
            // 세션에 support_ticket_token 저장
            $existingTokens = Session::get('support_ticket_tokens', []);
            $existingTokens[] = $validated['token'];
            Session::put('support_ticket_tokens', $existingTokens);

            $email = $user->email;
            $basic_settings = BasicSettingsProvider::get();
            if ($basic_settings->email_notification == true) {
                Notification::route('mail', $email)->notify(new SupportTicketNotification($validated));
            }

            if ($request->hasFile('attachment')) {
                $validated_files = $request->file("attachment");
                $attachment = [];
                $files_link = [];
                foreach ($validated_files as $item) {
                    $upload_file = upload_file($item, 'support-attachment');
                    if ($upload_file != false) {
                        $attachment[] = [
                            'user_support_ticket_id'    => $support_ticket_id,
                            'attachment'                => $upload_file['name'],
                            'attachment_info'           => json_encode($upload_file),
                            'created_at'                => now(),
                        ];
                    }

                    $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
                }

                try {
                    UserSupportTicketAttachment::insert($attachment);
                } catch (Exception $e) {
                    \Log::info($e);

                    // 에러 발생 시 세션에서 해당 token 제거
                    $existingTokens = Session::get('support_ticket_tokens', []);
                    $existingTokens = array_diff($existingTokens, [$validated['token']]);
                    Session::put('support_ticket_tokens', $existingTokens);

                    UserSupportTicket::destroy($support_ticket_id);

                    delete_files($files_link);
                    return back()->with(['error' => ['Failed to upload attachment. Please try again.']]);
                }
            }

            DB::commit();
            return redirect()->route('user.cs.index')->with('success', 'Support ticket created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Support ticket creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create support ticket. Please try again.');
        }
    }

    /**
     * 문의 상세내역 
     */
    public function view($id,$commentsLastId = null){
        $page_title = __("Customer Service");
        $csInfo = $this->cs->getCsInfo(urlSafeDecrypt($id));
        // 마지막 댓글 있으면 읽음 처리
        if($commentsLastId){
            $this->cs->updateComments(urlSafeDecrypt($commentsLastId),Auth::user()->id);
        }
        return view('user.cs.csView',compact('csInfo','page_title'));
    }

    /**
     * 고객문의 댓글 입력(유저)
     */
    public function insertComments(Request $request){
        $csInfo = $this->cs->getCsInfo(urlSafeDecrypt($request->input('id')));
        $maxDeeps = ($this->cs->getCsCommentMaxDeeps($csInfo->id))??0;
        $type = $request->input('type','admin');
    
        $insertCommentsParam = [
            'deeps' =>  $maxDeeps + 1,  
            'write_user_id' => Auth::user()->id,  
            'comments' => $request->comments,  
            'user_support_ticket_id' => $csInfo->id,
            'created_at'=> now()  
        ];
         
        // broadcast(new SupportTicketEvent($request->comments,1))->toOthers();
        // broadcast(new SupportTicketEvent($request->comments,71))->toOthers();

        $redirectUrl = '/user/cs/view/'.$request->input('id');
       

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
}
