@extends('admin.layouts.mainLayout')
@section("title")
Manage Notice
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

    /* 공지사항 제목과 버튼을 한 줄로 정렬 */
    .header-container {
        display: flex;
        justify-content: center; /* 좌우 정렬 */
        align-items: center; /* 수직 중앙 정렬 */
        margin-bottom: 30px;
    }

    .header-container h1 {
        margin: 0; /* 제목의 기본 margin 제거 */
    }

    .header-container .btn {
        margin: 0; /* 버튼에 기본 margin 제거 */
    }

    .board-table {
        width: 100%;
        border-collapse: collapse;
    }

    .board-table th, .board-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    .board-table th {
        background-color: #007bff;
        color: white;
    }

    .board-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .board-table tr:hover {
        background-color: #ddd;
    }

    .board-title {
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .board-title:hover {
        color: #0056b3;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .pagination a {
        text-decoration: none;
        color: #007bff;
        padding: 10px 15px;
        border: 1px solid #007bff;
        margin: 0 5px;
        border-radius: 5px;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: white;
    }

    .pagination .active {
        background-color: #007bff;
        color: white;
        pointer-events: none;
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
</style>
@endsection

@section('content')
    <div class="container">
        <!-- 공지사항 제목과 버튼을 한 줄로 배치 -->
        <div class="header-container">
            <h1>공지사항</h1>
            <input type='button' class='btn btn-primary btn-xs' onclick='makeNotice()' value='등록하기'>
        </div>

        <table class="board-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>상태</th>
                    <th>작성일</th>
                    <th>상세보기</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $count = ($page-1)*10+1; 
                @endphp
                @foreach($noticeList as $list)
                <tr>
                    <td>{{ $count }} </td>
                    <td style='white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>{{ $list->title }}</td>
                    <td>{{ ($list->status === 'Y')? '게시중': '사용중지' }}</td>
                    <td>{{ $list->created_at->format('Y-m-d') }}</td>
                    <td>
                        <input type='button' class='btn btn-block btn-primary btn-xs' onclick="noticeView('{{ urlSafeEncrypt($list->id) }}')" value='상세보기'>
                    </td>
                </tr>
                @php 
                    $count++;
                @endphp
                @endforeach
            </tbody>
        </table>
        
        <div class="pagination">
                {{ $noticeList->links() }}
        </div>
    </div>
@endsection

@section('script')
<script>
    function makeNotice(){
        location.href='/admin/manage-notice/form';
    }
    function noticeView(id){
        location.href='/admin/manage-notice/form/'+id;
    }
</script>
@endsection
