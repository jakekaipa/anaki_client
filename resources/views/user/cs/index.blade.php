@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="content cs-wrap">
        <h2 class="page-tit">{{ __('내 문의') }}</h2>

        <div class="container-row">
            <div class="card-box">
                <div class="table-wrap">
                    <table class="tbl-list cs-tbl">
                        <thead>
                            <tr>
                                <th>{{ __('Ticket ID') }}</th>
                                <th>{{ __('제목') }}</th>
                                <th>{{ __('상태') }}</th>
                                <th>{{ __('작성일') }}</th>
                                <th>{{ __('View Details') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($support_tickets as $item)
                            @php 
                                if($item->status == 3){
                                    $statusText = __('Pending Tickets');
                                } else if($item->status == 2){
                                    $statusText = __('Active Tickets');
                                } else {
                                    $statusText = __('Solved Tickets');     
                                }
                            @endphp
                                <tr>
                                    <td style='width:5%'>#{{ $item->token }}</td>
                                    <td style='width:100px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;'>{{ $item->subject }}</td>
                                    <td style='width:10%'>
                                        <span class="{{ $item->stringStatus->class }}">
                                           {{ $statusText }}
                                        </span>
                                    </td>
                                    <td> {{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td style="width:10%">
                                        <div class="last-a">
                                        @if(isset($item->getCommentsOne) && $item->getCommentsOne->write_user_id !== Auth::user()->id && (!$item->getCommentsOne->read_user_id) )
                                            <a href="/user/cs/view/{{ urlSafeEncrypt($item->id) }}/{{ urlSafeEncrypt($item->getCommentsOne->id) }}">
                                                <button class="btn-default normal">
                                                    <span class="txt ask">{{ __('View Details') }}</span>
                                                </button>
                                            </a>
                                            <div style="padding: 10px; border-radius: 5px; font-weight: bold; text-align: center; display: flex; align-items: center;">
                                                    <span style="background-color: #ff0000; color: white; padding: 3px 6px; border-radius: 3px; font-size: 12px; margin-right: 8px;">
                                                        New
                                                    </span>
                                            </div>
                                        @else
                                            <a href="/user/cs/view/{{ urlSafeEncrypt($item->id) }}">
                                                <button class="btn-default normal">
                                                    <span class="txt ask">{{ __('View Details') }}</span>
                                                </button>
                                                
                                            </a> 
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="tbl-btn flex-e-s">
                    <button class="btn-default normal" onclick="openPopSheet('sheet1')">
                        <span class="txt ask">{{ __('문의하기') }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- <div class="container-row">
            <div class="card-box">
                <div class="table-wrap">
                    <table class="tbl-list dsbo-tbl">
                        <colgroup>
                            <col class="col1">
                            <col class="col2">
                            <col class="col3">
                            <col class="col4">
                            <col class="col5">
                        </colgroup>

                        <thead>
                            <tr>
                                <th>{{ __('Ticket ID') }}</th>
                                <th>{{ __('이메일') }}</th>
                                <th>{{ __('제목') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('마지막 답변') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div class="deal-empty">
                                        <p class="empty-txt">{{ __('No Records Found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="sheet1">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('문의하기') }}</h2>
                </div>

                <form action="{{ route('user.cs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="ps-content custom-scroll trans">
                        <div class="cs-inqury">

                            <div class="i-txt__wrap">
                                <label for="" class="i-txt__label">{{ __('제목') }}</label>
                                <div class="i-txt__box">
                                    <input name="subject" type="text" class="i-txt__input" title="{{ __('제목을 입력하세요') }}"
                                        placeholder="{{ __('제목을 입력하세요') }}" maxlength='60'>
                                    <button type="button" class="i-txt__del" title="{{ __('입력내용 삭제') }}"><img
                                            src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                                </div>
                            </div>
                            <div class="i-txt__wrap">
                                <label for="askCont" class="i-txt__label">{{ __('문의 내용') }}</label>
                                <div class="textarea">
                                    <textarea name="desc" id="askCont" maxlength="1000" placeholder="{{ __('상담을 위하여 자세한 문의사항을 작성해 주세요') }}"></textarea>
                                </div>
                                <div class="text-cnt">
                                    <span class="cnt-num">0</span>/1000
                                </div>
                            </div>
                            <div class="i-txt__wrap">
                                <label for="" class="i-txt__label">{{ __('첨부파일') }}</label>
                                <div class="file-inp">
                                    <input type="text" class="f-inp" id="file-names" readonly
                                        placeholder="{{ __('문제가 발생되었을 때 캡쳐사진을 첨부하여 주세요') }}">
                                    <input type="file" name="attachment[]" multiple
                                        class="attach-file-input hidden-input" id="file-upload">
                                    <button type="button" class="btn-file"
                                        onclick="document.getElementById('file-upload').click();">
                                        <span class="txt">{{ __('+ 파일 선택') }}</span>
                                    </button>
                                </div>
                                <div id="file-list"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ps-process">
                        <button type="button" class="ps-process-btn cancel" onclick="closePopSheet('sheet1')">
                            <span class="txt">{{ __('취소') }}</span>
                        </button>
                        <button type="submit" class="ps-process-btn" onclick="closePopSheet('sheet1')">
                            <span class="txt">{{ __('문의하기') }}</span>
                        </button>
                    </div>
                </form>

                <button class="ps-close" onclick="closePopSheet('sheet1')"><img
                        src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="팝업시트 닫기"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->
@endsection

@push('script')
    <script>
        document.getElementById('file-upload').addEventListener('change', function(e) {
            var fileNames = [];
            var fileList = document.getElementById('file-list');
            fileList.innerHTML = '';

            for (var i = 0; i < this.files.length; i++) {
                fileNames.push(this.files[i].name);
            }

            document.getElementById('file-names').value = fileNames.join(', ');
        });

        function removeFile(button, index) {
            var input = document.getElementById('file-upload');
            var dt = new DataTransfer();

            for (var i = 0; i < input.files.length; i++) {
                if (index !== i)
                    dt.items.add(input.files[i]);
            }

            input.files = dt.files;

            button.parentNode.remove();

            var fileNames = Array.from(input.files).map(f => f.name);
            document.getElementById('file-names').value = fileNames.join(', ');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('askCont');
            const charCount = document.querySelector('.cnt-num');
            const maxLength = 1000;

            function updateCharCount() {
                const currentLength = textarea.value.length;
                charCount.textContent = currentLength;

                if (currentLength > maxLength) {
                    textarea.value = textarea.value.slice(0, maxLength);
                    charCount.textContent = maxLength;
                }
            }

            textarea.addEventListener('input', updateCharCount);

            // 초기 로드 시 글자 수 업데이트
            updateCharCount();
        });
    </script>
@endpush
