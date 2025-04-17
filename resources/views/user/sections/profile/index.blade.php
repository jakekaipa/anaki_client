@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

<style>

    .flex-s-s {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        max-width: 260px; /* 한 줄에 최대 3개 */
    }

    .mpi-txt2 {
        margin: 0;
    }

    .btn-default.normal {
        width: 100px;
    }
</style>

@section('content')
    <div class="container mypage">
        <h2 class="page-tit">마이페이지</h2>

        <div class="container-row">
            <div class="sub-box">
                <div class="mp-top">
                    <div class="profile">
                        <div class="p-img-wrap">
                            <div class="p-img"><img src="{{ $user->image }}" style='width:50%;' alt="프로필 이미지">
                            </div>
                            <!-- <button class="p-mod"><img src="{{ asset('/public/pub') }}/img/photo-up@2x.png"
                                    alt="수정하기"></button> -->
                        </div>

                        <div class="p-info">
                            <p class="acc">{{ $user->email }}</p>
                            <p class="name">{{ $user->username }}</p>
                        </div>
                    </div>

                    <button class="btn-default normal" onclick="openPopSheet('sheet-user-info');">
                        <span class="txt">회원정보 수정</span>
                    </button>
                </div>

                <div style='height:10px'></div>
                <div class="mp-mo-box">
                    <!-- 가입링크 생성-->
                    @if(isset($referralInfo))
                    <div class="container-row">
                        <div class="card-box">
                            <div class="mp-info">
                                <p class="mpi-txt1">레퍼럴 가입 링크</p>
                                <p style='height:10px'></p>
                                <!-- Flex 컨테이너 -->
                                <div class="flex-s-s" style="display: flex; flex-wrap: wrap; gap: 5px 10px; max-width: 280px;">
                                    @for($i = ($referralInfo->fee_rate * 10) + 1; $i <= 8; $i++)
                                        <p class="mpi-txt2" style="margin: 0;">
                                            <button type="button" class="btn-default normal" style="width: 85px;" onclick="copyToClipboard('{{ $referralInfo->referral_code.'_'.$i }}')">
                                                <span class="txt">0.{{ $i }}% 수수료</span>
                                            </button>
                                        </p>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- <div class="container-row">
                            <div class="card-box">
                                <div class="mp-info">
                                <p class="mpi-txt1">레퍼럴 가입 링크</p>
                                    <div style='height:5px'></div>
                                    <div class="flex-s-s" style="display: flex; flex-direction: column; gap: 10px;">
                                        @for($i = ($referralInfo->fee_rate * 10) + 1; $i <= 8; $i++)
                                        <button type="button" class="btn-default normal" style="width:100%;height:35px"  class="btn-del" onclick="copyToClipboard('{{ $referralInfo->referral_code.'_'.$i }}')">
                                                <span class="txt">0.{{ $i }}% 수수료 </span>
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                            </div> -->
                        
                    @endif
                    
                    <div style='height:15px'></div>

                    <div class="container-row card-box-wrap top">
                        <div class="card-box">
                            <div class="mp-info">
                                <p class="mpi-txt1">입출금 계좌</p>
                                <div class="flex-s-s">
                                    @if (count($bankinfoData) > 0)
                                        @foreach ($bankinfoData as $bankinfo)
                                            <p class="mpi-txt2">
                                                {{ $bankinfo->bankName }}
                                                ({{ substr($bankinfo->accountNumber, 0, 8) . str_repeat('*', strlen($bankinfo->accountNumber) - 8) }})
                                                <button type="button" class="btn-del" title="삭제하기"
                                                    data-index="{{ $bankinfo->index }}"
                                                    onclick="deleteBankAccount({{ $bankinfo->index }})">
                                                    <img src="{{ asset('/public/pub') }}/img/delete@2x.png" alt="삭제">
                                                </button>
                                            </p>
                                        @endforeach
                                    @else
                                        <p>등록된 계좌가 없습니다.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-row">
                        <div class="card-box">
                            <div class="mp-info">
                                <p class="mpi-txt1">내 카카오페이</p>

                                <div class="flex-s-s">
                                    @if (count($payqrData) > 0)
                                        @foreach ($payqrData as $payQrInfo)
                                            <p class="mpi-txt2">
                                                {{ $payQrInfo->payTag }}
                                                <button type="button" class="btn-del" title="삭제하기"
                                                    data-index="{{ $payQrInfo->index }}"
                                                    onclick="deletePayQr({{ $payQrInfo->index }})">
                                                    <img src="{{ asset('/public/pub') }}/img/delete@2x.png" alt="삭제">
                                                </button>
                                            </p>
                                        @endforeach
                                    @else
                                        <p>등록된 계좌가 없습니다.</p>
                                    @endif
                                    {{-- <p class="mpi-txt2">
                                        카카오페이 거래내역

                                        <a href="#" class="link">Jake</a>

                                        <button type="button" class="btn-del" title="삭제하기"><img
                                                src="{{ asset('/public/pub') }}/img/delete@2x.png"></button>
                                    </p>

                                    <p class="mpi-txt2">
                                        <a href="#" class="link">KakaoTalk_20240813_030409500.jpg</a>

                                        <button type="button" class="btn-del" title="삭제하기"><img
                                                src="{{ asset('/public/pub') }}/img/delete@2x.png"></button>
                                    </p> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-row btn-mp">
                        <button class="btn-default cancel normal" onclick="deactivateUser();"><span
                                class="txt">회원탈퇴</span></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- pop-sheet -->
        <div class="pop-sheet" data-pop-sheet="sheet-user-info">
            <div class="ps-inner">
                <div class="ps-container">
                    <div class="ps-head">
                        <h2 class="ps-tit">회원정보 수정</h2>
                    </div>

                    <div class="ps-content custom-scroll trans">
                        <div class="mp-mod">
                            <div class="i-txt__wrap">
                                <label for="nickname" class="i-txt__label">닉네임</label>
                                <div class="i-txt__box">
                                    <input type="text" id="nickname" class="i-txt__input mod" title="닉네임 수정"
                                        value="{{ $user->username }}">
                                    <button type="button" class="i-txt__del" title="입력내용 삭제"><img
                                            src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                                </div>
                            </div>

                            <div class="i-txt__wrap">
                                <label for="mod2" class="i-txt__label">이름</label>
                                <div class="i-txt__box">
                                    <input type="text" id="mod2" class="i-txt__input" title="이름 수정"
                                        value="{{ $user->realname ?? '' }}" disabled>
                                    <button type="button" class="i-txt__del" title="입력내용 삭제"><img
                                            src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                                </div>
                            </div>

                            <div class="i-txt__wrap">
                                <label for="mod3" class="i-txt__label">휴대폰 번호</label>
                                <div class="i-txt__box">
                                    <input type="text" id="mod3" class="i-txt__input mod" title="전화번호 수정"
                                        value="{{ $user->mobile ?? '' }}" disabled>
                                    <button type="button" class="i-txt__del" title="입력내용 삭제"><img
                                            src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                                </div>
                            </div>

                            <div class="i-txt__wrap">
                                <label for="mod4" class="i-txt__label">이메일</label>
                                <div class="i-txt__box">
                                    <input type="text" id="mod" class="i-txt__input mod" title="이메일 수정"
                                        value="{{ $user->email }}" disabled>
                                    <button type="button" class="i-txt__del" title="입력내용 삭제"><img
                                            src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                                </div>
                            </div>

                            <!-- <form id="changePasswordForm" method="POST"
                                action="{{ setRoute('user.profile.password.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="i-txt__wrap">
                                    <span class="i-txt__label">비밀번호 변경</span>
                                </div>

                                <div class="i-txt__wrap">
                                    <label for="current-pw" class="i-txt__label small">현재 비밀번호</label>
                                    <div class="i-txt__box">
                                        <input type="password" id="current-pw" name="current_password"
                                            class="i-txt__input" title="비밀번호 입력하세요" placeholder="비밀번호" required>
                                        <button type="button" id="current-pw-view" class="i-txt__btn-look"
                                            title="비밀번호 보기 실행">
                                            <span class="txt blind">비밀번호 보기</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="i-txt__wrap">
                                    <label for="new-pw" class="i-txt__label small">새 비밀번호</label>
                                    <div class="i-txt__box">
                                        <input type="password" id="new-pw" name="password" class="i-txt__input"
                                            title="비밀번호 입력하세요" placeholder="비밀번호" required>
                                        <button type="button" id="new-pw-view" class="i-txt__btn-look"
                                            title="비밀번호 보기 실행">
                                            <span class="txt blind">비밀번호 보기</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="i-txt__wrap">
                                    <label for="confirm-pw" class="i-txt__label small">비밀번호 다시 입력</label>
                                    <div class="i-txt__box">
                                        <input type="password" id="confirm-pw" name="password_confirmation"
                                            class="i-txt__input" title="비밀번호 입력하세요" placeholder="비밀번호" required>
                                        <button type="button" id="confirm-pw-view" class="i-txt__btn-look"
                                            title="비밀번호 보기 실행">
                                            <span class="txt blind">비밀번호 보기</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="btn-mod-wrap">
                                    <button type="submit" class="btn-mod">변경</button>
                                </div>
                            </form> -->
                        </div>
                    </div>

                    <div class="ps-process">
                        <button class="ps-process-btn" onclick="changeUsername();">
                            <span class="txt">수정하기</span>
                        </button>
                    </div>

                    <button class="ps-close" onclick="closePopSheet('sheet-user-info')"><img
                            src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="팝업시트 닫기"></button>
                </div>
            </div>
        </div>
        <!-- pop-sheet -->

        <form id="deleteBankAccountForm" method="POST" action="{{ route('user.profile.account.delete') }}"
            style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="index" id="deleteAccountIndex">
        </form>

        <form id="deletePayQrForm" method="POST" action="{{ route('user.profile.qr.delete') }}"
            style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="index" id="deletePayQrIndex">
        </form>

        <form id="changeUsername" method="POST" action="{{ setRoute('user.profile.usename.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="username" id="newNickname">
        </form>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 비밀번호 보기/숨기기 기능
            function togglePasswordVisibility(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                button.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        button.innerHTML = '<span class="txt blind">비밀번호 숨기기</span>';
                    } else {
                        input.type = 'password';
                        button.innerHTML = '<span class="txt blind">비밀번호 보기</span>';
                    }
                });
            }

            togglePasswordVisibility('current-pw', 'current-pw-view');
            togglePasswordVisibility('new-pw', 'new-pw-view');
            togglePasswordVisibility('confirm-pw', 'confirm-pw-view');

            // 폼 제출 처리
            const form = document.getElementById('changePasswordForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const newPassword = document.getElementById('new-pw').value;
                const confirmPassword = document.getElementById('confirm-pw').value;

                if (newPassword !== confirmPassword) {
                    alert(`{{ __('새 비밀번호와 확인 비밀번호가 일치하지 않습니다.') }}`);
                    return;
                }

                // 여기에서 폼을 제출합니다.
                this.submit();
            });
        });

        function deactivateUser() {
            const message =
                `Are you sure you want to delete your account?`;

            if (confirm(message)) {
                // 사용자가 확인을 클릭한 경우
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.delete.account') }}";

                // CSRF 토큰 추가
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                // DELETE 메소드 지정
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // 필요한 경우 추가 필드 삽입
                const targetField = document.createElement('input');
                targetField.type = 'hidden';
                targetField.name = 'target';
                targetField.value = '1';
                form.appendChild(targetField);

                // form을 body에 추가하고 제출
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteBankAccount(index) {
            if (confirm(`{{ __('정말로 이 계좌를 삭제하시겠습니까?') }}`)) {
                document.getElementById('deleteAccountIndex').value = index;
                document.getElementById('deleteBankAccountForm').submit();
            }
        }

        function deletePayQr(index) {
            if (confirm(`{{ __('정말로 이 계좌를 삭제하시겠습니까?') }}`)) {
                document.getElementById('deletePayQrIndex').value = index;
                document.getElementById('deletePayQrForm').submit();
            }
        }

        function changeUsername() {
            if (confirm(`{{ __('정말로 닉네임을 변경하시겠습니까?') }}`)) {
                const username = document.getElementById('nickname').value;
                document.getElementById('newNickname').value = username;
                document.getElementById('changeUsername').submit();
            }
        }

        function copyToClipboard(text){
            // 회원가입 링크
            var signUpUrl = "https://anakip2p.com/register";
            const textarea = document.createElement('textarea');
            textarea.value = signUpUrl+'/'+text;  
            document.body.appendChild(textarea);
            textarea.select();  // 텍스트 선택
            document.execCommand('copy');  // 복사 명령 실행
            document.body.removeChild(textarea);  // 임시 textarea 제거

            // 복사 완료 메시지 (선택 사항)
            alert('클립보드에 복사되었습니다.');
        }
    </script>
@endpush
