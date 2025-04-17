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
        <div class="pop-sheet" data-pop-sheet="sheet1">
            <div class="ps-inner">
                <div class="ps-container">
                    <div class="ps-head">
                        <h2 class="ps-tit">{{ __('Security Verification') }}</h2>
                    </div>

                    <div class="ps-content custom-scroll trans">
                        <div class="sa-wrap">
                            <div class="sa">
                                <p class="sa-txt1">Google 2FA Code</p>

                                <form id="twoFactorForm" action="{{ setRoute('user.authorize.google.2fa.submit') }}"
                                    method="POST">
                                    @csrf
                                    <div class="sa-inp-row">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(1)">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(2)">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(3)">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(4)">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(5)">
                                        <input type="text" class="sa-inp otp" name="code[]" maxlength="1"
                                            oninput="digitValidate(this)" onkeyup="tabChange(6)"
                                            onblur="submitIfComplete()">
                                    </div>
                                </form>

                                <p class="sa-txt2">{{ __('Enter the 6-digit code from Google Authenticator.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const digitValidate = function(ele) {
            ele.value = ele.value.replace(/[^0-9]/g, '');
        }

        const tabChange = function(val) {
            let ele = document.querySelectorAll('.otp');
            if (ele[val - 1].value !== '') {
                if (val < ele.length) {
                    ele[val].focus();
                } else if (val === ele.length) {
                    ele[val - 1].blur();
                    submitIfComplete();
                }
            } else {
                if (val > 1) {
                    ele[val - 2].focus();
                }
            }
        }

        const submitIfComplete = () => {
            let ele = document.querySelectorAll('.otp');
            let complete = true;
            for (let i = 0; i < ele.length; i++) {
                if (ele[i].value === '') {
                    complete = false;
                    break;
                }
            }
            if (complete) {
                document.getElementById('twoFactorForm').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            openPopSheet('sheet1');

            document.querySelector('.otp').focus();
        });
    </script>
@endpush
