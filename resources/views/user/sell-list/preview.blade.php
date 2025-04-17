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
        <h2 class="page-tit">{{ __('얼마를 구매 하시겠어요?') }}</h2>

        <div class="container-row">
            <div class="card-box">
                <div class="dt-inner">
                    <form id="buyForm" class="card-form" action="{{ setRoute('user.sell-list.buy') }}" method="POST"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ urlSafeEncrypt($item->id) }}">
                        <input type="hidden" name="price" value="{{ $item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice }}">

                        <div class="dt-row">
                            
                            <div class="dt-col">
                            @if (!$isOwner)
                                <label for="price" class="dt-label">{{ __('Purchase Amount') }}</label>
                                <div class="dt-inp">
                                    <input type="text" id="payAmount" name="payAmount" class="input"
                                        placeholder="{{ __('enter_amount') }}" oninput="onPayAmountChange()">
                                    <span class="unit">{{ __('KRW') }}</span>
                                </div>
                                <p class="caution-txt">{{ __('start_transaction_message') }}</p>
                           
                            </div>

                            <div class="dt-col">
                           
                                <label for="receipt" class="dt-label">{{ __('USDT Amount') }}</label>
                                <div class="dt-inp">
                                    <input type="text" id="recvAmount" name="recvAmount" class="input"
                                        placeholder="{{ __('enter_quantity') }}" oninput="onRecvAmountChange()">
                                    <span class="unit">USDT</span>
                                </div>
                            @endif
                            </div>
                        </div>

                        @if ($isOwner)
                            <div class="dt-row buy-btn">

                                <!-- <div class="dt-col m-first"> -->
                                    <!-- <button type="button" class="close-offer" style='vbackground-color:#21b8a1;'
                                        onclick="submitUpdateForm('{{ urlSafeEncrypt($item->id) }}')">
                                        <span class="txt">{{ __('Update') }}</span>
                                    </button> -->
                                <!-- </div> -->

                                    <button type="button" class="close-offer" style='background-color:#21b8a1;margin-top:1.5%'
                                        onclick="submitUpdateForm('{{ urlSafeEncrypt($item->id) }}')">
                                        <span class="txt">{{ __('Edit Offer') }}</span>
                                    </button>
                                   
                                    <button type="button" class="close-offer" style='margin-top:1.5%' 
                                        onclick="submitCancelForm('{{ urlSafeEncrypt($item->id) }}')">
                                        <span class="txt">{{ __('Close Offer') }}</span>
                                    </button>
                                <!-- <div class="dt-col m-first"> -->
                                    <!-- <button type="button" class="close-offer" 
                                        onclick="submitCancelForm('{{ urlSafeEncrypt($item->id) }}')">
                                        <span class="txt">{{ __('Close Offer') }}</span>
                                    </button> -->
                                <!-- </div> -->

                                <!-- <div class="dt-col m-second">
                                    <p class="info-txt small">{{ __('escrow_message') }}</p>
                                </div> -->
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
                                    <button type="button" class="btn-buy" onclick="submitBuyForm()">
                                        <span class="txt">{{ __('Buy Now') }}</span>
                                    </button>
                                </div>

                                <div class="dt-col m-second">
                                    <p class="info-txt small">{{ __('escrow_message') }}</p>
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
                            <div class="dt-tit">{{ __('Seller') }}</div>
                            <div class="border-box">
                                <div class="user">
                                    <div class="user-img"><img src="{{ $item->user->userImage }}"></div>
                                    <div class="user-info">
                                        <div class="user-name-q">
                                            <span class="user-name">{{ $item->user->username }}</span>
                                        </div>

                                        <div class="log online">{{ $item->user->status == 1 ? 'Online' : 'Offline' }}</div>
                                        <div class="log last">{{ __('Last Login At') }}:
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
                                    <span >
                                      <img src="{{ ($item->user->kyc_verified == 1)? '/pub/img/mm-kyc-on.png':'/pub/img/mm-kyc.png' }}" style='width:12%'>{{ $item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified') }}
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
    <!-- 바로 구매 버튼 클릭후 나오는 거래정보 팝업창 -->
    <div class="pop-sheet" data-pop-sheet="buy_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('구매하기-구매자 거래정보') }}</h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="dt-pop">
                        <ul class="summary">
                            <li>
                                <span class="label">{{ __('Status') }}</span>
                                <div class="data">
                                    <span class="data-txt going">{{ __('In Progress') }}</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Seller Name') }}</span>
                                <div class="data">
                                    <span class="data-txt">{{ $item->user->realname ?? $item->user->username }}</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Buyer Name') }}</span>
                                <div class="data">
                                    <span
                                        class="data-txt">{{ auth()->user()->realname ?? auth()->user()->username }}</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Offered Price') }}</span>
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
                                <span class="label">{{ __('Purchase Amount') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="purchaseAmount"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('USDT Purchase Quantity') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="usdtQuantity"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Purchase Time Limit') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="offerTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Trade End Time') }}</span>
                                <div class="data">
                                    <span class="data-txt" id="endTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Transaction Bank') }}</span>
                                <div class="data">
                                    <span class="data-txt">{{ $item->bankName }}({{ $item->accountNumber }})
                                        {{ $item->accountName }}</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div class="buy-row" style="{{ empty($item->payQR) ? 'display: none;' : '' }}">
                                <span class="ps-row-tit">{{ __('QR Code') }}</span>
                                <div class="qr-img">
                                    <img src="{{ get_image($item->payQR, 'QRImage') }}" alt="{{ __('QR Code Image') }}">
                                </div>
                                <div class="qr-save-button" style="display: flex; flex-direction: column; align-items: center;">
                                    <button class="ps-process-btn w-150" onclick="saveQRCode()" style="{{ empty($item->payQR) ? 'display: none;' : '' }}">
                                        <span class="txt">{{ __('QR 코드 저장') }}</span>
                                    </button>
                                </div>
                            </div>

                            <div class="buy-row">
                                <span class="ps-row-tit">{{ __('Payment Proof') }}</span>
                                <div class="attach-img" id="attachedImage"></div>
                                <div class="attach-file">
                                    <p class="att-txt">
                                        {{ __('Only JPEG, JPG, PNG, GIF files are allowed for payment proof attachments.') }}
                                    </p>
                                    <input type="file" id="fileInput" accept="image/*" style="display: none;"
                                        name="imageProof" id="imageProof">
                                    <button type="button" class="btn-att"
                                        onclick="document.getElementById('fileInput').click();">
                                        <span class="txt">+ {{ __('Select File') }}</span>
                                    </button>
                                </div>

                                <div class="waiting" style="display:none;">
                                    <p class="waiting-txt">{{ __('테더 입금 대기 중') }}</p>
                                </div>
                                <div class="waiting" id="process_step3" style="display:none;">
                                    <p class="waiting-txt">{{ __('테더 입금 완료') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process" id="process_step1">
                    <button class="ps-process-btn cancel w-200" onclick="cancelTransaction()">
                        <span class="txt">{{ __('Cancel Transaction') }}</span>
                    </button>
                    <button class="ps-process-btn w-200" onclick="confirmTransaction()">
                        <span class="txt">{{ __('Confirm Transfer and Send Proof') }}</span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id='chatButtonSell' onclick="openChat('sell')">
                        <span class="txt">{{ __('채팅') }}</span>
                    </button>
                </div>

                <div class="ps-process" id="process_step2" style="display:none;">
                    <button id="transaction_finished" class="ps-process-btn cancel w-200"
                        onclick="transactionFinished()">
                        <span class="txt">{{ __('거래 완료') }}</span>
                    </button>
                    <button class="ps-process-btn black w-200" onclick="confirmDispute();">
                        <span class="txt">{{ __('분쟁요청') }}</span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id='chatButtonSell' onclick="openChat()">
                        <span class="txt">{{ __('채팅') }}</span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('buy_detail')"><img
                        src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="{{ __('Close Pop-up') }}"></button>
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

        .qr-save-button {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
@endpush

@push('script')
    <script>
        let itemId;
        let orderId;

        var msg = '{{ Session::get('alert') }}';
        var exist = '{{ Session::has('alert') }}';
        if (exist) {
            setTimeout(() => {
                alert(msg);
            }, 1000);

        }

        function submitCancelForm(itemId) {
            console.log('submitCancelForm', itemId);

            if (confirm("{{ __('거래를 삭제 하시겠습니까?') }}")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.sell-list.canceloffer') }}";
                form.style.display = 'none';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                var itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'item_id';
                itemIdInput.value = itemId;
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
                            window.location.href = "{{ setRoute('user.sell-list.index') }}";
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

        function submitBuyForm() {
            // 폼 제출 전 유효성 검사 등을 수행할 수 있습니다.
            let payAmount = document.getElementById('payAmount').value;
            let recvAmount = document.getElementById('recvAmount').value;
            let passwordInput = document.getElementById('password');
            let itemPassword = @json($item->password);

            if (payAmount === '' || recvAmount === '') {
                alert('{{ __('Please enter both payment amount and purchase amount.') }}');
                return;
            }

            if (itemPassword !== null) {
                if (!passwordInput || passwordInput.value !== itemPassword) {
                    alert('{{ __('Incorrect password. Please try again.') }}');
                    return;
                }
            }

            if (confirm('{{ __('Are you sure you want to proceed with this purchase?') }}')) {
                // 폼 제출
                // document.getElementById('buyForm').submit();
                let form = document.getElementById('buyForm');
                let formData = new FormData(form);

                fetch('{{ route('user.sell-list.buy') }}', {
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
                            updatePopSheetContent();
                            openPopSheet('buy_detail');

                            // document.getElementById('start-chat-header').click();
                        } else {
                            console.log(result);
                            alert(result.message);
                        }

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error);
                        // let messageElement = document.getElementById('buyResultMessage');
                        // messageElement.innerHTML =
                        //     '<div class="alert alert-danger">An error occurred while processing your request.</div>';
                        // $('#buyResultModal').modal('show');
                    });
            }
        }

        function onPayAmountChange() {
            let payAmount = document.getElementById('payAmount').value;
            let recvAmountElement = document.getElementById('recvAmount');

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
                let recvAmount = payAmount / (priceInt * multiplier);

                recvAmountElement.value = recvAmount.toFixed(2);
            }
        }

        function onRecvAmountChange() {
            let payAmountElement = document.getElementById('payAmount');
            let recvAmount = document.getElementById('recvAmount').value;

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
                let payAmount = recvAmount * priceInt * multiplier;

                payAmountElement.value = payAmount.toFixed(2);
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

        function updatePopSheetContent() {
            let payAmount = parseFloat(document.getElementById('payAmount').value);
            let recvAmount = parseFloat(document.getElementById('recvAmount').value);

            document.getElementById('purchaseAmount').textContent =
                `${payAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} {{ __('KRW') }}`;
            document.getElementById('usdtQuantity').textContent = recvAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            let now = new Date();
            document.getElementById('offerTime').textContent = now.toLocaleString();

            let endTime = new Date(now.getTime() + {{ $item->offerTimeLimit }} * 60000);
            document.getElementById('endTime').textContent = endTime.toLocaleString();
            openChat();
        }

        document.getElementById('fileInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('attachedImage').innerHTML =
                        `<img src="${e.target.result}" alt="Attached Image">`;
                };
                reader.readAsDataURL(file);
            }
        });

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

        // function startDepositCheck() {
        //     let checkInterval;
        //     let attempts = 0;
        //     const maxAttempts = 60 * 10; // 10분

        //     function checkDeposit() {
        //         fetch(`{{ route('user.wallet.checkRecentDeposit') }}`, {
        //                 method: 'GET',
        //                 headers: {
        //                     'X-CSRF-TOKEN': "{{ csrf_token() }}"
        //                 }
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     // Deposit found
        //                     clearInterval(checkInterval);

        //                     const formattedAmount = parseFloat(data.amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
        //                         '$&,');
                            
        //                     // alert(`'${data.sender_realname}' {{ __('구매하신') }} ${formattedAmount} {{ __('USDT has been deposited') }}`);
        //                     //alert(`{{ __('Purchased') }} ${formattedAmount} {{ __('USDT has been deposited') }}`);
        //                     // Update UI
        //                     document.getElementById('usdtQuantity').textContent = formattedAmount;
        //                     document.querySelector('.waiting').style.display = 'none';
        //                     document.querySelector('#process_step3').style.display = '';
        //                     document.querySelector('#transaction_finished').style.display = 'none';
        //                 } else {
        //                     attempts++;
        //                     if (attempts >= maxAttempts) {
        //                         clearInterval(checkInterval);
        //                         // alert(
        //                         //     "No matching deposit found after 120 seconds. Please try again or contact support."
        //                         // );
        //                     }
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error('Error:', error);
        //                 clearInterval(checkInterval);
        //                 alert("An error occurred while checking for deposits. " + error);
        //             });
        //     }

        //     checkInterval = setInterval(checkDeposit, 1000); // Check every 1 second
        // }

        function confirmTransaction() {
            if (confirm("{{ __('업로드한 이체증빙을 전송 하시겠습니까?') }}")) {
                let fileInput = document.getElementById('fileInput');
                let file = fileInput.files[0];

                // if (!file) {
                //     alert("{{ __('Please select a file for proof of payment.') }}");
                //     return;
                // }

                if (!itemId) {
                    return alert("거래ID가 잘못됬습니다.");
                }
                let formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('_method', 'PUT');
                formData.append('itemId', itemId);
                formData.append('imageProof', file);

                fetch("{{ setRoute('user.mytrade.uploadproof') }}", {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector('.attach-file').style.display = 'none';
                            document.querySelector('.waiting').style.display = '';
                            document.querySelector('#process_step1').style.display = 'none';
                            document.querySelector('#process_step2').style.display = '';
                            document.querySelector('#transaction_finished').style.display = 'none';

                            //startDepositCheck();
                        } else {
                            console.log(data);
                            alert(data.message || "{{ __('An error occurred while uploading the proof.') }}");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('An error occurred while processing your request.') }}");
                    });
            }
        }

        function transactionFinished() {
            closePopSheet('buy_detail');

            if (confirm("{{ __('거래를 완료 하시겠습니까?') }}")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ setRoute('user.mytrade.transactionFinished') }}";
                form.style.display = 'none';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                var itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'item_id';
                itemIdInput.value = itemId;
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
                            window.location.href = "{{ setRoute('user.wallet.index') }}";
                        } else {
                            // 실패 시 에러 메시지 표시
                            alert(data.message || "{{ __('An error occurred while finishing the transaction.') }}");
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

        function saveQRCode() {
            const qrImage = document.querySelector('[data-pop-sheet="buy_detail"] .qr-img img');
            if (qrImage.style.display === 'none') {
                return alert(`{{ __('등록된 이미지가 없습니다.') }}`);
            };

            const imageUrl = qrImage.src;
            
            // 이미지 URL에서 파일 이름 추출
            const fileName = imageUrl.split('/').pop() || 'qr-code.jpg';

            // 이미지를 Blob으로 변환
            fetch(imageUrl)
                .then(response => response.blob())
                .then(blob => {
                    // Blob을 사용하여 다운로드 링크 생성
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = fileName;
                    
                    // 링크를 문서에 추가하고 클릭 이벤트 발생
                    document.body.appendChild(a);
                    a.click();
                    
                    // cleanup
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Error saving QR code:', error);
                    alert("{{ __('QR 코드 저장 중 오류가 발생했습니다.') }}");
                });
        }

        function submitUpdateForm(id){
            location.href='/user/make-offer/moveUpdateForm/'+id;
        }

    </script>
@endpush
