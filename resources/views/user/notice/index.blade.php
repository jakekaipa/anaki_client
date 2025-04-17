@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')




@section('content')
    <div class="content cs-wrap">
        <h2 class="page-tit">{{ __('공지사항') }}</h2>

        <div class="container-row">
            <div class="card-box">
                <div class="table-wrap">
                    <table class="tbl-list cs-tbl">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('제목') }}</th>
                                <th>{{ __('작성일') }}</th>
                                <th>{{ __('View Details') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php 
                                $count = ($page-1)*10+1; 
                            @endphp
                            @foreach ($noticeList as $item)
                            @php if($item->status === 'N') continue; @endphp
                                <tr>
                                    <td style='width:5%'>{{ $count }}</td>
                                    <td style='width:100px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;'>{{  strlen($item->title) > 30 ? substr($item->title, 0, 30) . '...' : $item->title }}</td>
                                    <td> {{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td style="width:10%">
                                        <div class="last-a">
                                            <a href="/user/notice/form/{{ urlSafeEncrypt($item->id) }}">
                                                <button class="btn-default normal">
                                                    <span class="txt ask">{{ __('View Details') }}</span>
                                                </button>
                                            </a> 
                                        </div>
                                    </td>
                                </tr>
                            @php $count++ @endphp
                            @endforeach
                           
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    {{ $noticeList->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection


<style>
.pagination {
    display: flex;
    justify-content: center;
    list-style-type: none;
    padding: 0;
    margin: 20px 0;
}

.pagination li {
    margin: 0 5px;
}

.pagination li a {
    color: #ffffff;
    background-color: #21b8a1; /* 초록색 배경 */
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px solid #21b8a1;
    font-weight: bold;
    transition: all 0.3s ease; /* 부드러운 전환 효과 */
    font-size: 16px;
}

.pagination li a:hover {
    background-color: ##21b8a1; /* 마우스를 올렸을 때 어두운 초록색 */
    border-color: ##21b8a1;
    transform: scale(1.05); /* 마우스 호버 시 크기 확대 */
}

.pagination li.active a {
    background: linear-gradient(45deg, #21b8a1, #21b8a1); /* 그라데이션 배경 */
    color: white;
    border-color: #21b8a1;
}

.pagination li.disabled a {
    background-color: #f1f1f1;
    color: #ccc;
    pointer-events: none; /* 비활성화된 페이지는 클릭할 수 없도록 */
    border-color: #ccc;
}

.pagination li.disabled a:hover {
    background-color: #f1f1f1; /* 비활성화된 페이지는 hover 효과 없음 */
}
</style>

