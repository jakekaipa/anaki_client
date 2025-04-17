@extends('frontend.layouts.auth')

@php
    $defualt = get_default_language_code() ?? 'en';
    $auth_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $auth = App\Models\Admin\SiteSections::getData($auth_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();

@endphp

@section('content')
    <div class="full-container flex-c-c f8f8f8">

        <div class="join2">
            <div class="form-box" style="display: flex; justify-content: center; align-items: center;">
                <div class="find-pi">
                    <div class="join-head">
                        <h1 class="join-tit">{{ __('Set New Password') }}</h1>
                        <p class="join-tit-sub">{{ __('Reset Sub Heading') }}.</p>
                    </div>
                    <form class="account-form" action="{{ setRoute('user.password.reset', $token) }}" method="POST">
                        @csrf

                        <div class="i-txt password w-full">
                            <label for="pw" class="i-txt__label">비밀번호</label>
                            <div class="i-txt__wrap">
                                <input type="password" id="new-pw" name="password" class="i-txt__input"
                                    title="영어 대소문자, 숫자, 특수문자 중 2종류 조합의 8-15자" placeholder="비밀번호를 입력해 주세요">
                                <button type="button" id="new-pw-button"  class="i-txt__btn-look" title="비밀번호 보기 실행">
                                    <span class="txt blind">비밀번호 보기</span>
                            </div>
                        </div>

                        <div class="i-txt password w-full">
                            <label for="pw2" class="i-txt__label">비밀번호 확인</label>
                            <div class="i-txt__wrap">
                                <input type="password" id="confirm-pw" name="password_confirmation" class="i-txt__input"
                                    title="비밀번호를 다시 입력해 주세요" placeholder="비밀번호를 다시 입력해 주세요">
                                <button type="button" id="confirm-pw-button" class="i-txt__btn-look" title="비밀번호 보기 실행">
                                    <span class="txt blind">비밀번호 보기</span>
                                </button>
                            </div>
                        </div>

                        <div class="btn-wrap join-done flex-c-s">
                            <button type="submit" class="btn-solid small">
                                <span class="txt">{{ __('Reset Password') }}</span>
                            </button>
                        </div>
                    </form>

                    <div class="go-login flex-c-s">
                        <a href="#" class="info-txt tdu">로그인 화면으로 이동</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // 비밀번호 보기/숨기기 토글
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

            togglePasswordVisibility('new-pw', 'new-pw-button');
            togglePasswordVisibility('confirm-pw', 'confirm-pw-button');
        });
    </script>
@endpush
