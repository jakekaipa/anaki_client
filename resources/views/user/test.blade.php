@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

<style>
.flex-container {
    display: flex;
    justify-content: flex-start; /* 섹션들을 왼쪽 정렬로 배치 */
    gap: 10px; /* 두 섹션 간의 간격을 설정 */
}

.card-box, .offer3-inner {
    width: 48%; /* 섹션 너비를 48%로 설정하여 두 섹션이 가깝게 배치되도록 */
}

.offer3-inner {
    margin-left: 10px; /* offer3-inner 왼쪽에 여백을 추가해 조정 */
}

.pc-btn {
    display: flex;
    justify-content: center; /* 버튼을 가로로 중앙 정렬 */
    align-items: center; /* 버튼을 세로로 중앙 정렬 */
    margin-top: 20px; /* 필요에 따라 버튼 위쪽에 여백 추가 */
}

.process-btn {
    /* 버튼 스타일 추가 (예시) */
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 20px;
}

.process-btn:hover {
    background-color: #0056b3;
}

@media (max-width: 768px) {
    .flex-container {
        flex-direction: column; /* 작은 화면에서는 세로로 쌓이도록 설정 */
    }

    .card-box, .offer3-inner {
        width: 100%; /* 모바일에서는 전체 너비로 설정 */
    }
}
</style>
@section('content')
<div id="Phase2" class="content offer offer2">
    <h2 class="page-tit" id="phase2Title"></h2>
    <form id='submitForm'>
    @csrf
    <input type='hidden' name='tradeOfferId' value="{{ $tradeOfferId }}">
    <div class="container-row">
        <!-- Flex container for left and right sections -->
        <div class="flex-container">
            <!-- Left Section (Price Selection) -->
            <div class="card-box border">
                <h3 class="step-tit">{{ __('Select the price you want to use') }}</h3>
                <div>
                    <div class="select-price">
                        <button type="button" class="sp-col btn-radio active">
                            <div class="radio">
                                <input type="radio" class="radio-inp" id="priceType1" name="priceType"
                                    value="0" {{ ($tradeInfo->priceType == 0)? "checked":"" }}  >
                                <label for="priceType1">{{ __('Market Price') }}</label>
                            </div>
                            <p class="sptxt">
                                {{ __('The selling price of the transaction will change according to fluctuations in the Tether market price.') }}
                            </p>
                        </button>
                        <button type="button" class="sp-col btn-radio">
                            <div class="radio">
                                <input type="radio" class="radio-inp" id="priceType2" name="priceType"
                                    value="1" {{ ($tradeInfo->priceType == 1)? "checked":"" }} >
                                <label for="priceType2">{{ __('Fixed Price') }}</label>
                            </div>
                            <p class="sptxt">
                                {{ __('The selling price of the transaction is fixed and remains constant regardless of market price fluctuations.') }}
                            </p>
                        </button>
                    </div>

                    <div class="dt-row">
                        <!-- 최소 금액 입력란 -->
                        <div class="dt-col">
                            <label for="price" class="dt-label">{{ __('Set Transaction Amount') }}</label>
                            <label class="dt-label"style="margin-left: 10px; color: rgb(83,126,238)">{{ __('Min. 15,000원 ~ Max. 제한없음') }}</label>
                            <div class="dt-inp">
                                <input type="text" id="price" class="input" placeholder="{{ __('Minimum Amount') }}" name="tradeVolMin"
                                    value='{{ number_format($tradeInfo->tradeVolMin) }}'>
                                <span class="unit">{{ __('KRW') }}</span>
                            </div>
                        </div>

                        <!-- 대시 (구분선) -->
                        <span class="dash" style="font-size: 20px;">-</span>

                        <!-- 최대 금액 입력란 -->
                        <div class="dt-col">
                            <div class="dt-inp">
                                <input type="text" id="receipt" class="input" placeholder="{{ __('Maximum Amount') }}" name="tradeVolMax"
                                    value='{{ number_format($tradeInfo->tradeVolMax) }}'>
                                <span class="unit">{{ __('KRW') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 시장가격 --> 
                    <div class="section" id="MarketPriceGroup">
                        <h4 class="section-tit">{{ __('Transaction Margin') }}</h4>

                        <div class="setting-inp money">
                            <input type="text" class="si-inp inp-m" name="offerMargin" value="{{ ($tradeInfo->offerMargin)? $tradeInfo->offerMargin : 5 }}"
                                id="offerMargin" readonly>
                            <span class="si-display">%</span>
                            <button type="button" class="btn-set decrease" id="margin-decrease">-</button>
                            <button type="button" class="btn-set increase" id="margin-increase">+</button>
                        </div>
                        <p class="setting-txt">{{ __('Current Tether Market Price:') }}
                            <strong>{{ number_format($prices['KRW'], 2) }} {{ __('KRW') }}</strong> <!--  {{ number_format($prices['KRW'], 2) }} {{ __('KRW') }} -->
                        </p>
                    </div>

                    <!-- 고정가격 -->
                    <div class="section" id="FixedPriceGroup">
                        <h4 class="section-tit">{{ __('Fixed price') }}</h4>

                        <div class="setting-inp money">
                            <input type="number" class="si-inp inp-m" value="{{ ($tradeInfo->fixedPrice)? intval($tradeInfo->fixedPrice) : $prices['KRW'] }}" 
                                name="fixedPrice" id="fixedPrice" readonly>
                            <button type="button" class="btn-set decrease" id="price-decrease">-</button>
                            <button type="button" class="btn-set increase" id="price-increase">+</button>
                        </div>
                        <p class="setting-txt">{{ __('Current Tether Market Price:') }}
                            <strong> {{ number_format($prices['KRW']), 2}} {{ __('KRW') }}</strong>
                        </p>
                    </div>

                    <div class="section">
                        <h4 class="section-tit">{{ __('Transaction Time Limit') }}</h4>

                        <div class="setting-inp time">
                            <input type="text" class="si-inp inp-s" value="{{ ($tradeInfo->offerTimeLimit)? $tradeInfo->offerTimeLimit : 20 }}" name="offerTimeLimit"
                                id="offerTimeLimit">
                            <span class="si-display">{{ __('minutes') }}</span>
                            <button type="button" class="btn-set decrease">-</button>
                            <button type="button" class="btn-set increase">+</button>
                        </div>

                        <p class="setting-txt">
                            {{ __('The transaction will be automatically cancelled after the set time for possible transactions has passed. (Minimum input is 20 minutes)') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Section (Other Options) -->
            <div class="offer3-inner">
                <div class="section">
                    <div class="checkbox aic">
                        <input type="checkbox" name="secretTrade" id="secretTrade" class="checkbox__chk"
                           {{ ($tradeInfo->password != null)? 'checked':'' }}>
                        <label for="secretTrade"
                            class="checkbox__label section-tit">{{ __('Private Trade') }}</label>
                        <label class="" style="margin-left: 10px;">{{ __('비밀번호 잠금기능') }}</label>
                    </div>
                    <div class="input" name="passwordGroup" id="passwordGroup">
                        <div class="i-txt password w-full">
                            <div class="i-txt__wrap">
                                <input type="password" class="i-txt__input" placeholder="{{ __('Pin Number') }}"
                                    name="password" id="password" maxlength="4" pattern="\d{4}"
                                    inputmode="numeric" onkeypress="return onlyNumberKey(event)"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                    value='{{ $tradeInfo->password }}'
                                    >
<!-- 
                                <button type="button" class="i-txt__btn-look" title="{{ __('비밀번호 보기 실행') }}">
                                    <span class="txt blind">{{ __('비밀번호 보기') }}</span>
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h4 class="section-tit">{{ __('Transaction Limit') }}</h4>
                    <div class="checkbox aic">
                        <input type="checkbox" name="checkboxKYCAuth" id="checkboxKYCAuth"
                            class="checkbox__chk" {{ ($tradeInfo->needKYCAuth == 1)? 'checked':'' }}>
                        <label for="checkboxKYCAuth"
                            class="checkbox__label">{{ __('Only partners who have completed KYC verification can trade') }}</label>
                    </div>
                    <div>  <span class="checkbox__note">{{ __('To prevent illegal funds and third-party fraud, we recommend trading with partners who have completed KYC certification') }}</span></div>
                </div>

                <div class="section">
                    <h4 class="section-tit required">{{ __('Transaction Title') }}</h4>
                    <div class="input">
                        <input type="text" size="10" name="offerLabel" id="offerLabel" value='{{ $tradeInfo->offerLabel }}'
                            class="inp-txt w-full" placeholder="{{ __('한글 10자 이내 입력') }}">
                    </div>
                </div>

                <div class="section">
                    <label for="dealtitle"
                        class="checkbox__label required section-tit">{{ __('Transaction Conditions') }}</label>
                    <div class="textarea">
                        <textarea name="offerCondition" id="offerCondition" class="w-full" style='height:30%'>{{ $tradeInfo->offerCondition }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <div class="pc-btn">
        <button type="button" class="process-btn" id="nextBtn" onclick="onSubmit()">
            <span class="txt">{{ __('Update') }}</span>
        </button>
        <button type="button" class="process-btn" style='background-color:red'  id="nextBtn" " onclick="javascript:history.back();">
            <span class="txt">{{ __('Cancel') }}</span>
        </button>
    </div>
</div>

@endsection

@push('css')
    <style>
        .conditions-textarea-wrapper {
            max-height: 200px; /* 원하는 최대 높이로 조정하세요 */
            overflow-y: auto;
            border: 1px solid #ccc; /* 테두리를 추가하여 스크롤 영역을 시각적으로 구분합니다 */
            padding: 5px;
        }

        #conditionsTextarea {
            width: 100%;
            min-height: 100px;
            max-height: none; /* max-height 제거 */
            height: auto; /* 높이를 자동으로 조정 */
            resize: none;
            border: none;
            background-color: transparent;
            font-family: inherit;
            font-size: inherit;
            color: inherit;
            cursor: default;
            user-select: text; /* 텍스트 선택 가능하도록 변경 */
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            pointer-events: auto; /* 포인터 이벤트 활성화 */
            overflow-y: visible; /* 내용이 넘칠 경우 스크롤 허용 */
        }

        #conditionsTextarea:focus {
            outline: none;
        }

        /* 스크롤바 스타일링 (선택 사항) */
        .conditions-textarea-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .conditions-textarea-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .conditions-textarea-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .conditions-textarea-wrapper::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .checkbox-container {
            display: flex;
            flex-direction: column;
        }

        .checkbox__note {
            margin-top: 8px; /* 체크박스와 텍스트 간의 간격 */
            font-size: 14px;
            color: blue; /* 텍스트 색상 */
        }

    </style>
@endpush

@push('script')
<script>
    var phase = 2;
    const elements = {
        Phase2: document.getElementById('Phase2'),
        FixedPriceGroup: document.getElementById('FixedPriceGroup'),
        conditionsTextarea: document.getElementById('conditionsTextarea')
       
    };
    const form = document.querySelector('form[action="{{ setRoute('user.make-offer.submit') }}"]');
    
    function refreshPhase() {
        if (phase === 2) {
            elements.Phase2.style.display = "";
            const action = document.querySelector('input[name="action"]:checked').value;
            const phase2Title = document.getElementById('phase2Title');
            phase2Title.textContent = action === 'buy' 
                ? "{{ __('How much would you like to buy?') }}" 
                : "{{ __('How much would you like to sell?') }}";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        // 가격타입 변경 함수
        document.querySelectorAll(".select-price .btn-radio").forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const radio = this.querySelector("input[type='radio']");
                radio.checked = true;

                document.querySelectorAll(".select-price .btn-radio").forEach(button => {
                    button.classList.remove("active");
                });
                this.classList.add("active");
                changePriceType(radio.value);
            });
        });

        // 가격타입 변경 처리
        function changePriceType(priceType) {
            if (priceType == 0) {
                document.getElementById('MarketPriceGroup').style.display = "";
                document.getElementById('FixedPriceGroup').style.display = "none";
            } else {
                document.getElementById('MarketPriceGroup').style.display = "none";
                document.getElementById('FixedPriceGroup').style.display = "";
            }
        }
        
         // 버튼 클릭 이벤트 처리
         $(".select-price .btn-radio").click(function(e) {
                e.preventDefault(); // 버튼의 기본 동작 막기

                // 라디오 버튼 선택
                var radio = $(this).find("input[type='radio']");
                radio.prop("checked", true);

                // active 클래스 처리
                $(".select-price .btn-radio").removeClass("active");
                $(this).addClass("active");

                // PriceType 변경 함수 호출
                changePriceType(radio.val());
            });

        // 초기 상태 설정
        changePriceType($("input[name='priceType']:checked").val());

        // // 비밀번호 보기/숨기기
        // document.querySelector('.i-txt__btn-look').addEventListener('click', function() {
        //     const passwordInput = document.querySelector('input[name="password"]');
        //     passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        //     this.classList.toggle('active');
        // });
    });

     // 테더 가격 선택
     $('.btn-radio').click(function() {
            $('.btn-radio').removeClass('active'); // 모든 버튼에서 'active' 클래스 제거
            $(this).addClass('active'); // 현재 클릭한 버튼에 'active' 클래스 추가
            $(this).find('.radio-inp').prop('checked', true); // 클릭한 버튼의 라디오 버튼 선택
    });

    // 거래 시장가격 마진
    function changeOfferMargin(delta) {
        var currentValue = parseFloat($('#offerMargin').val()) || 0;
        var newValue = Math.round((currentValue + delta) * 10) / 10;
        
        // 옵션 1: 0 이상의 값만 허용 (기존 로직 유지)
        if(newValue < 0)
        {
            alert("{{ __('최저 퍼센트 입니다') }}");
            return false;
        }
        if (newValue >= 0) {
            $('#offerMargin').val(newValue.toFixed(1));
        }
    }

    // $('#margin-decrease').click(
    // 감소 버튼
    document.querySelector('#margin-decrease').addEventListener('click', function() {
        changeOfferMargin(-0.1);
    });

    // $('#margin-increase').click(function() {
    // 증가 버튼
    document.querySelector('#margin-increase').addEventListener('click', function() {
        changeOfferMargin(0.1);
    });

    // 거래 고정 가격
    function changeFixedPrice(delta) {
        var currentValue = parseInt($('#fixedPrice').val(), 10);
        var newValue = currentValue + delta;
        
        if(newValue < 0 )
        {
            alert("{{ __('최저 금액 입니다') }}");
            return false;
        }
        if (newValue >= 0) {
            $('#fixedPrice').val(newValue);
        }
    }

    // $('#margin-decrease').click(
    // 고정가격 감소
    document.querySelector('#price-decrease').addEventListener('click', function() {
        changeFixedPrice(-1);
    });

    // $('#margin-increase').click(function() {
    // 고정거래 증가
    document.querySelector('#price-increase').addEventListener('click', function() {
        changeFixedPrice(+1);
    });

    // 거래 시간 제한
    function changeTime(delta) {
        var currentValue = parseInt($('.inp-s').val(), 10);
        var newValue = currentValue + delta;
        if (newValue >= 20) {
            $('.inp-s').val(newValue);
        }
    }

    $('.time .decrease').click(function() {
        changeTime(-5);
    });

    $('.time .increase').click(function() {
        changeTime(+5);
    });

    // 숫자 입력만 받기
    function onlyNumberKey(evt) {
        const keyCode = evt.which || evt.keyCode;
        return keyCode >= 48 && keyCode <= 57;
    }

    // 툴팁 처리
    $('.tooltip').tooltipPlugin();

    function onSubmit(){
        const form = document.getElementById('submitForm');
        const secretTradeCheckbox = document.getElementById('secretTrade');
        const password =  document.getElementById('password').value;
        if(secretTradeCheckbox.checked){
            if(!password){
                alert("{{ __('Enter 4-digit password') }}")
                return false;
            }
        }
        
         // 폼 데이터를 JSON으로 변환
        const formData = new FormData(form);
        const jsonData = {};
        formData.forEach((value, key) => {
            if(key === 'tradeVolMin' || key === 'tradeVolMax' )
            {
                jsonData[key] = value.replace(/\,/g, '');
            } else {
                jsonData[key] = value;
            }
        });

        fetch('/updateMakeOffer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            alert("{{ __('업데이트 성공하였습니다') }}");
            location.reload();
        })
        .catch(error => {
            alert("{{ __('Something went wrong.') }}");
        });

    }


</script>
@endpush