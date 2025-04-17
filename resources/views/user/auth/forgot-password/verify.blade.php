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
        <div class="pop-sheet" data-pop-sheet="sheet1">
            <div class="ps-inner">
                <div class="ps-container">
                    <div class="ps-head">
                        <h2 class="ps-tit">{{ __('please Enter The Code') }}</h2>
                    </div>

                    <div class="ps-content custom-scroll trans">
                        <div class="sa-wrap">
                            <div class="sa">
                                <p class="sa-txt1">{{ __('We sent a 6 digit code here') }} <span
                                        class="text--base">{{ $user_email }}</span></p>

                                <form id="account-form" action="{{ setRoute('user.password.forgot.verify.code', $token) }}"
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

                                    <p class="time-area sa-txt2">{{ __("Didn't get the code?") }} <span
                                            id="time"></span></p>
                                </form>

                                <div class="sa-txt2">
                                    <label>{{ __('Already Have An Account?') }} <a href="{{ setRoute('user.login') }}"
                                            class="account-control-btn">{{ __('Login Now') }}</a></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            End Login
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
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
                document.getElementById('account-form').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            openPopSheet('sheet1');
            document.querySelector('.otp').focus();
        });

        var resendTime = "{{ $resend_time ?? 0 }}";
        var resendCodeLink = "{{ setRoute('user.password.forgot.resend.code',$token) }}";

        function resetTime(second = 20) {
            var coundDownSec = second;
            var countDownDate = new Date();
            countDownDate.setMinutes(countDownDate.getMinutes() + 120);
            var x = setInterval(function() { // Get today's date and time
                var now = new Date().getTime(); // Find the distance between now and the count down date
                var distance = countDownDate -
                    now; // Time calculations for days, hours, minutes and seconds  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * coundDownSec)) / (1000 * coundDownSec));
                var seconds = Math.floor((distance % (1000 * coundDownSec)) /
                    1000); // Output the result in an element with id="time"
                document.getElementById("time").innerHTML = second +
                    "s "; // If the count down is over, write some text
                if (distance <= 0 || second <= 0) {
                    // alert();
                    clearInterval(x);
                    // document.getElementById("time").innerHTML = "RESEND";
                    document.querySelector(".time-area").innerHTML =
                        `{{ __("Didn't get the code?") }} <a class='text--danger' href='${resendCodeLink}'>{{ __('resend') }}</a>`;
                }
                second--
            }, 1000);
        }
        resetTime(resendTime);
    </script>
@endpush
