@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')


@section('style')
<style>
    /* 기본적인 스타일 */
    .content {
        padding: 20px;
        background-color: #f9f9f9;
    }

    .page-tit {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .container-row {
        display: flex;
        justify-content: flex-start; /* 왼쪽 정렬 */
        padding: 20px 0;
    }

    .card-box {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 80%;
    }

    .detail-info {
        font-family: Arial, sans-serif;
    }

    /* 제목 레이블 */
    .detail-title,
    .detail-content {
        margin-top: 15px;
    }

    .label {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    /* 제목 */
    .detail-title h3 {
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    /* 내용 */
    .detail-content p {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin-top: 15px;
    }

    /* 첨부 이미지 */
    .detail-image {
        width: 100%;
        max-width: 500px;
        margin-top: 15px;
    }

    /* "첨부 파일" 텍스트 */
    .image-label {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px; /* 이미지와의 간격 */
    }

    /* 이미지 컨테이너 */
    .image-container {
        width: 100%;
    }

    /* 이미지 스타일 */
    #images {
        width: 30%; /* 이미지 크기 조정 */
        height: auto;
        border-radius: 5px;
    }

    /* 댓글 입력창 */
    .comment-section {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .comment-section textarea {
        width: 50%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: none;
        box-sizing: border-box;
    }

    .comment-section button {
        align-self: flex-start;
        padding: 10px 20px;
        background-color: #21b8a1;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .comment-section button:hover {
        background-color: #0056b3;
    }

    /* 댓글 리스트 */
    .comment-list {
        margin-top: 30px;
        max-height: 300px; /* 최대 높이를 설정 (원하는 높이로 조정) */
        overflow-y: auto; /* 세로 스크롤을 추가 */
    }

    .comment-item {
        background-color: #f1f1f1;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        font-size: 14px;
        line-height: 1.5;
    }

    .comment-item p {
        margin: 0;
        color: #333;
    }

    .comment-item strong {
        color: #007bff;
    }

    /* 반응형 디자인: 작은 화면에서 댓글 입력창 및 리스트 크기 조정 */
    @media (max-width: 768px) {
        .container-row {
            flex-direction: column;
            align-items: flex-start; /* 왼쪽 정렬 */
        }

        .card-box {
            width: 100%;
        }

        .detail-title h3 {
            font-size: 22px;
        }

        .detail-content p {
            font-size: 14px;
        }

        .comment-section textarea {
            font-size: 12px;
        }

        .comment-section button {
            font-size: 12px;
            padding: 8px 16px;
        }

        .comment-item {
            padding: 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="content cs-wrap">
    <h2 class="page-tit">{{ __('View Details') }}</h2>

    <div class="container-row">
        <div class="card-box">
            <div class="detail-info">
                <!-- 제목 -->
                <div class="detail-title">
                    <div class="label">{{ __('Title') }}</div>
                    <p>{{ $csInfo->subject }}</p>
                </div>
                
                <div style="height: 1vh;"></div>

                <!-- 내용 -->
                <div class="detail-content">
                    <div class="label">{{ __('문의 내용') }}</div>
                    <p>{{ $csInfo->desc }}</p>
                </div>
                
                <div style="height: 1vh;"></div>

                <!-- 첨부 이미지 -->
               
                <div class="detail-image">
                    <div class="image-label">{{ __('첨부파일') }}</div> <!-- 첨부 파일 텍스트 -->
                    <div class="image-container">
                        @if(isset($csInfo->attachments[0]))
                            <img src="{{ $csInfo->attachments[0]->attachment_info->file_link }}" id="images" alt="첨부 이미지" class="img-fluid">
                        @else
                            첨부된 파일이 없습니다
                        @endif
                    </div>
                </div>
                

                <div style="height: 1vh;"></div>

                <div class="comment-section">
                    <button type="button" onclick="javascript:location.href='/user/cs'" class="btn btn-primary">{{ __('뒤로가기') }}</button>
                </div>

                <div style="height: 1vh;"></div>

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

                 <!-- 댓글 입력창 -->
                 <form action="/user/cs/insert-comment" method="POST" id="commentForm">
                    @csrf
                    <input type='hidden' name='id' value="{{ urlSafeEncrypt($csInfo->id) }}">
                    <input type='hidden' name='type' value="user"> <!-- 어드민 , 유저 등록 구분 -->
                    <div class="comment-section">
                        <textarea id="comment-input" name="comments" placeholder="{{ __('댓글을 입력하세요') }}" rows="4" cols="50"></textarea>
                        <button type="button" onclick="submitForm()" class="btn btn-primary">{{ __('댓글 등록') }}</button>
                    </div>
                </form>

                

                <div id="comment-list" class="comment-list">
                    @if(isset($csInfo->getComments))
                        @foreach($csInfo->getComments as $comment)
                        <div class="comment-item">
                            <p>
                                @if ($csInfo->user_id == $comment->write_user_id )
                                    <strong>{{ $csInfo->user->username }} </strong>  ({{ $comment->created_at->format('Y-m-d H:i') }})
                                @else 
                                    <strong>관리자</strong>  ({{ $comment->created_at->format('Y-m-d H:i') }}) 
                                @endif     
                            </p>
                            <p>{{ $comment->comments }}</p>
                        </div>
                        @endforeach
                    @endif    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
    setTimeout(function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 2000); 

    function submitForm(){
        let comment = document.getElementById('comment-input').value;
        if(!comment){
            alert("{{ __('댓글을 입력하세요') }}");
            return false;
        }
        $('#commentForm').submit();
    }
    
    </script>
@endpush
