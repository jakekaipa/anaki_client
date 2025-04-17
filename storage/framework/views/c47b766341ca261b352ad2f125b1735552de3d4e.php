<?php
    $default = get_default_language_code();
?>


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

<?php $__env->startSection('content'); ?>
    <div class="content pc-dt">
        <h2 class="page-tit"><?php echo e(__('얼마를 구매 하시겠어요?')); ?></h2>

        <div class="container-row">
            <div class="card-box">
                <div class="dt-inner">
                    <form id="buyForm" class="card-form" action="<?php echo e(setRoute('user.sell-list.buy')); ?>" method="POST"
                        autocomplete="off">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="item_id" value="<?php echo e(urlSafeEncrypt($item->id)); ?>">
                        <input type="hidden" name="price" value="<?php echo e($item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice); ?>">

                        <div class="dt-row">
                            
                            <div class="dt-col">
                            <?php if(!$isOwner): ?>
                                <label for="price" class="dt-label"><?php echo e(__('Purchase Amount')); ?></label>
                                <div class="dt-inp">
                                    <input type="text" id="payAmount" name="payAmount" class="input"
                                        placeholder="<?php echo e(__('enter_amount')); ?>" oninput="onPayAmountChange()">
                                    <span class="unit"><?php echo e(__('KRW')); ?></span>
                                </div>
                                <p class="caution-txt"><?php echo e(__('start_transaction_message')); ?></p>
                           
                            </div>

                            <div class="dt-col">
                           
                                <label for="receipt" class="dt-label"><?php echo e(__('USDT Amount')); ?></label>
                                <div class="dt-inp">
                                    <input type="text" id="recvAmount" name="recvAmount" class="input"
                                        placeholder="<?php echo e(__('enter_quantity')); ?>" oninput="onRecvAmountChange()">
                                    <span class="unit">USDT</span>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>

                        <?php if($isOwner): ?>
                            <div class="dt-row buy-btn">

                                <!-- <div class="dt-col m-first"> -->
                                    <!-- <button type="button" class="close-offer" style='vbackground-color:#21b8a1;'
                                        onclick="submitUpdateForm('<?php echo e(urlSafeEncrypt($item->id)); ?>')">
                                        <span class="txt"><?php echo e(__('Update')); ?></span>
                                    </button> -->
                                <!-- </div> -->

                                    <button type="button" class="close-offer" style='background-color:#21b8a1;margin-top:1.5%'
                                        onclick="submitUpdateForm('<?php echo e(urlSafeEncrypt($item->id)); ?>')">
                                        <span class="txt"><?php echo e(__('Edit Offer')); ?></span>
                                    </button>
                                   
                                    <button type="button" class="close-offer" style='margin-top:1.5%' 
                                        onclick="submitCancelForm('<?php echo e(urlSafeEncrypt($item->id)); ?>')">
                                        <span class="txt"><?php echo e(__('Close Offer')); ?></span>
                                    </button>
                                <!-- <div class="dt-col m-first"> -->
                                    <!-- <button type="button" class="close-offer" 
                                        onclick="submitCancelForm('<?php echo e(urlSafeEncrypt($item->id)); ?>')">
                                        <span class="txt"><?php echo e(__('Close Offer')); ?></span>
                                    </button> -->
                                <!-- </div> -->

                                <!-- <div class="dt-col m-second">
                                    <p class="info-txt small"><?php echo e(__('escrow_message')); ?></p>
                                </div> -->
                            </div>
                        <?php else: ?>
                            <div class="dt-row buy-btn">
                                <div class="dt-col m-first">
                                    <?php if($item->password != null): ?>
                                        <div class="appoint">
                                            <span class="txt"><?php echo e(__('Private Trade')); ?></span>
                                            <div class="input">
                                                <input type="password" class="inp-txt"
                                                    placeholder="<?php echo e(__('Pin Number')); ?>" name="password" id="password"
                                                    maxlength="4" pattern="\d{4}" inputmode="numeric"
                                                    onkeypress="return onlyNumberKey(event)"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                                    onfocus="this.removeAttribute('readonly');">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <button type="button" class="btn-buy" onclick="submitBuyForm()">
                                        <span class="txt"><?php echo e(__('Buy Now')); ?></span>
                                    </button>
                                </div>

                                <div class="dt-col m-second">
                                    <p class="info-txt small"><?php echo e(__('escrow_message')); ?></p>
                                </div>
                                
                            </div>
                        <?php endif; ?>
                    </form>

                    <div class="dt-row introduce">
                        <div class="dt-col">
                            <div class="dt-tit"><?php echo e(__('Transaction Information')); ?></div>
                            <div class="border-box">
                                <ul class="deal">
                                    <li>
                                        <span class="label"><?php echo e(__('seller_price')); ?></span>
                                        <div class="data big">
                                            <?php echo e($item->priceType == 0 ? number_format(($prices['KRW'] * (100 + $item->offerMargin)) / 100) : number_format($item->fixedPrice)); ?>

                                            <?php echo e(__('KRW')); ?>

                                            <?php if($item->priceType == 0): ?>
                                                <span
                                                    class="arrow <?php echo e($item->offerMargin >= 0 ? 'up' : 'down'); ?>"><?php echo e(abs($item->offerMargin)); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="label"><?php echo e(__('purchase_limit')); ?></span>
                                        <span class="data"><?php echo e(__('minimum')); ?> <?php echo e(number_format($item->tradeVolMin)); ?>

                                            <?php echo e(__('KRW')); ?> - <?php echo e(__('maximum')); ?>

                                            <?php echo e(number_format($item->tradeVolMax)); ?> <?php echo e(__('KRW')); ?></span>
                                    </li>
                                    <li>
                                        <span class="label"><?php echo e(__('trade_time_limit')); ?></span>
                                        <span class="data"><?php echo e($item->offerTimeLimit); ?><?php echo e(__('minutes')); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="dt-col">
                            <div class="dt-tit"><?php echo e(__('Seller')); ?></div>
                            <div class="border-box">
                                <div class="user">
                                    <div class="user-img"><img src="<?php echo e($item->user->userImage); ?>"></div>
                                    <div class="user-info">
                                        <div class="user-name-q">
                                            <span class="user-name"><?php echo e($item->user->username); ?></span>
                                        </div>

                                        <div class="log online"><?php echo e($item->user->status == 1 ? 'Online' : 'Offline'); ?></div>
                                        <div class="log last"><?php echo e(__('Last Login At')); ?>:
                                            <?php
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
                                            ?>
                                        </div>
                                    </div>
                                </div>
                              
                                <!-- <div class="cerf">
                                    <span >
                                      <img src="<?php echo e(($item->user->kyc_verified == 1)? '/pub/img/mm-kyc-on.png':'/pub/img/mm-kyc.png'); ?>" style='width:12%'><?php echo e($item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified')); ?>

                                    </span>    
                                </div> -->
                              
                                <button class="verify-button<?php echo e(($item->user->kyc_verified == 0)? '-no':''); ?>"><?php echo e($item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified')); ?></button>
                                                        
                            </div>
                        </div>
                    </div>

                    <div class="dt-row condition">
                        <div class="dt-col w-full">
                            <div class="dt-tit"><?php echo e(__('Transaction Conditions')); ?></div>
                            <div class="border-box">
                                <ul class="term">
                                    <?php echo nl2br(e($item->offerCondition)); ?>

                                </ul>

                                <p class="term-txt"><?php echo e($item->transGuide); ?></p>
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
                    <h2 class="ps-tit"><?php echo e(__('구매하기-구매자 거래정보')); ?></h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="dt-pop">
                        <ul class="summary">
                            <li>
                                <span class="label"><?php echo e(__('Status')); ?></span>
                                <div class="data">
                                    <span class="data-txt going"><?php echo e(__('In Progress')); ?></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Seller Name')); ?></span>
                                <div class="data">
                                    <span class="data-txt"><?php echo e($item->user->realname ?? $item->user->username); ?></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Buyer Name')); ?></span>
                                <div class="data">
                                    <span
                                        class="data-txt"><?php echo e(auth()->user()->realname ?? auth()->user()->username); ?></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Offered Price')); ?></span>
                                <div class="data">
                                    <span class="data-price">
                                        <?php echo e($item->priceType == 0 ? number_format(($prices['KRW'] * (100 + $item->offerMargin)) / 100, 2) : number_format($item->fixedPrice, 2)); ?>

                                        <?php echo e(__('KRW')); ?>

                                        <?php if($item->priceType == 0): ?>
                                            <i
                                                class="<?php echo e($item->offerMargin >= 0 ? 'up' : 'down'); ?>"><?php echo e(abs($item->offerMargin)); ?>%</i>
                                        <?php endif; ?>
                                    </span>
                                    <p class="market-price"><?php echo e(__('Market Price')); ?>:
                                        <?php echo e(number_format($prices['KRW'], 2)); ?>

                                        <?php echo e(__('KRW')); ?></p>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Purchase Amount')); ?></span>
                                <div class="data">
                                    <span class="data-txt" id="purchaseAmount"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('USDT Purchase Quantity')); ?></span>
                                <div class="data">
                                    <span class="data-txt" id="usdtQuantity"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Purchase Time Limit')); ?></span>
                                <div class="data">
                                    <span class="data-txt" id="offerTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Trade End Time')); ?></span>
                                <div class="data">
                                    <span class="data-txt" id="endTime"></span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Transaction Bank')); ?></span>
                                <div class="data">
                                    <span class="data-txt"><?php echo e($item->bankName); ?>(<?php echo e($item->accountNumber); ?>)
                                        <?php echo e($item->accountName); ?></span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div class="buy-row" style="<?php echo e(empty($item->payQR) ? 'display: none;' : ''); ?>">
                                <span class="ps-row-tit"><?php echo e(__('QR Code')); ?></span>
                                <div class="qr-img">
                                    <img src="<?php echo e(get_image($item->payQR, 'QRImage')); ?>" alt="<?php echo e(__('QR Code Image')); ?>">
                                </div>
                                <div class="qr-save-button" style="display: flex; flex-direction: column; align-items: center;">
                                    <button class="ps-process-btn w-150" onclick="saveQRCode()" style="<?php echo e(empty($item->payQR) ? 'display: none;' : ''); ?>">
                                        <span class="txt"><?php echo e(__('QR 코드 저장')); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div class="buy-row">
                                <span class="ps-row-tit"><?php echo e(__('Payment Proof')); ?></span>
                                <div class="attach-img" id="attachedImage"></div>
                                <div class="attach-file">
                                    <p class="att-txt">
                                        <?php echo e(__('Only JPEG, JPG, PNG, GIF files are allowed for payment proof attachments.')); ?>

                                    </p>
                                    <input type="file" id="fileInput" accept="image/*" style="display: none;"
                                        name="imageProof" id="imageProof">
                                    <button type="button" class="btn-att"
                                        onclick="document.getElementById('fileInput').click();">
                                        <span class="txt">+ <?php echo e(__('Select File')); ?></span>
                                    </button>
                                </div>

                                <div class="waiting" style="display:none;">
                                    <p class="waiting-txt"><?php echo e(__('테더 입금 대기 중')); ?></p>
                                </div>
                                <div class="waiting" id="process_step3" style="display:none;">
                                    <p class="waiting-txt"><?php echo e(__('테더 입금 완료')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process" id="process_step1">
                    <button class="ps-process-btn cancel w-200" onclick="cancelTransaction()">
                        <span class="txt"><?php echo e(__('Cancel Transaction')); ?></span>
                    </button>
                    <button class="ps-process-btn w-200" onclick="confirmTransaction()">
                        <span class="txt"><?php echo e(__('Confirm Transfer and Send Proof')); ?></span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id='chatButtonSell' onclick="openChat('sell')">
                        <span class="txt"><?php echo e(__('채팅')); ?></span>
                    </button>
                </div>

                <div class="ps-process" id="process_step2" style="display:none;">
                    <button id="transaction_finished" class="ps-process-btn cancel w-200"
                        onclick="transactionFinished()">
                        <span class="txt"><?php echo e(__('거래 완료')); ?></span>
                    </button>
                    <button class="ps-process-btn black w-200" onclick="confirmDispute();">
                        <span class="txt"><?php echo e(__('분쟁요청')); ?></span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id='chatButtonSell' onclick="openChat()">
                        <span class="txt"><?php echo e(__('채팅')); ?></span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('buy_detail')"><img
                        src="<?php echo e(asset('/public/pub')); ?>/img/close-popup@2x.png" alt="<?php echo e(__('Close Pop-up')); ?>"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        let itemId;
        let orderId;

        var msg = '<?php echo e(Session::get('alert')); ?>';
        var exist = '<?php echo e(Session::has('alert')); ?>';
        if (exist) {
            setTimeout(() => {
                alert(msg);
            }, 1000);

        }

        function submitCancelForm(itemId) {
            console.log('submitCancelForm', itemId);

            if (confirm("<?php echo e(__('거래를 삭제 하시겠습니까?')); ?>")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "<?php echo e(setRoute('user.sell-list.canceloffer')); ?>";
                form.style.display = 'none';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "<?php echo e(csrf_token()); ?>";
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
                            window.location.href = "<?php echo e(setRoute('user.sell-list.index')); ?>";
                        } else {
                            // 실패 시 에러 메시지 표시
                            alert(data.message || "<?php echo e(__('An error occurred while closing the offer.')); ?>");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
                    });

                document.body.removeChild(form);
            }
        }

        function submitBuyForm() {
            // 폼 제출 전 유효성 검사 등을 수행할 수 있습니다.
            let payAmount = document.getElementById('payAmount').value;
            let recvAmount = document.getElementById('recvAmount').value;
            let passwordInput = document.getElementById('password');
            let itemPassword = <?php echo json_encode($item->password, 15, 512) ?>;

            if (payAmount === '' || recvAmount === '') {
                alert('<?php echo e(__('Please enter both payment amount and purchase amount.')); ?>');
                return;
            }

            if (itemPassword !== null) {
                if (!passwordInput || passwordInput.value !== itemPassword) {
                    alert('<?php echo e(__('Incorrect password. Please try again.')); ?>');
                    return;
                }
            }

            if (confirm('<?php echo e(__('Are you sure you want to proceed with this purchase?')); ?>')) {
                // 폼 제출
                // document.getElementById('buyForm').submit();
                let form = document.getElementById('buyForm');
                let formData = new FormData(form);

                fetch('<?php echo e(route('user.sell-list.buy')); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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

            let price = <?php echo json_encode($item->fixedPrice, 15, 512) ?>;
            let offerMargin = <?php echo json_encode($item->offerMargin, 15, 512) ?>;
            let priceType = <?php echo json_encode($item->priceType, 15, 512) ?>;
            if (priceType == 0) {
                price = <?php echo json_encode($prices['KRW'], 15, 512) ?>;
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

            let price = <?php echo json_encode($item->fixedPrice, 15, 512) ?>;
            let offerMargin = <?php echo json_encode($item->offerMargin, 15, 512) ?>;
            let priceType = <?php echo json_encode($item->priceType, 15, 512) ?>;
            if (priceType == 0) {
                price = <?php echo json_encode($prices['KRW'], 15, 512) ?>;
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
                        alert("<?php echo e(__('error_downloading_attachment')); ?>");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("<?php echo e(__('error_downloading_attachment')); ?>");
                });

            return false;
        }

        function updatePopSheetContent() {
            let payAmount = parseFloat(document.getElementById('payAmount').value);
            let recvAmount = parseFloat(document.getElementById('recvAmount').value);

            document.getElementById('purchaseAmount').textContent =
                `${payAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} <?php echo e(__('KRW')); ?>`;
            document.getElementById('usdtQuantity').textContent = recvAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            let now = new Date();
            document.getElementById('offerTime').textContent = now.toLocaleString();

            let endTime = new Date(now.getTime() + <?php echo e($item->offerTimeLimit); ?> * 60000);
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
            let reason = prompt("<?php echo e(__('거래 취소 사유를 적어주세요.')); ?>");
            if (reason === null) {
                return; // User cancelled the prompt
            }

            if (confirm("<?php echo e(__('거래를 정말 취소 하시겠습니까?')); ?>")) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "<?php echo e(setRoute('user.mytrade.cancel')); ?>";
                form.style.display = 'none';

                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "<?php echo e(csrf_token()); ?>";
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
                            // alert("<?php echo e(__('Transaction cancelled successfully.')); ?>");
                            closePopSheet('buy_detail');
                            // You might want to refresh the page or update the UI here
                            window.location.href = '<?php echo e(route('user.dashboard')); ?>';
                        } else {
                            alert(data.message || "<?php echo e(__('An error occurred while cancelling the transaction.')); ?>");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
                    });

                document.body.removeChild(form);
            }
        }

        // function startDepositCheck() {
        //     let checkInterval;
        //     let attempts = 0;
        //     const maxAttempts = 60 * 10; // 10분

        //     function checkDeposit() {
        //         fetch(`<?php echo e(route('user.wallet.checkRecentDeposit')); ?>`, {
        //                 method: 'GET',
        //                 headers: {
        //                     'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
        //                 }
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     // Deposit found
        //                     clearInterval(checkInterval);

        //                     const formattedAmount = parseFloat(data.amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
        //                         '$&,');
                            
        //                     // alert(`'${data.sender_realname}' <?php echo e(__('구매하신')); ?> ${formattedAmount} <?php echo e(__('USDT has been deposited')); ?>`);
        //                     //alert(`<?php echo e(__('Purchased')); ?> ${formattedAmount} <?php echo e(__('USDT has been deposited')); ?>`);
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
            if (confirm("<?php echo e(__('업로드한 이체증빙을 전송 하시겠습니까?')); ?>")) {
                let fileInput = document.getElementById('fileInput');
                let file = fileInput.files[0];

                // if (!file) {
                //     alert("<?php echo e(__('Please select a file for proof of payment.')); ?>");
                //     return;
                // }

                if (!itemId) {
                    return alert("거래ID가 잘못됬습니다.");
                }
                let formData = new FormData();
                formData.append('_token', "<?php echo e(csrf_token()); ?>");
                formData.append('_method', 'PUT');
                formData.append('itemId', itemId);
                formData.append('imageProof', file);

                fetch("<?php echo e(setRoute('user.mytrade.uploadproof')); ?>", {
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
                            alert(data.message || "<?php echo e(__('An error occurred while uploading the proof.')); ?>");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
                    });
            }
        }

        function transactionFinished() {
            closePopSheet('buy_detail');

            if (confirm("<?php echo e(__('거래를 완료 하시겠습니까?')); ?>")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "<?php echo e(setRoute('user.mytrade.transactionFinished')); ?>";
                form.style.display = 'none';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "<?php echo e(csrf_token()); ?>";
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
                            window.location.href = "<?php echo e(setRoute('user.wallet.index')); ?>";
                        } else {
                            // 실패 시 에러 메시지 표시
                            alert(data.message || "<?php echo e(__('An error occurred while finishing the transaction.')); ?>");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
                    });

                document.body.removeChild(form);
            }
        }

        function openChat() {
            $('.chat-layer').show();
            window.startChat(orderId, '<?php echo e($item->user->id); ?>');
        }

        function closeChat() {
            $('.chat-layer').hide();
        }

        function confirmDispute() {
            const message = "<?php echo e(__('\"분쟁 요청\" 시 거래가 해결될 때까지 한시적으로 정지 됩니다. 고객센터에 접수해 주세요.')); ?>";
            if (confirm(message)) {
                // '<?php echo e(urlSafeEncrypt($item->id)); ?>'
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "<?php echo e(setRoute('user.mytrade.dispute')); ?>";
                form.style.display = 'none';

                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "<?php echo e(csrf_token()); ?>";
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
                            window.location.href = '<?php echo e(setRoute('user.cs.index')); ?>';
                        } else {
                            alert(data.message || "<?php echo e(__('An error occurred while disputing the transaction.')); ?>");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
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
                return alert(`<?php echo e(__('등록된 이미지가 없습니다.')); ?>`);
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
                    alert("<?php echo e(__('QR 코드 저장 중 오류가 발생했습니다.')); ?>");
                });
        }

        function submitUpdateForm(id){
            location.href='/user/make-offer/moveUpdateForm/'+id;
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/sell-list/preview.blade.php ENDPATH**/ ?>