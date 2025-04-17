@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <form action="{{ setRoute('user.make-offer.submit') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('PUT')

        <div id="Phase1" class="content offer offer1">
            <h2 class="page-tit">{{ __('Create Tether Trade') }}</h2>

            <div class="container-row">
                <div class="card-box border">
                    <div class="step">
                        <div class="step-chip active">
                            <span class="step-num">①</span>
                            <span class="step-txt">{{ __('Payment Method') }}</span>
                        </div>
                        <div class="step-chip">
                            <span class="step-num">②</span>
                            <span class="step-txt">{{ __('Price') }}</span>
                        </div>
                        <div class="step-chip">
                            <span class="step-num">③</span>
                            <span class="step-txt">{{ __('Other Settings') }}</span>
                        </div>
                    </div>

                    <h3 class="step-tit">{{ __('Select Cryptocurrency') }}</h3>

                    <div class="select-coin">
                        <div class="radio-el">
                            <input type="radio" id="coin1" class="coin-radio" checked name="coin" value="tether">
                            <label for="coin1" class="sc-label tether">
                                <span class="coin-txt">{{ __('Tether') }}</span>
                            </label>
                        </div>
                        <div class="radio-el">
                            <input type="radio" id="coin2" class="coin-radio" disabled name="coin"
                                value="usd-coin">
                            <label for="coin2" class="sc-label usd">
                                <span class="coin-txt">{{ __('USD Coin') }}</span>
                            </label>
                        </div>
                        <div class="radio-el">
                            <input type="radio" id="coin3" class="coin-radio" disabled name="coin" value="bitcoin">
                            <label for="coin3" class="sc-label bitcoin">
                                <span class="coin-txt">{{ __('Bitcoin') }}</span>
                            </label>
                        </div>
                        <div class="radio-el">
                            <input type="radio" id="coin4" class="coin-radio" disabled name="coin"
                                value="ethereum">
                            <label for="coin4" class="sc-label ed">
                                <span class="coin-txt">{{ __('Ethereum') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="section what-do">
                        <h4 class="section-tit">{{ __('What would you like to do?') }}</h4>

                        <ul class="what-do-list">
                            <li>
                                <div class="radio">
                                    <input type="radio" id="sellTether" class="radio-inp" name="action" value="sell"
                                        checked>
                                    <label for="sellTether" class="radio-label">{{ __('Sell Tether') }}</label>
                                </div>
                                <p class="info-txt small">
                                    {{ __('The transaction will be posted on the Tether purchase page.') }}</p>
                            </li>
                            <li>
                                <div class="radio">
                                    <input type="radio" id="buyTether" class="radio-inp" name="action" value="buy">
                                    <label for="buyTether" class="radio-label">{{ __('Buy Tether') }}</label>
                                </div>
                                <p class="info-txt small">
                                    {{ __('The transaction will be posted on the Tether selling page.') }}</p>
                            </li>
                        </ul>
                    </div>

                    <div class="section how">
                        <h4 class="section-tit">{{ __('Select Payment Method') }}</h4>

                        <ul class="how-list">
                            <li>
                                <div class="checkbox aic">
                                    <input type="checkbox" id="bankTrasfer" class="checkbox__chk" name="bankTrasfer"
                                        value="transfer">
                                    <label for="bankTrasfer" class="label-txt">
                                        <img src="{{ asset('/public/pub') }}/img/account-bank@2x.png" class="bank">
                                        {{ __('Bank Account Transfer') }}
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="checkbox aic">
                                    <input type="checkbox" id="kakaopay" class="checkbox__chk" name="kakaopay"
                                        value="kakaopay">
                                    <label for="kakaopay" class="label-txt">
                                        <img src="{{ asset('/public/pub') }}/img/qrCode.png" class="kakao">
                                        {{ __('Qr Pay') }}
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <p class="info-txt small">
                            {{ __('You can select both bank transfer and QR Pay at the same time.') }}
                        </p>
                    </div>

                    <div id="BankInfo" class="section b-transfer">
                        <h4 class="section-tit small">{{ __('계좌정보') }}</h4>

                        <div class="bt-row">
                            <div class="custom-select" id="bankInfoSelect">
                                <button type="button" class="cs-btn bank-cs-btn">
                                    <span class="dp-txt bank-dp-txt">{{ __('최근거래 은행 선택') }}</span>
                                </button>

                                <ul class="cs-list bank-cs-list">
                                    @forelse ($bankinfoData as $index => $bank)
                                        <li><a href="#" class="value"
                                                onclick="onSelectMyAccount({{ $index }}); return false;">{{ $bank->accountTag }}</a>
                                        </li>
                                    @empty
                                        <li>{{ __('No bank accounts available') }}</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="bt-row">
                            <div class="input">
                                <input type="text" class="inp-txt" placeholder="{{ __('별칭') }}"
                                    value="{{ $bankinfoData->isNotEmpty() ? $bankinfoData[0]->accountTag : '' }}"
                                    name="accountTag" id="accountTag">
                            </div>
                            <div class="input">
                                <input type="text" class="inp-txt" placeholder="{{ __('은행명') }}"
                                    value="{{ $bankinfoData->isNotEmpty() ? $bankinfoData[0]->bankName : '' }}"
                                    name="bankName" id="bankName">
                            </div>

                            <div class="input long">
                                <input type="text" class="inp-txt" placeholder="{{ __('계좌 번호') }}"
                                    value="{{ $bankinfoData->isNotEmpty() ? $bankinfoData[0]->accountNumber : '' }}"
                                    name="accountNumber" id="accountNumber">
                            </div>

                            <div class="input">
                                <input type="text" class="inp-txt" placeholder="{{ __('예금주') }}"
                                    value="{{ $bankinfoData->isNotEmpty() ? $bankinfoData[0]->accountHolder : '' }}"
                                    name="accountName" id="accountName">
                            </div>
                        </div>
                    </div>

                    <div id="KakaopayInfo" class="section kakao-qr">
                        <h4 class="section-tit small">{{ __('QR 페이 이미지') }}</h4>

                        <div class="qr-row">
                            <div class="add-file-row">
                                <input type="text" style="color: black;"
                                    value="{{ $payqrData->isNotEmpty() ? $payqrData[0]->payQR : '' }}" name="payQRUrl"
                                    id="payQRUrl" hidden />
                                <div class="input">
                                    <input type="hidden" class="inp-txt" placeholder="{{ __('별칭') }}"
                                        value="{{ $payqrData->isNotEmpty() ? $payqrData[0]->payTag : __('카카오뱅크') }}"
                                        name="payTag" id="payTag" style="width: 160px; margin-right: 10px;" />
                                </div>
                                <div class="input">
                                    <button type="button" class="btn-add-file" onclick="addFile();">{{ __('+ 파일 선택') }}</button>
                                </div>
                                <div class="input">
                                    <input type="file" class="inp-hide" onchange="handleFileChange(event);"
                                        name="payQRImage" id="payQRImage">
                                    <span class="guide-t">{{ __('QR 페이 이미지 선택하세요') }}</span>
                                </div>
                            </div>

                            <p class="file-name"></p>
                        </div>
                    </div>

                    <div class="pc-btn">
                        <button type="button" class="process-btn" id="nextBtn" onclick="onNextClick()">
                            <span class="txt">{{ __('Next') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="Phase2" class="content offer offer2">
            <h2 class="page-tit" id="phase2Title"></h2>

            <div class="container-row">
                <div class="card-box border">
                    <div class="step">
                        <div class="step-chip">
                            <span class="step-num">①</span>
                            <span class="step-txt">{{ __('Payment Method') }}</span>
                        </div>
                        <div class="step-chip active">
                            <span class="step-num">②</span>
                            <span class="step-txt">{{ __('Price') }}</span>
                        </div>
                        <div class="step-chip">
                            <span class="step-num">③</span>
                            <span class="step-txt">{{ __('Other Settings') }}</span>
                        </div>
                    </div>

                    <h3 class="step-tit">{{ __('Select the Tether price to use.') }}</h3>

                    <div>
                        <div class="select-price">
                            <button type="button" class="sp-col btn-radio active">
                                <div class="radio">
                                    <input type="radio" class="radio-inp" id="priceType1" name="priceType"
                                        value="MarketPrice" checked>
                                    <label for="priceType1">{{ __('Market Price') }}</label>
                                </div>
                                <p class="sptxt">
                                    {{ __('The sale price of the transaction will change based on the fluctuation of the Tether market price') }}
                                </p>
                            </button>
                            <button type="button" class="sp-col btn-radio">
                                <div class="radio">
                                    <input type="radio" class="radio-inp" id="priceType2" name="priceType"
                                        value="FixedPrice">
                                    <label for="priceType2">{{ __('Fixed Price') }}</label>
                                </div>
                                <p class="sptxt">
                                    {{ __('The sale price of the transaction will remain fixed regardless of market fluctuations') }}
                                </p>
                            </button>
                        </div>

                        <div class="dt-row">
                            <div class="dt-col">
                                <label for="price" class="dt-label">{{ __('Set Transaction Amount') }}</label>
                                <label class="dt-label"
                                    style="margin-left: 10px; color: rgb(83,126,238)">{{ __('Min. 15,000원 ~ Max. 제한없음') }}</label>
                                <div class="dt-inp">
                                    <input type="text" id="price" class="input"
                                        placeholder="{{ __('Minimum Amount') }}" value="100,000" name="tradeVolMin"
                                        id="tradeVolMin">
                                    <span class="unit">{{ __('KRW') }}</span>
                                </div>
                            </div>
                            <span class="dash">-</span>
                            <div class="dt-col">
                                <div class="dt-inp">
                                    <input type="text" id="receipt" class="input"
                                        placeholder="{{ __('Maximum Amount') }}" value="1,000,000" name="tradeVolMax"
                                        id="tradeVolMax">
                                    <span class="unit">{{ __('KRW') }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- 시장가격 -->
                        <div class="section" id="MarketPriceGroup">
                            <h4 class="section-tit">{{ __('Transaction Margin') }}</h4>

                            <div class="setting-inp money">
                                <input type="text" class="si-inp inp-m" value="5" name="offerMargin"
                                    id="offerMargin" readonly>
                                <span class="si-display">%</span>
                                <button type="button" class="btn-set decrease" id="margin-decrease">-</button>
                                <button type="button" class="btn-set increase" id="margin-increase">+</button>
                            </div>
                            <p class="setting-txt">{{ __('Current Tether Market Price:') }}
                                <strong> {{ number_format($prices['KRW'], 2) }} {{ __('KRW') }}</strong>
                            </p>
                        </div>
                        <!-- 고정가격 -->
                        <div class="section" id="FixedPriceGroup">
                            <h4 class="section-tit">{{ __('Fixed price') }}</h4>

                            <div class="setting-inp money">
                                <input type="number" class="si-inp inp-m" value="{{ $prices['KRW'] }}"
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
                                <input type="text" class="si-inp inp-s" value="30" name="offerTimeLimit"
                                    id="offerTimeLimit">
                                <span class="si-display">{{ __('minutes') }}</span>
                                <button type="button" class="btn-set decrease">-</button>
                                <button type="button" class="btn-set increase">+</button>
                            </div>

                            <p class="setting-txt">
                                {{ __('If the transaction is not completed within the set time, it will be automatically cancelled. (Minimum 20 minutes required)') }}
                            </p>
                        </div>
                    </div>

                    <div class="pc-btn">
                        <button type="button" class="process-btn" id="nextBtn" onclick="onNextClick()">
                            <span class="txt">{{ __('Next') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="Phase3" class="content offer offer3">
            <h2 class="page-tit">{{ __('Make Offer') }}</h2>

            <div class="container-row">
                <div class="card-box border">
                    <div class="step">
                        <div class="step-chip">
                            <span class="step-num">①</span>
                            <span class="step-txt">{{ __('Payment Method') }}</span>
                        </div>
                        <div class="step-chip">
                            <span class="step-num">②</span>
                            <span class="step-txt">{{ __('Price') }}</span>
                        </div>
                        <div class="step-chip active">
                            <span class="step-num">③</span>
                            <span class="step-txt">{{ __('Other Settings') }}</span>
                        </div>
                    </div>

                    <div class="offer3-inner">
                        <div class="section">
                            <div class="checkbox aic">
                                <input type="checkbox" name="secretTrade" id="secretTrade" class="checkbox__chk"
                                    onchange="onSecretTradeChange()">
                                <label for="secretTrade"
                                    class="checkbox__label section-tit">{{ __('Private Trade Password lock feature') }}</label>
                                <label class="" style="margin-left: 10px;">{{ __('비밀번호 잠금기능') }}</label>
                            </div>
                            <div class="input" name="passwordGroup" id="passwordGroup">
                                <div class="i-txt password w-full">
                                    <div class="i-txt__wrap">
                                        <input type="password" class="i-txt__input" placeholder="{{ __('Enter 4-digit password') }}"
                                            name="password" id="password" maxlength="4" pattern="\d{4}"
                                            inputmode="numeric" onkeypress="return onlyNumberKey(event)"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)">

                                        <button type="button" class="i-txt__btn-look" title="{{ __('비밀번호 보기 실행') }}">
                                            <span class="txt blind">{{ __('비밀번호 보기') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <h4 class="section-tit">{{ __('Trade Restriction') }}</h4>
                                <div class="checkbox aic">
                                    <input type="checkbox" name="checkboxKYCAuth" id="checkboxKYCAuth"
                                        class="checkbox__chk">
                                    <label for="checkboxKYCAuth"
                                        class="checkbox__label">{{ __('Only partners who have completed KYC verification can trade') }}</label>
                                </div>
                                <div>  <span class="checkbox__note">{{ __('To prevent illegal funds and third-party fraud, we recommend trading with partners who have completed KYC certification') }}</span></div>
                            </div>

                            <div class="section">
                                <h4 class="section-tit required">{{ __('Transaction Title') }}</h4>
                                <div class="input">
                                    <input type="text" size="10" name="offerLabel" id="offerLabel"
                                        class="inp-txt w-full" placeholder="{{ __('한글 10자 이내 입력') }}">
                                </div>
                            </div>

                            <div class="section">
                                <label for="dealtitle"
                                    class="checkbox__label required section-tit">{{ __('Transaction Conditions') }}</label>
                                <div class="textarea">
                                    <textarea name="offerCondition" id="offerCondition" class="w-full">
{{ __('1. 카카오뱅크로만 거래합니다.') }}
{{ __('2. 채팅으로 본인 확인을 합니다.') }}
{{ __('3. 본인 명의가 아니면 거래가 힘듭니다.') }}
{{ __('4. 불법적인 거래(마약, 도박 등)는 안 합니다.') }}
                                </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pc-btn">
                            <button type="submit" class="process-btn" id='make-trade'>
                                <span class="txt">{{ __('Confirm Trade Creation') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </form>

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="sheet1">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('Confirm Trade Creation') }}</h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="offer4">
                        <div class="offer-code">
                            <p class="oc-txt">{{ __('아래의 링크를 복사하고 직접 공유하셔도 됩니다.') }}</p>

                            <div class="oc-code-sub">
                                <p class="oc-code-txt">
                                </p>
                                <button type="button" class="oc-copy"><img
                                        src="{{ asset('/public/pub') }}/img/copy@2x.png" alt="{{ __('클립보드에 복사') }}"></button>
                            </div>
                        </div>
                        <div class="of4-sumarry">
                            <ul class="of4-sumarry-list">
                                <li class="of4-sumarry-row">
                                    <span class="of4-sumarry-label">{{ __('가격') }}</span>
                                    <span class="of4-sumarry-data price">
                                        991,434 KRW
                                        <i class="up">9.55</i>
                                        <!-- 하락인 경우 -->
                                        <!-- <i class="down">9.55</i> -->
                                        <!-- 하락인 경우 -->
                                    </span>
                                </li>
                                <li class="of4-sumarry-row">
                                    <span class="of4-sumarry-label">{{ __('거래량 제한') }}</span>
                                    <span class="of4-sumarry-data"></span>
                                </li>
                                <li class="of4-sumarry-row">
                                    <span class="of4-sumarry-label">{{ __('거래 시간 제한') }}</span>
                                    <span class="of4-sumarry-data"></span>
                                </li>
                                <li class="of4-sumarry-row" id="anaki_fee">
                                    <span class="of4-sumarry-label">{{ __('ANAKI 수수료') }}
                                        <div class="tooltip">
                                            <button type="button" class="tt-a"><span class="tt-q blind">{{ __('수수료') }}</span></button>
                                            <div class="tt-cont">
                                                <!-- 등급 제거  -->
                                                <!-- <ul class="tt-list">
                                                    <li class="tt-list-item jcsb">
                                                        <span class="tt-list-txt">{{ __('일반') }}</span>
                                                        <span class="tt-list-txt">0.8%</span>
                                                    </li> -->
                                                    <!-- <li class="tt-list-item jcsb">
                                                        <span class="tt-list-txt">{{ __('실버') }}</span>
                                                        <span class="tt-list-txt">0.6%</span>
                                                    </li>
                                                    <li class="tt-list-item jcsb">
                                                        <span class="tt-list-txt">{{ __('골드') }}</span>
                                                        <span class="tt-list-txt">0.5%</span>
                                                    </li>
                                                    <li class="tt-list-item jcsb">
                                                        <span class="tt-list-txt">{{ __('다이아') }}</span>
                                                        <span class="tt-list-txt">0.3%</span>
                                                    </li> -->
                                                <!-- </ul> -->
                                            </div>
                                        </div>
                                    </span>
                                    <span class="of4-sumarry-data">0.8%</span>
                                </li>
                            </ul>
                        </div>

                        <div class="of4-dl">
                            <dl>
                                <dt>{{ __('거래조건') }}</dt>
                                <dd>
                                    <div class="conditions-textarea-wrapper">
                                        <textarea id="conditionsTextarea" readonly></textarea>
                                    </div>
                                </dd>
                            </dl>
                            <dl>
                                <dt>{{ __('인증') }}</dt>
                                <dd class="dot">{{ __('전화번호 실명 인증을 완료한 파트너만 거래 가능') }}</dd>
                                <dd class="dot">{{ __('KYC 인증을 완료한 파트너만 거래 가능') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button type="button" class="ps-process-btn" id="confirmFinalBtn">
                        <span class="txt">{{ __('Confirm') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->
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
        const bankInfoFields = ['bankName', 'accountNumber', 'accountHolder'];
        let bankInfo = {};

        <?php
        $bankinfoDataSize = $bankinfoData->count();
        for ($i = 0; $i < $bankinfoDataSize; $i++) {
            echo "bankInfo[$i] = {};\n";
            foreach (['bankName', 'accountNumber', 'accountHolder'] as $field) {
                echo "bankInfo[$i]['$field'] = '" . ($bankinfoData->isNotEmpty() && isset($bankinfoData[$i]) ? addslashes($bankinfoData[$i]->$field) : '') . "';\n";
            }
        }
        ?>

        let payqrInfo = {};

        <?php
        $payqrDataSize = $payqrData->count();
        for ($i = 0; $i < $payqrDataSize; $i++) {
            echo "payqrInfo[$i] = {};\n";
            foreach (['payTag', 'payQR'] as $field) {
                if ($payqrData->isNotEmpty() && isset($payqrData[$i])) {
                    $value = "'" . $payqrData[$i]->$field . "'";
                } else {
                    $value = '';
                }
                echo "payqrInfo[$i]['$field'] = $value;\n";
            }
        }
        ?>

        var phase = 1;

        document.all.BankInfo.style.display = "none";
        document.all.KakaopayInfo.style.display = "none";

        document.all.Phase2.style.display = "none";
        document.all.Phase3.style.display = "none";
        document.all.passwordGroup.style.display = "none";

        document.all.FixedPriceGroup.style.display = "none";

        function onSecretTradeChange() {
            var isSecretTrade = document.all.secretTrade.checked;
            if (isSecretTrade) {
                document.all.passwordGroup.style.display = "";
            } else {
                document.all.passwordGroup.style.display = "none";
            }
        }

        function onPicUploadChange() {
            // document.all.payQRList.style.display = "none";
        }

        function onSelectMyAccount(index) {
            console.log(`onSelectMyAccount index:${index}`);

            var bankName = "";
            var accountNumber = "";
            var accountHolder = "";

            if (index >= 0 && index < <?php echo $bankinfoDataSize; ?>) {
                bankName = bankInfo[index].bankName;
                accountNumber = bankInfo[index].accountNumber;
                accountHolder = bankInfo[index].accountHolder;
            } else {
                bankName = '';
                accountNumber = '';
                accountHolder = '';
            }

            // console.log(`onSelectMyAccount : ${bankName}, ${accountNumber}, ${accountHolder}`);

            document.all.bankName.value = bankName;
            document.all.accountNumber.value = accountNumber;
            document.all.accountName.value = accountHolder;
        }

        function onSelectMyPay(index) {
            var payTag = null;
            var payQRUrl = "";

            console.log(`onSelectMyPay index:${index}`);

            if (index >= 0 && index < <?php echo $payqrDataSize; ?>) {
                payTag = payqrInfo[index].payTag;
                payQRUrl = payqrInfo[index].payQR;
            } else {
                payTag = '';
                payQRUrl = '';
            }

            console.log(payTag, payQRUrl);

            document.querySelector('#payTag').value = payTag;
            document.querySelector('#payQRUrl').value = payQRUrl;
        }

        function refreshPhase() {
            if (phase == 2) {
                document.all.Phase1.style.display = "none";
                document.all.Phase2.style.display = "";
                document.all.Phase3.style.display = "none";

                // Phase2 타이틀 설정
                var action = document.querySelector('input[name="action"]:checked').value;
                var phase2Title = document.getElementById('phase2Title');
                if (action === 'buy') {
                    phase2Title.textContent = "{{ __('Create Trade Buyer') }}";
                } else {
                    phase2Title.textContent = "{{ __('Create Trade Seller') }}";
                }

            } else if (phase == 3) {
                document.all.Phase1.style.display = "none";
                document.all.Phase2.style.display = "none";
                document.all.Phase3.style.display = "";

            } else {
                document.all.BankInfo.style.display = "none";
                document.all.KakaopayInfo.style.display = "none";

                document.all.Phase1.style.display = "";
                document.all.Phase2.style.display = "none";
                document.all.Phase3.style.display = "none";

            }
        }

        function onNextClick() {

            const bankTransfer = $("input[name='bankTrasfer']:checked").val();
            const kakaopay = $("input[name='kakaopay']:checked").val();

            if (!bankTransfer && !kakaopay) {
                alert('{{ __("Please register a payment method") }}');
                return
            }

            phase++;

            if (phase >= 3) {
                phase = 3;
            }

            refreshPhase();
        }

        function onPreClick() {
            phase--;

            if (phase <= 1) {
                phase = 1;
            }

            refreshPhase();
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.location.href='{{ setRoute('user.make-offer.index') }}';
            };

            const conditionsTextarea = document.getElementById('conditionsTextarea');

            conditionsTextarea.addEventListener('mousedown', function(e) {
                e.preventDefault();
            });

            conditionsTextarea.addEventListener('focus', function(e) {
                this.blur();
            });

             
            const form = document.querySelector('form[action="{{ setRoute('user.make-offer.submit') }}"]');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // 중복 방지
                const makeTradeButton =  document.getElementById('make-trade');
                makeTradeButton.disabled = true; 

                const formData = new FormData(form);
                
                // console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                  
                    if (key === 'tradeVolMin' || key === 'tradeVolMax') {
                        // 쉼표 제거 및 FormData 업데이트
                        const cleanedValue = value.replace(/,/g, '');
                        formData.set(key, cleanedValue);
                    }
                }
                // console.log( document.querySelector('#fixedPrice').value);
                // return false;
                
                // fetch('{{ setRoute('user.make-offer.submit') }}', {
                //         method: 'POST',
                //         body: formData,
                //         headers: {
                //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                //                 .getAttribute('content')
                //         }
                //     })
                //     .then(response => response.json())
                //     .then(data => {
                //         if (data.success) {
                //             // 팝업 시트 데이터 채우기
                //             populatePopSheet(data.data);
                //             // 팝업 시트 표시
                //             openPopSheet('sheet1');
                //             showNotification(data.message, "success");
                //         } else {
                //             alert(data.message || "오류가 발생했습니다.");
                //         }
                //     })
                //     .catch(error => {
                //         console.log('Error:', error);
                //         showNotification("{{ __('예기치 않은 오류가 발생했습니다. 다시 시도해 주세요.') }}", "error");
                //     });
                fetch('{{ setRoute('user.make-offer.submit') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        // HTTP 상태 코드 확인
                        if (!response.ok) {  // HTTP 상태가 200~299 범위가 아니면 오류로 간주
                            throw new Error(`HTTP Error: ${response.status}`);
                        }
                        return response.json();  // 응답이 정상적이면 JSON으로 파싱
                    })
                    .then(data => {
                        console.log(data);
                        if (data.success) {
                            // 팝업 시트 데이터 채우기
                            populatePopSheet(data.data);
                            // 팝업 시트 표시
                            openPopSheet('sheet1');
                            showNotification(data.message, "success");
                        } else {
                            alert(data.message || "오류가 발생했습니다.");
                        }
                    })
                    .catch(error => {
                        console.log('Error:', error);  // 전체 error 객체를 출력
                        showNotification("{{ __('예기치 않은 오류가 발생했습니다. 다시 시도해 주세요.') }}", "error");
                    });
            });

            form.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // 폼 제출 방지

                    // 입력 요소가 숫자를 다루는 특정 필드인 경우에만 반올림 적용
                    if (event.target.matches('input[type="text"], input[type="number"]')) {
                        const inputValue = parseFloat(event.target.value.replace(/,/g,
                            '')); // 쉼표 제거 후 숫자로 변환
                        if (!isNaN(inputValue)) {
                            const roundedValue = Math.round(inputValue / 1000) * 1000; // 1000 단위로 반올림
                            event.target.value = roundedValue.toLocaleString(); // 결과를 다시 쉼표가 포함된 문자열로 변환
                        }
                    }
                }
            });

            // 숫자 입력 필드에 대한 포맷팅 함수
            function formatNumber(input) {
                let value = input.value.replace(/,/g, '');
                if (value !== '') {
                    value = parseFloat(value).toLocaleString();
                    input.value = value;
                }
            }

            // 숫자 입력이 예상되는 필드들에 이벤트 리스너 추가
            const numberInputs = form.querySelectorAll(
                'input[name="tradeVolMin"], input[name="tradeVolMax"]'); // 'input[name="tradeVolMin"], input[name="tradeVolMax"], input[name="fixedPrice"]' fixedPrice 제외
            numberInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    formatNumber(this);
                });
            });

            function populatePopSheet(data) {
                if (data.action == "buy") {
                    document.querySelector('#anaki_fee').style.display = 'none';
                }

                // Select the pop-sheet element
                const popSheet = document.querySelector('.pop-sheet[data-pop-sheet="sheet1"]');

                // Update the URL with the order_id
                const urlElement = popSheet.querySelector('.oc-code-txt');
                const shareUrl = `{{ config('app.url') }}/user/${data.action}-list/preview/${data.order_id}`;
                urlElement.textContent = shareUrl;

                // Add click event listener to the copy button
                const copyButton = popSheet.querySelector('.oc-copy');
                copyButton.addEventListener('click', () => {
                    navigator.clipboard.writeText(shareUrl)
                        .then(() => {
                            // Optional: Provide visual feedback that the URL was copied
                            alert('{{ __('URL이 클립보드에 복사되었습니다.') }}');
                        })
                        .catch(err => {
                            console.error('클립보드 복사 실패:', err);
                            alert('{{ __('URL 복사에 실패했습니다. 수동으로 복사해 주세요.') }}');
                        });
                });

                // Populate price
                const priceElement = popSheet.querySelector('.of4-sumarry-data.price');

                //  MarketPrice 일 경우 처리
                if (data.item.priceType === 0) {
                    priceElement.innerHTML =
                        `${data.prices.KRW.toLocaleString()} {{ __('KRW') }} <i class="${data.item.offerMargin >= 0 ? 'up' : 'down'}"> ${data.item.offerMargin}%</i>`;
                } else {
                    priceElement.innerHTML = `${data.item.fixedPrice}`;
                }

                // FixedPrice data.item.priceType

                // Populate transaction volume limit
                const volumeLimitElement = popSheet.querySelector(
                    '.of4-sumarry-row:nth-child(2) .of4-sumarry-data');
                volumeLimitElement.textContent =
                    `${Number(data.item.tradeVolMin).toLocaleString()} KRW ~ ${Number(data.item.tradeVolMax).toLocaleString()} KRW`;

                // Populate transaction time limit
                const timeLimitElement = popSheet.querySelector('.of4-sumarry-row:nth-child(3) .of4-sumarry-data');
                timeLimitElement.textContent = `${data.item.offerTimeLimit}{{ __('Minute') }}`;

                // Populate transaction conditions
                const conditionsTextarea = popSheet.querySelector('#conditionsTextarea');
                conditionsTextarea.value = data.item.offerCondition;

                // Populate authentication requirements
                const authElements = popSheet.querySelectorAll('.of4-dl dd.dot');
                authElements[0].style.display = data.item.needMobileAuth ? 'block' : 'none';
                authElements[1].style.display = data.item.needKYCAuth ? 'block' : 'none';
            }

            const confirmFinalBtn = document.getElementById('confirmFinalBtn');
            confirmFinalBtn.addEventListener('click', function() {
                // Here you can add any final confirmation logic if needed
                // alert('Trade offer created successfully!');
                closePopSheet('sheet1');
                // Optionally, redirect to a success page or refresh the current page
                window.location.href = '{{ route('user.dashboard') }}';
            });

            // 기존 함수들 유지
            function RefreshPayInfo() {
                try {
                    var bankTransfer = $("input[name='bankTrasfer']:checked").val();
                    var kakaopay = $("input[name='kakaopay']:checked").val();
                    var sell = $("input[name='action']:checked").val();
                    if (sell == "sell") {
                        if (bankTransfer) {
                            document.all.BankInfo.style.display = "";
                        } else {
                            document.all.BankInfo.style.display = "none";
                        }
                        if (kakaopay) {
                            document.all.KakaopayInfo.style.display = "";
                        } else {
                            document.all.KakaopayInfo.style.display = "none";
                        }
                    } else {
                        document.all.BankInfo.style.display = "none";
                        document.all.KakaopayInfo.style.display = "none";
                    }

                    document.all.payQRImage.value = '';
                    // document.all.payQRList.style.display = "";
                } catch (error) {
                    console.log(error);
                }
            }

            // priceType 변경 함수
            function changePriceType(priceType) {
                if (priceType == "MarketPrice") {
                    document.getElementById('MarketPriceGroup').style.display = "";
                    document.getElementById('FixedPriceGroup').style.display = "none";
                } else {
                    document.getElementById('MarketPriceGroup').style.display = "none";
                    document.getElementById('FixedPriceGroup').style.display = "";
                }
            }

            // 기존 이벤트 리스너 유지
            $("input[name='action']").click(RefreshPayInfo);
            $("input[name='bankTrasfer']").click(RefreshPayInfo);
            $("input[name='kakaopay']").click(RefreshPayInfo);

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
        });

        // 비밀번호 보기/숨기기 토글
        document.querySelector('.i-txt__btn-look').addEventListener('click', function() {
            var passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('active');
            }
        });

        // 커스텁 셀렉트
        $('.custom-select').customSelect();

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

        function addFile() {
            document.querySelector('.inp-hide').click();
        }

        function handleFileChange(event) {
            const fileInput = event.target;
            const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : '';
            document.querySelector('.file-name').textContent = fileName;

            if (fileName) {
                document.querySelector('.add-file-row').classList.add('added');
            } else {
                document.querySelector('.add-file-row').classList.remove('added');
            }
        }

        (function($) {
            $.fn.tooltipPlugin = function(options) {
                // 기본 옵션
                var settings = $.extend({
                    activeClass: 'active',
                }, options);

                // 플러그인 기능 정의
                function toggleTooltip(e) {
                    e.preventDefault();
                    var $tooltip = $(this).closest('.tooltip');
                    $('.tooltip').not($tooltip).removeClass(settings.activeClass); // 다른 모든 tooltips 비활성화
                    $tooltip.toggleClass(settings.activeClass);
                }

                // 플러그인 적용
                this.each(function() {
                    var $this = $(this);
                    $this.find('.tt-a').off('click').on('click', toggleTooltip);
                });

                // 문서 외부 클릭 시 Tooltip 비활성화
                $(document).click(function(e) {
                    if (!$(e.target).closest('.tooltip').length) {
                        $('.tooltip').removeClass(settings.activeClass);
                    }
                });

                return this; // 체이닝을 위해 this 반환
            };
        }(jQuery));

        (function($) {
            $.fn.customSelect = function() {
                var customSelect = this;

                customSelect.find('.cs-btn').click(function(e) {
                    e.stopPropagation();
                    customSelect.find('.cs-list').slideToggle('fast');
                });

                customSelect.find('.value').click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var valueText = $(this).text();
                    customSelect.find('.dp-txt').text(valueText);
                    customSelect.find('.cs-list').slideUp('fast');
                });

                $(document).click(function() {
                    customSelect.find('.cs-list').slideUp('fast');
                });
            };
        })(jQuery);

        function onlyNumberKey(evt) {
            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        // 툴팁 공통
        $('.tooltip').tooltipPlugin();
    </script>
@endpush
