<?php

namespace App\Http\Controllers\User;

use App\Constants\NotificationConst;
use App\Events\NewPrivateMessage;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Message;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TradeTransaction;

class MessageController extends Controller
{
    public function getReceiverName(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($request->input('receiver_id'));

            // username이 있으면 우선적으로 사용
            if ($user->username) {
                $displayName = $user->username;
            } else {
                // username이 없는 경우 firstname과 lastname을 사용
                $fullName = trim($user->firstname . ' ' . $user->lastname);
                $displayName = $fullName ?: 'Unknown User'; // fullName도 비어있을 경우 대비
            }

            return response()->json([
                'success' => true,
                'receiver_name' => $displayName,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'chat_id' => 'required|string',
                'message' => 'required|string|max:1000',
            ]);

            $sender = auth()->user();

            // username이 있으면 우선적으로 사용
            if ($sender->username) {
                $senderName = $sender->username;
            } else {
                // username이 없는 경우 firstname과 lastname을 사용
                $fullName = trim($sender->firstname . ' ' . $sender->lastname);
                $senderName = $fullName ?: 'Unknown User'; // fullName도 비어있을 경우 대비
            }
            
            $tradeTransactionId =  explode('-',$request->input('chat_id'))[0];

            $message = Message::create([
                'sender_id'             => $sender->id,
                'receiver_id'           => $request->input('receiver_id'),
                'chat_id'               => $request->input('chat_id'),
                'message'               => $request->input('message'),
                'trade_transaction_id'  => $tradeTransactionId
            ]);
            // NewPrivateMessage 이벤트에 sender_name 추가
            broadcast(new NewPrivateMessage($message, $request->receiver_id, $senderName))->toOthers();

            // 응답에도 sender_name 포함
            return response()->json([
                'message' => $message,
                'sender_name' => $senderName,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|string',
            ]);

            $query = Message::with('sender')
                ->where('chat_id', $request->input('chat_id'));

            $totalCount = $query->count();

            $messages = $query->orderBy('created_at', 'desc')  // 최신 메시지부터 정렬
                ->take(1000)  // 최근 1000개 선택
                ->get()     // 데이터 가져오기
                ->reverse();  // 컬렉션을 역순으로 변경

            return response()->json([
                'messages' => $messages->values(),
                'total_count' => $totalCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB 최대
            'receiver_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');

            $attachment = Attachment::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $downloadLink = route('user.messages.downloadFile', $attachment->id);

            $notification_content = [
                'title'   => __('첨부파일 업로드됨'),
                'message' => __(
                    ':nickname님이 새로운 첨부파일을 업로드했습니다.',
                    ['nickname' => auth()->user()->realname ?? auth()->user()->username]
                ),
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'          => NotificationConst::ATTACHMENT_UPLOADED,
                'sender_id'       => Auth::id(),
                'user_id'       => $request->input('receiver_id'),
                'message'       => $notification_content,
            ]);

            return response()->json([
                'success' => true,
                'file_name' => $file->getClientOriginalName(),
                'download_link' => $downloadLink,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'File upload failed.'], 400);
    }

    public function downloadFile($id)
    {
        $attachment = Attachment::findOrFail($id);
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function getNotifications()
    {
        // $notifications = UserNotification::auth()->latest('id')->take(4)->get();

        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest('id')
            ->take(4)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'sender_id' => $notification->sender_id ?? '',
                    'order_id' =>  $notification->order_id ?? '',
                    'message' => [
                        'title' => $notification->message->title, // 'message' 객체 대신 직접 'type' 필드에 접근
                        'message' => $notification->message->message ?? '', // 'message' 객체의 'message' 프로퍼티에 접근
                    ],
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }

    /**
     * 채팅창 내에서 채팅은 바로 읽음 처리
     */
    public function messageReadUserUpdate(Request $request)
    {
        $tradeId = explode('-',$request->input('chatId'))[0];
        $result = 0;
         // 접속 유저가 recevier일때 메시지 읽음 처리 
        $messageInfo = Message::where([
                                        ['trade_transaction_id', $tradeId],
                                        ['read_user_id', 0],
                                    ])->first();

        if(!$messageInfo) return $result=-1;

        try{
            Message::where('id',$messageInfo->id)->update(
            [
                'read_user_id' => $messageInfo->receiver_id
            ]);
            $result =1;
        } catch (\Exception $e) {
            Log::error('messageReadUserUpdate: ' . $e->getMessage());
        }

        return $result;

    }

}
