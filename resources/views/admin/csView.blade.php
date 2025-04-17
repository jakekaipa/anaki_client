@extends('admin.layouts.mainLayout')

@section("title")
    Manage Cs View
@endsection

@section('style')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }

        .notice-title {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
        }

        .notice-content {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        .notice-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
        }

        .back-btn {
            display: block;
            margin-top: 30px;
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            width: 100px;
            margin-left: auto;
            margin-right: auto;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        /* CKEditor 내부 영역 크기 */
        .ck-editor__editable_inline {
            min-height: 600px; /* 높이를 조정 */
            resize: vertical;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            padding: 1rem;
            margin: 1rem 0;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            padding: 1rem;
            margin: 1rem 0;
        }

        /* textarea 크기 조정 */
        #content {
            min-height: 300px; /* 기본 높이 */
            height: auto; /* 자동으로 크기 조정 */
        }

        .form-select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            background-color: #f8f9fa;
            color: #555;
            transition: border-color 0.3s ease;
    }
     
    </style>
@endsection
@section('content')
    <div class="container">
        <h1>상세보기</h1>

            <div class="form-group">
                <label for="title">제목</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $csInfo->subject }}" readonly >
            </div>

            <div style="height: 2vh;"></div>

            <div class="form-group">
                <label for="contet">내용</label>
                <textarea id="content" name="contents" class="form-control" readonly>{{ $csInfo->desc }}</textarea>
            </div> 
           
            <div style="height: 2vh;"></div>

            <div class="image-section" style="margin-top: 20px;">
                    <h3>첨부 이미지</h3>
                    @if(isset($csInfo->attachments[0]))
                    <img src="{{ $csInfo->attachments[0]->attachment_info->file_link }}"style="width: 300px; height: auto;" alt="첨부 이미지" class="img-fluid">
                    @else
                        <p>첨부된 이미지가 없습니다.</p>
                    @endif
            </div>
           
            <div style="height: 2vh;"></div>

            <div class="form-group">
                <label for="contet">상태</label>
                <select id="status" class="form-select" onchange="changeStatus(this.value)" style='width:15%'>
                    <option value = 3 {{ ($csInfo->status == 3)? 'selected':'' }} >대기중</option>
                    <option value = 2 {{ ($csInfo->status == 2)? 'selected':'' }} >진행중</option>
                    <option value = 1 {{ ($csInfo->status == 1)? 'selected':'' }} >해결완료</option>
                </select>
            </div> 
           
            <div class="button-group" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <input type="button" class="btn btn-primary" onclick="javascript:location.href='/admin/manage-cs/'" value="뒤로가기">
            </div>
       

        <div style="height: 4vh;"></div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-toast">
            {{ __(session('success')) }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-toast">
            {{ __(session('error')) }}
        </div>
        @endif

        <!-- 댓글 섹션 -->
        <div class="comments-section">
            <h2>댓글</h2>

            <!-- 댓글 입력 폼 -->
            <form action="/admin/manage-cs/insert-comment" method="POST" id="commentForm">
                @csrf
                <input type='hidden' name='id' value="{{ urlSafeEncrypt($csInfo->id) }}">
                <input type='hidden' name='type' value="admin"> <!-- 어드민 , 유저 등록 구분 -->
                <div class="form-group">
                    <!-- <label for="comment">댓글 작성</label> -->
                    <textarea class="form-control" id="comment" name="comments" rows="4" placeholder="댓글을 작성해주세요" required></textarea>
                </div>
                <button type="button" onclick="submitForm()" class="btn btn-primary">댓글 등록</button>
            </form>

            <div class="comments-list" style="margin-top: 30px; max-height: 400px; overflow-y: auto;">
                @if(isset($csInfo->getComments))
                    <ul class="list-group">
                        @foreach($csInfo->getComments as $comment)
                            <li class="list-group-item">
                                @if ($csInfo->user_id == $comment->write_user_id )
                                    <strong>{{ $csInfo->user->username }} </strong>
                                @else 
                                    <strong>관리자</strong> 
                                @endif
                                    ({{ $comment->created_at->format('Y-m-d H:i') }})
                                    <p>{{ $comment->comments }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>댓글이 없습니다.</p>
                @endif
            </div>
        </div>
       
    </div>
@endsection


@section('script')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>    
    setTimeout(function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 2000); 

    // 상태 변경
    function changeStatus(value){
        $.ajax({
            url: '/admin/manage-cs/change-status', 
            method: 'POST',
            data: {
                ticketId: "{{ urlSafeEncrypt($csInfo->id) }}",
                status: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
            if(response = 1){
                    alert('변경 되었습니다.');
            } else{
                    alert('변경 작업중 오류가 발생하였습니다.');
            }
            
            },
            error: function(xhr, status, error) {
                alert('오류가 발생하였습니다.');
            }
        });
    }

    function submitForm(){
        const comment = $('#comment').val();
        if(!comment){
            alert('내용을 입력해주세요');
            return false;
        }
        $('#commentForm').submit();
    }

      // 고객문의 소켓 보류 
    // document.addEventListener('DOMContentLoaded', function() {
    //     var primaryKey = "{{ env('PUSHER_APP_KEY') }}"; // Pusher의 앱 키
    //     var cluster = "{{ env('PUSHER_APP_CLUSTER') }}"; // Pusher 클러스터
    //     var currentUserId = {{ Auth::guard('admin')->id() }};    

    //     var pusher = new Pusher(primaryKey, {
    //         cluster: cluster
    //     });

    //     // 현재 사용자의 ID로 개인 채널을 구독
    //     var channel = pusher.subscribe('support_conversation.' + currentUserId);

    //     function bindPusherEvents() {
    //         channel.bind('pusher:subscription_succeeded', function() {
    //             console.log('Successfully subscribed to private channel: support_conversation.' + currentUserId);
    //         });

    //         channel.bind('pusher:subscription_error', function(error) {
    //             console.error('Pusher subscription error:', error);
    //         });

    //         channel.bind('support-conversation', function(data) {
    //             console.log('New message received:', data); // 수신된 메시지 출력
    //         });
    //     }

    //     // 이벤트 바인딩 함수 호출
    //     bindPusherEvents();
    // });
    </script>
@endsection
