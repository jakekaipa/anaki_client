@extends('frontend.layouts.auth')
@php
    $defualt = get_default_language_code() ?? 'en';
    $login_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $login = App\Models\Admin\SiteSections::getData($login_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
@endphp
@section('content')
    <div class="full-container flex-c-c f8f8f8">
        <div class="find-pi">
            <div class="join-head">
                <h1 class="join-tit">{{ __('Find Password') }}</h1>
                <p class="join-tit-sub">{{ __('We will send a password reset link to your email.') }}</p>
            </div>

            <form class="account-form" action="{{ setRoute('user.password.forgot.send.code') }}" method="POST">
                @csrf
                <div class="i-txt w-full">
                    <div class="i-txt__wrap">
                        <input type="email" class="i-txt__input" name="credentials" title="{{ __('Enter your email') }}"
                            placeholder="{{ __('Enter your email') }}" required>
                        <button type="button" class="i-txt__del" title="{{ __('Delete input') }}"><img
                                src="{{ asset('/public/pub') }}/img/elimination@2x.png"></button>
                    </div>
                </div>

                <div class="btn-wrap find-btn flex-c-s">
                    <button type="submit" class="btn-other w-full">
                        <span class="txt">{{ __('Find Password') }}</span>
                    </button>
                </div>
            </form>

            <div class="go-login flex-c-s">
                <a href="{{ setRoute('user.login') }}" class="info-txt tdu">{{ __('Back to login screen') }}</a>
            </div>
        </div>
    </div><!-- //container End -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.i-txt__del').on('click', function() {
                $(this).siblings('input').val('');
            });
        });
    </script>
@endpush
