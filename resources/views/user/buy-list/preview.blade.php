@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')


<style>
    .verify-button {
      background: linear-gradient(45deg, #32cd32, #228b22);
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease, transform 0.2s ease-in-out;
      position: relative;
      overflow: hidden;
      margin-top:10px;
    }

    .verify-button:hover {
      background: linear-gradient(45deg, #228b22, #32cd32);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px) scale(1.05);
    }

    .verify-button-no {
      background: linear-gradient(45deg, #ff6347, #e60000);
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease, transform 0.2s ease-in-out;
      position: relative;
      overflow: hidden;
      margin-top:10px;
    }

    .verify-button-no:hover {
      background: linear-gradient(45deg, #e60000, #ff6347);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px) scale(1.05);
    }

</style>

@section('content')
    <div class="content pc-dt">
        <h2 class="page-tit">{{ __('얼마를 판매 하시겠어요?') }}</h2>

        <div class="container-row">
            <div class="card-box">
                <div class="dt-inner">
                    <form id="sellForm" class="card-form" action="{{ setRoute('user.buy-list.sell') }}" method="POST"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ urlSafeEncrypt($item->id) }}">
                        <input type="hidden" name="price"
                            value="{{ $item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice }}">

                        <div class="dt-row">
                            <div class="dt-col">
                            @if (!$isOwner)
                                <label for="price" class="dt-label">{{ __('원화 수령액') }}</label>
                                <div class="dt-inp">
                                    <input type="text" id="paidAmount" name="paidAmount" class="input"
                                        placeholder="{{ __('enter_amount') }}" oninput="onPaidAmountChange()">
                                    <span class="unit">{{ __('KRW') }}</span>
                                </div>
                                <p class="caution-txt">{{ __('start_transaction_message') }}</p>
                            </div>
                            <div class="dt-col">
                                <label for="receipt" class="dt-label">{{ __('테더 지급액') }}</label>
                                <div class="dt-inp">
                                    <input type="text" id="sendAmount" name="sendAmount" class="input"
                                        placeholder="{{ __('enter_quantity') }}" oninput="onSendAmountChange()">
                                    <span class="unit">USDT</span>
                                </div>
                            @endif
                            </div>
                        </div>

                        @if ($isOwner)
                            <div class="dt-row buy-btn">
                                <!-- <div class="dt-col m-first">
                                    <button type="button" class="close-offer" onclick="submitCancelForm()">
                                        <span class="txt">{{ __('Close Offer') }}</span>
                                    </button>
                                </div> -->
                                <!-- <div class="dt-col m-second">
                                    <p class="info-txt small">{{ __('escrow_message') }}</p>
                                </div> -->
                                <button type="button" class="close-offer" style='background-color:#21b8a1;margin-top:1.5%'
                                        onclick="submitUpdateForm('{{ urlSafeEncrypt($item->id) }}')">
                                        <span class="txt">{{ __('Edit Offer') }}</span>
                                </button>

                                <button type="button" class="close-offer" onclick="submitCancelForm()" style='margin-top:1.5%' >
                                        <span class="txt">{{ __('Close Offer') }}</span>
                                </button>
                            </div>
                        @else
                            <div class="dt-row buy-btn">
                                <div class="dt-col m-first">
                                    @if ($item->password != null)
                                        <div class="appoint">
                                            <span class="txt">{{ __('Private Trade') }}</span>
                                            <div class="input">
                                            <input type="password" class="inp-txt"
                                                    placeholder="{{ __('Pin Number') }}" name="password" id="password"
                                                    maxlength="4" pattern="\d{4}" inputmode="numeric"
                                                    onkeypress="return onlyNumberKey(event)"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                                    onfocus="this.removeAttribute('readonly');">
                                            </div>
                                        </div>
                                    @endif

                                    <button type="button" class="btn-sell" onclick="submitSellForm()">
                                        <span class="txt">{{ __('Sell Now') }}</span>
                                    </button>
                                </div>

                                <div class="dt-col m-second">
                                    <p class="info-txt small">{{ __('escrow_message2') }}</p>
                                </div>
                            </div>
                        @endif
                    </form>

                    <div class="dt-row introduce">
                        <div class="dt-col">
                            <div class="dt-tit">{{ __('Transaction Information') }}</div>
                            <div class="border-box">
                                <ul class="deal">
                                    <li>
                                        <span class="label">{{ __('seller_price') }}</span>
                                        <div class="data big">
                                            {{ $item->priceType == 0 ? number_format(($prices['KRW'] * (100 + $item->offerMargin)) / 100) : number_format($item->fixedPrice) }}
                                            {{ __('KRW') }}
                                            @if ($item->priceType == 0)
                                                <span
                                                    class="arrow {{ $item->offerMargin >= 0 ? 'up' : 'down' }}">{{ abs($item->offerMargin) }}</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li>
                                        <span class="label">{{ __('purchase_limit') }}</span>
                                        <span class="data">{{ __('minimum') }} {{ number_format($item->tradeVolMin) }}
                                            {{ __('KRW') }} - {{ __('maximum') }}
                                            {{ number_format($item->tradeVolMax) }} {{ __('KRW') }}</span>
                                    </li>
                                    <li>
                                        <span class="label">{{ __('trade_time_limit') }}</span>
                                        <span class="data">{{ $item->offerTimeLimit }}{{ __('minutes') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="dt-col">
                            <div class="dt-tit">{{ __('Buyer Infomation') }}</div>
                            <div class="border-box">
                                <div class="user">
                                    <div class="user-img"><img src="{{ $item->user->userImage }}"></div>
                                    <div class="user-info">
                                        <div class="user-name-q">
                                            <span class="user-name">{{ $item->user->username }}</span>
                                        </div>

                                        <div class="log online">{{ $item->user->status == 1 ? 'Online' : 'Offline' }}
                                        </div>
                                        <div class="log last">{{ __('last_access') }}:
                                            @php
                                                $diff = $now->diff(new DateTime($item->user->updated_at));
                                                if ($diff->days == 0 && $diff->h == 0 && $diff->i < 3) {
                                                    echo __('currently_logged_in');
                                                } else {
                                                    $time = '';
                                                    if ($diff->days > 0) {
                                                        $time = trans_choice('days_ago', $diff->days, [
                                                            'value' => $diff->days,
                                                        ]);
                                                    } elseif ($diff->h > 0) {
                                                        $time = trans_choice('hours_ago', $diff->h, [
                                                            'value' => $diff->h,
                                                        ]);
                                                    } elseif ($diff->i > 0) {
                                                        $time = trans_choice('minutes_ago', $diff->i, [
                                                            'value' => $diff->i,
                                                        ]);
                                                    } else {
                                                        $time = __('just_now');
                                                    }
                                                    echo $time;
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="cerf">
                                    <span class="txt">
                                        {{ $item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified') }}
                                    </span>
                                </div> -->
                              
                                <button class="verify-button{{ ($item->user->kyc_verified == 0)? '-no':''  }}">{{ $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="dt-row condition">
                        <div class="dt-col w-full">
                            <div class="dt-tit">{{ __('Transaction Conditions') }}</div>
                            <div class="border-box">
                                <ul class="term">
                                    {!! nl2br(e($item->offerCondition)) !!}
                                </ul>

                                <p class="term-txt">{{ $item->transGuide }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="sell_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('판매하기-판매자 거래정보') }}</h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="dt-pop">
                        <ul class="summary">
                            <li>
                                <span class="label">{{ __('상태') }}</span>
                                <div class="data">
                                    <span class="data-txt going">진행중</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('판매자 이름') }}</span>
                                <div class="data">
                                    <span
                                        class="data-txt">{{ auth()->user()->realname ?? auth()->user()->username }}</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매자 이름') }}</span>
                                <div class="data">
                                    <span class="data-txt">{{ $item->user->realname ?? $item->user->username }}</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('제안가') }}</span>
                                <div class="data">
                                    <span class="data-price">
                                        {{ $item->priceType == 0 ? number_format(($prices['KRW'] * (100 + $item->offerMargin)) / 100, 2) : number_format($item->fixedPrice, 2) }}
                                        {{ __('KRW') }}
                                        @if ($item->priceType == 0)
                                            <i
                                                class="{{ $item->offerMargin >= 0 ? 'up' : 'down' }}">{{ abs($item->offerMargin) }}%</i>
                                        @endif
                                    </span>
                                    <p class="market-price">{{ __('Market Price') }}:
                                        {{ number_format($prices['KRW'], 2) }}
                                        {{ __('KRW') }}</p>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('판매금액') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="purchaseAmount"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('테더 판매 수량') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="usdtQuantity"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매 제안 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="offerTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래 종료 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="endTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래은행') }}</span>
                                <div class="data">
                                    <span class="data-txt">{{ $item->bankName }}({{ $item->accountNumber }})
                                        {{ $item->accountName }}</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div class="buy-row">
                                <span class="ps-row-tit">{{ __('입금 증빙자료') }}</span>
                                <div class="pay-proof-img attach-img"><img src="" alt="{{ __('입금 증빙 이미지') }}"
                                        style="display: none;"></div>
                            </div>
                        </div>

                        <div class="dt-row">
                            <div class="my-balance">
                                <span class="ps-row-tit">{{ __('내 잔고') }}</span>

                                <div class="mb-data">
                                    <div class="mb-data1">9,400.<small>32</small> USDT</div>
                                    <div class="mb-data2">pending:61276.686102485</div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-row pd-h25">
                            <div class="seller-btn-wrap flex-c-s">
                                <input type="hidden" id="email" readonly title="{{ __('거래자 이메일입니다.') }}" value="">
                                <button id="send_tether" class="ps-process-btn w-200" onclick="sendTether();" disabled>
                                    <span class="txt">{{ __('테더 이체') }}</span>
                                </button>
                            </div>

                            <div class="mb-txt flex-c-s">
                                <p class="info-txt small c666">{{ __('※ 구매자가 허위 입금증을 보내거나 입금확인이 안될 경우,고객센터에 분쟁 요청을 하면 거래가 중지되며, 소명자료 확인 후 정상처리가 됩니다.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn cancel w-200" onclick="cancelTransaction()">
                        <span class="txt">{{ __('Cancel Transaction') }}</span>
                    </button>
                    <button class="ps-process-btn black w-200" onclick="confirmDispute();">
                        <span class="txt">{{ __('분쟁요청') }}</span>
                    </button>
                    <button class="ps-process-btn cancel w-200" onclick="openChat('sell')">
                        <span class="txt">{{ __('채팅') }}</span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('sell_detail')"><img
                        src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="{{ __('팝업시트 닫기') }}"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->
@endsection

@push('style')
    <style>
        .toggle-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        .toggle-btn i {
            font-size: 18px;
        }

        .dashboard-header-wrapper .title {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
        }

        .dashboard-header-wrapper .title i {
            margin-right: 15px;
            font-size: 2rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .message-input-group {
            max-width: 100%;
            margin-bottom: 15px;
        }

        .message-input-wrapper {
            width: 100%;
        }

        .input-button-wrapper {
            display: flex;
            align-items: center;
        }

        #content {
            flex-grow: 1;
            resize: none;
            height: 38px;
            overflow-y: auto;
        }

        .input-button-wrapper .btn {
            margin-left: 10px;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .input-button-wrapper {
                flex-direction: column;
                align-items: stretch;
            }

            .input-button-wrapper .btn {
                margin-left: 0;
                margin-top: 10px;
            }
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-success {
            background-color: #28a745;
            color: #ffffff;
        }

        .form-group {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
        }

        .custom-control {
            display: flex;
            align-items: center;
            padding-left: 0;
        }

        .custom-control-input {
            position: static;
            margin-left: 0;
            margin-right: 10px;
        }
    </style>
@endpush

@push('script')
    <script>
        var msg = '{{ Session::get('alert') }}';
        var exist = '{{ Session::has('alert') }}';
        if (exist) {
            setTimeout(() => {
                alert(msg);
            }, 1000);

        }

        let itemId;
        let orderId;

        function submitCancelForm() {
            if (confirm("{{ __('거래를 삭제 하시겠습니까?') }}")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.buy-list.canceloffer') }}";
                form.style.display = 'none';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                var itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'item_id';
                itemIdInput.value = "{{ urlSafeEncrypt($item->id) }}";
                form.appendChild(itemIdInput);

                document.body.appendChild(form);

                // AJAX를 사용하여 폼 제출
                var formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 성공 시 대시보드로 리다이렉트
                            window.location.href = "{{ setRoute('user.buy-list.index') }}";
                        } else {
                            // 실패 시 에러 메시지 표시
                            alert(data.message || "{{ __('An error occurred while closing the offer.') }}");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('An error occurred while processing your request.') }}");
                    });

                document.body.removeChild(form);
            }
        }

        function submitSellForm() {
            // 폼 제출 전 유효성 검사 등을 수행할 수 있습니다.
            let paidAmount = document.getElementById('paidAmount').value;
            let sendAmount = document.getElementById('sendAmount').value;
            let passwordInput = document.getElementById('password');
            let itemPassword = @json($item->password);

            if (paidAmount === '' || sendAmount === '') {
                alert('{{ __('Please enter both payment amount and purchase amount.') }}');
                return;
            }

            if (itemPassword !== null) {
                if (passwordInput.value !== itemPassword) {
                    alert('{{ __('Incorrect password. Please try again.') }}');
                    return;
                }
            }

            if (confirm('{{ __('Are you sure you want to proceed with this sale?') }}')) {
                // 폼 제출
                // document.getElementById('sellForm').submit();
                let form = document.getElementById('sellForm');
                let formData = new FormData(form);
                
                fetch('{{ route('user.buy-list.sell') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {

                        if (result.success) {
                            itemId = result.item_id;
                            orderId = result.order_id;
                            updatePopSheetContent(result.client_email);
                            openPopSheet('sell_detail');
                            startPayProofCheck(itemId);
                        } else {
                            alert(result.message);
                        }

                    })
                    .catch(error => {
                        console.log(error)
                        alert(error);
                        // let messageElement = document.getElementById('buyResultMessage');
                        // messageElement.innerHTML =
                        //     '<div class="alert alert-danger">An error occurred while processing your request.</div>';
                        // $('#buyResultModal').modal('show');
                    });
            }
        }

        function onPaidAmountChange() {
            let paidAmount = document.getElementById('paidAmount').value;
            let sendAmountElement = document.getElementById('sendAmount');

            let price = @json($item->fixedPrice);
            let offerMargin = @json($item->offerMargin);
            let priceType = @json($item->priceType);
            if (priceType == 0) {
                price = @json($prices['KRW']);
            }

            if (offerMargin == null)
                offerMargin = 0;

            if (price != null) {
                let offerMarginInt = parseFloat(offerMargin);
                if (priceType == 1) {
                    offerMarginInt = 0;
                    price = price.replace("KRW", '');
                }

                let priceInt = parseFloat(price);

                let multiplier = (100 + offerMarginInt) / 100;
                let sendAmount = paidAmount / (priceInt * multiplier);

                sendAmountElement.value = Number(sendAmount.toFixed(2)).toLocaleString();
            }
        }

        function onSendAmountChange() {
            let paidAmountElement = document.getElementById('paidAmount');
            let sendAmount = document.getElementById('sendAmount').value;

            let price = @json($item->fixedPrice);
            let offerMargin = @json($item->offerMargin);
            let priceType = @json($item->priceType);
            if (priceType == 0) {
                price = @json($prices['KRW']);
            }

            if (offerMargin == null)
                offerMargin = 0;

            if (price != null) {
                let offerMarginInt = parseFloat(offerMargin);
                if (priceType == 1) {
                    offerMarginInt = 0;
                    price = price.replace("KRW", '');
                }

                let priceInt = parseFloat(price);

                let multiplier = (100 + offerMarginInt) / 100;
                let paidAmount = sendAmount * priceInt * multiplier;

                paidAmountElement.value = paidAmount;
            }
        }

        function confirmDownload(event, attachmentId) {
            event.preventDefault();

            fetch(`/api/check-attachment/${attachmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.canDownload) {
                        window.open(event.target.href, '_blank');
                    } else {
                        alert("{{ __('error_downloading_attachment') }}");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("{{ __('error_downloading_attachment') }}");
                });

            return false;
        }

        function startPayProofCheck(tradeId) {
            let checkInterval;
            let attempts = 0;
            const maxAttempts = 60 * 10; // 10분

            function checkPayProof() {
                fetch(`{{ route('user.mytrade.checkPayProof', '') }}/${tradeId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Deposit found
                            clearInterval(checkInterval);

                            const payProofContainer = document.querySelector('[data-pop-sheet="sell_detail"] .dt-buy');
                            const payProofImg = payProofContainer.querySelector('.pay-proof-img img');
                            if (payProofImg && data.pay_proof) {
                                payProofImg.src = "/frontend/proof/" + data.pay_proof;

                                payProofImg.parentElement.classList.remove('attach-img');
                                payProofImg.style.display = '';
                            }

                            document.getElementById('send_tether').disabled = false;
                            document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.cancel').style
                                .display = 'none';

                            alert(`{{ __('이체증빙자료를 받았습니다.') }}`);
                        } else {
                            attempts++;
                            if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                alert(
                                    "{{ __('No matching pay proof found after 120 seconds. Please try again or contact support.') }}"
                                );
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        clearInterval(checkInterval);
                        alert("{{ __('An error occurred while checking for deposits. Please try again.') }}");
                    });
            }

            checkInterval = setInterval(checkPayProof, 1000); // Check every 1 second
        }

        function updatePopSheetContent(client_email) {
            let paidAmount = parseFloat(document.getElementById('paidAmount').value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                '$&,');
            let sendAmount = parseFloat(document.getElementById('sendAmount').value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                '$&,');

            document.getElementById('purchaseAmount').textContent = `${paidAmount} {{ __('KRW') }}`;
            document.getElementById('usdtQuantity').textContent = sendAmount;


            let now = new Date();
            document.getElementById('offerTime').textContent = now.toLocaleString();

            let endTime = new Date(now.getTime() + {{ $item->offerTimeLimit }} * 60000);
            document.getElementById('endTime').textContent = endTime.toLocaleString();

            document.querySelector('[data-pop-sheet="sell_detail"] .mb-data1').innerHTML =
                `{{ number_format($usdtBalance, 2) }} <small>USDT</small>`;
            document.querySelector('[data-pop-sheet="sell_detail"] .mb-data2').textContent =
                `pending:{{ number_format($usdtPending, 2) }}`;

            const emailField = document.querySelector('[data-pop-sheet="sell_detail"] #email');
            if (emailField) {
                emailField.value = client_email;
            }
            openChat();
        }

        function sendTether() {

            if (!confirm("{{ __('테더를 전송하시겠습니까?') }}")) {
                return;
            }

            const sendButton = document.getElementById('send_tether');
            sendButton.disabled = true;
            sendButton.innerText = `{{ __('전송중') }}`;
            sendButton.style.color = 'white';

            const emailField = document.querySelector('[data-pop-sheet="sell_detail"] #email');
            const amount = document.getElementById('usdtQuantity').textContent;

            // 데이터 준비
            const data = {
                amounts: parseFloat(amount),
                email: emailField.value,
                itemId: itemId,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            const apiEndpoint = "{{ setRoute('user.wallet.transferUSDTInternally') }}";
            fetch(apiEndpoint, {
                    method: 'PUT',
                    body: JSON.stringify(data),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("{{ __('USDT가 성공적으로 전송되었습니다.') }}");
                        window.location.href = '{{ setRoute('user.wallet.index') }}'
                    } else {
                        throw new Error(data.message || `{{ __('전송에 실패했습니다.') }}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`{{ __('전송 중 오류가 발생했습니다: ') }}` + error.message);
                    sendButton.disabled = true;
                    sendButton.innerText = `{{ __('테더 이체') }}`;
                    sendButton.style.color = 'white';
                });
        }

        function cancelTransaction() {
            let reason = prompt("{{ __('거래 취소 사유를 적어주세요.') }}");
            if (reason === null) {
                return; // User cancelled the prompt
            }

            if (confirm("{{ __('거래를 정말 취소 하시겠습니까?') }}")) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.mytrade.cancel') }}";
                form.style.display = 'none';

                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                let methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);

                let itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'itemId';
                itemIdInput.value = itemId;
                form.appendChild(itemIdInput);

                let cancelReasonInput = document.createElement('input');
                cancelReasonInput.type = 'hidden';
                cancelReasonInput.name = 'cancelReason';
                cancelReasonInput.value = reason;
                form.appendChild(cancelReasonInput);

                document.body.appendChild(form);

                // Submit the form using AJAX
                let formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // alert("{{ __('Transaction cancelled successfully.') }}");
                            closePopSheet('buy_detail');
                            // You might want to refresh the page or update the UI here
                            window.location.href = '{{ route('user.dashboard') }}';
                        } else {
                            alert(data.message || "{{ __('An error occurred while cancelling the transaction.') }}");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('An error occurred while processing your request.') }}");
                    });

                document.body.removeChild(form);
            }
        }

        function openChat() {
            $('.chat-layer').show();
            window.startChat(orderId, '{{ $item->user->id }}');
        }

        function closeChat() {
            $('.chat-layer').hide();
        }

        function confirmDispute() {
            const message = "{{ __('\"분쟁 요청\" 시 거래가 해결될 때까지 한시적으로 정지 됩니다. 고객센터에 접수해 주세요.') }}";
            if (confirm(message)) {
                // '{{ urlSafeEncrypt($item->id) }}'
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.mytrade.dispute') }}";
                form.style.display = 'none';

                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                let methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);

                let itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'itemId';
                itemIdInput.value = itemId;
                form.appendChild(itemIdInput);

                document.body.appendChild(form);

                // Submit the form using AJAX
                let formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ setRoute('user.cs.index') }}';
                        } else {
                            alert(data.message || "{{ __('An error occurred while disputing the transaction.') }}");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('An error occurred while processing your request.') }}");
                    });

                document.body.removeChild(form);
            }
        }

        function onlyNumberKey(evt) {
            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        function submitUpdateForm(id){
            location.href='/user/make-offer/moveUpdateForm/'+id;
        }

    </script>
@endpush
