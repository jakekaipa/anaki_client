@extends('admin.layouts.mainLayout')

@section("title")
    Manage Notcie  {{ isset($noticeInfo->id)? 'View':'Insert' }}
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
        
    </style>
@endsection

@section('content')

    <div class="container">
        <h1>{{ isset($noticeInfo->id)? '상세보기':'등록하기' }}</h1>

       
        <form action="{{ isset($noticeInfo->id)? '/admin/manage-notice/update':'/admin/manage-notice/store' }}" id='submitForm' method="POST" enctype="multipart/form-data" >
            @csrf
            <input type='hidden' name='id' value="{{ isset($noticeInfo->id)? urlSafeEncrypt($noticeInfo->id):'' }}">
            <div class="form-group">
                <label for="title">제목</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($noticeInfo->title)? $noticeInfo->title : '' }}" >
            </div>
            
            <div class="form-group">
                <label for="editor">내용</label>
                <textarea id="editor" name="contents" class="form-control">
                @if(isset($noticeInfo->contents) && !empty($noticeInfo->contents))
                {!! $noticeInfo->contents !!}
                @endif
                </textarea>
            </div> 
            
            @if(isset($noticeInfo->id))
            <div class="form-group">
                <label for="show">사용여부</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="Y" {{ isset($noticeInfo->status) && $noticeInfo->status == 'Y' ? 'checked' : '' }}>
                    <label class="form-check-label" for="inlineRadio1">사용</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="N" {{ isset($noticeInfo->status) && $noticeInfo->status == 'N' ? 'checked' : '' }}>
                    <label class="form-check-label" for="inlineRadio2">중지</label>
                </div>
            </div>
            @endif
            
            <div class="button-group" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <input type="button" class="btn btn-primary" onclick='submitForm()' value="{{ isset($noticeInfo->id)? '수정하기':'등록하기' }}">
                <input type="button" class="btn btn-primary" onclick="javascript:location.href='/admin/manage-notice/'" value="뒤로가기">
            </div>
        </form>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-toast">
                <strong>성공!</strong> {{ session('success') }}
            
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-toast">
                <strong>오류!</strong> {{ session('error') }}
            </div>
        @endif
    </div>
       
@endsection

@section('script')
    <script>
    setTimeout(function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 2000); 


    function submitForm(){
        $('#submitForm').submit();
    }
    
      ClassicEditor
        .create(document.querySelector('#editor'), {
            ckfinder: {
               uploadUrl: '/admin/manage-notice/upload'
            },
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'uploadImage', 'blockQuote', 'bulletedList', 'numberedList', 'alignment', '|',
                'undo', 'redo'
            ]
        })
        .catch(error => {
            console.error(error);
        });
    </script>
@endsection
