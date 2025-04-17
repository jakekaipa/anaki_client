<?php
    $default = get_default_language_code();
?>


<?php $__env->startSection('content'); ?>
    

    <div class="content">
        <h2 class="page-tit"><?php echo e(__('Dashboard')); ?></h2>

        <div class="container-row card-box-wrap mt-0">
            <div class="card-box tether h-132" onclick="window.location.href = '<?php echo e(route('user.wallet.index')); ?>';"
                style="cursor: pointer;">
                <strong class="cb-tit"><?php echo e(__('Tether Balance')); ?></strong>
                <p class="tether-dt"><strong>$&nbsp<?php echo e(number_format($usdtBalance, 2)); ?></strong></p>
                <p class="pending"><?php echo e(__('Pending')); ?> : $&nbsp<?php echo e(number_format($usdtPending, 2)); ?></p>
            </div>

            <div class="card-box ico ico-tip h-132">
                <strong class="cb-tit"><?php echo e(__('Ongoing Trade')); ?></strong>
                <p class="cb-info"><?php echo e($ongoing_trade > 0 ? $ongoing_trade : __('No ongoing trades')); ?></p>
            </div>
        </div>

        <div class="container-row card-box-wrap">
            <div class="card-box ico ico-cs h-150" onclick="window.location.href = '<?php echo e(route('user.cs.index')); ?>';"
                style="cursor: pointer;">
                <strong class="cb-tit"><?php echo e(__('Customer Service')); ?></strong>
                <p class="cs-cnt"><strong><?php echo e($active_ticket); ?></strong> <?php echo e(__('case(s)')); ?></p>
            </div>

            <div class="card-box ico ico-mauth h-150" onclick="window.location.href = '<?php echo e(route('user.authorize.kyc')); ?>';"
                style="cursor: pointer;">
                <strong class="cb-tit"><?php echo e(__('KYC Verification')); ?></strong>
                <p class="mauth-txt <?php echo e($user->kyc_verified != 0 ? 'verified' : ''); ?>"><?php echo e($user->kyc_verified != 0 ? __('Verified') : __('Unverified')); ?></p>
            </div>

            <div class="card-box ico ico-google h-150"
                onclick="window.location.href = '<?php echo e(route('user.security.google.2fa')); ?>';" style="cursor: pointer;">
                <strong class="cb-tit"><?php echo e(__('2FA Verification')); ?></strong>
                <p class="gg-info <?php echo e($user->two_factor_verified > 0 ? 'verified' : ''); ?>">
                    <?php echo e($user->two_factor_verified > 0 ? __('2FA is activated') : __('2FA is not activated')); ?></p>
                <?php if($user->two_factor_verified == 0): ?>
                    <a href="<?php echo e(setRoute('user.security.google.2fa')); ?>"
                        class="gg-auth-link"><?php echo e(__('Activate Google 2FA')); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="container-row">
            <div class="card-box">
                <strong class="cb-tit"><?php echo e(__('Ongoing Trade')); ?></strong>
                <div class="table-wrap">
                    <table class="tbl-list dsbo-tbl">
                        <colgroup>
                            <col class="col1">
                            <col class="col2">
                            <col class="col3">
                            <col class="col4">
                            <col class="col5">
                            <col class="col6">
                            <col class="col7">
                            <col class="col8">
                            <col class="col9">
                        </colgroup>

                        <thead>
                            <tr>
                                <th><?php echo e(__('거래구분')); ?></th>
                                <th><?php echo e(__('거래자 이름')); ?></th>
                                <th><?php echo e(__('Tether')); ?></th>
                                <th><?php echo e(__('거래금액')); ?></th>
                                <th><?php echo e(__('제안단가')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th><?php echo e(__('거래 시작 시간')); ?></th>
                                <th><?php echo e(__('거래 종료 시간')); ?></th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="trade-list-container">
                            <?php if(isset($listData) && $listData->count() > 0): ?>
                                <?php echo $__env->make('user.dashboard.partials.trade-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div style="margin-top: 20px;" id="load-more-container">
            <button class="btn-other btn-small" id="load-more" style="padding-left: 30px; padding-right:30px;">
                <span class="txt"><?php echo e(__('Load More Trades')); ?></span>
            </button>
        </div>
        
    </div>

    <!-- pop-sheet   -->
    <div class="pop-sheet" data-pop-sheet="sell_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                   
                    <h2 class="ps-tit" id='sell_title'><?php echo e(__('거래 상세 정보')); ?></h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="dt-pop">
                        <ul class="summary">
                            <li>
                                <span class="label"><?php echo e(__('상태')); ?></span>
                                <div class="data">
                                    <span class="data-txt going">진행중</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('판매자 이름')); ?></span>
                                <div class="data">
                                    <span class="data-txt">gonikim</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('구매자 이름')); ?></span>
                                <div class="data">
                                    <span class="data-txt">jaeookryu</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('Offered Price')); ?></span>
                                <div class="data">
                                    <span class="data-price">
                                        991,434 KRW
                                        <i class="up">9.55</i>
                                        <!-- 하락인 경우 -->
                                        <!-- <i class="down">9.55</i> -->
                                        <!-- 하락인 경우 -->
                                    </span>
                                    <p class="market-price"><?php echo e(__('시장가')); ?>: 1376 KRW</p>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('판매금액')); ?></span>
                                <div class="data">
                                    <span class="data-txt">100,000 KRW</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"id='quantity'><?php echo e(__('테더 판매 수량')); ?></span>
                                <div class="data">
                                    <span class="data-txt">68.28</span>
                                </div> 
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('구매 제안 시간')); ?></span>
                                <div class="data">
                                    <span class="data-txt">2024.08.03 11:41</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('거래 종료 시간')); ?></span>
                                <div class="data">
                                    <span class="data-txt">2024.08.04 12:11</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('거래은행')); ?></span>
                                <div class="data">
                                    <span class="data-txt">카카오뱅크(3333027208895) 홍길동</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div class="buy-row">
                                <span class="ps-row-tit"><?php echo e(__('입금증빙')); ?></span>
                                <div class="pay-proof-img attach-img"><img src="" alt="<?php echo e(__('입금 증빙 이미지')); ?>"
                                        style="display: none;"></div>
                            </div>
                        </div>

                        <div class="dt-row">
                            <div class="my-balance">
                                <span class="ps-row-tit"><?php echo e(__('내 잔고')); ?></span>

                                <div class="mb-data">
                                    <div class="mb-data1">9,400.<small>32</small> USDT</div>
                                    <div class="mb-data2">pending:61276.686102485</div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-row pd-h25">
                            <div class="seller-btn-wrap flex-c-s">
                                <input type="hidden" id="email" readonly title="<?php echo e(__('거래자 이메일입니다.')); ?>" value="">
                                <button id="send_tether" class="ps-process-btn w-200" onclick="sendTether();" disabled>
                                    <span class="txt"><?php echo e(__('테더 이체')); ?></span>
                                </button>
                            </div>

                            <div class="mb-txt flex-c-s">
                                <p class="info-txt small c666"><?php echo e(__('※ 구매자가 허위 입금증을 보내거나 입금확인이 안될 경우,고객센터에 분쟁 요청을 하면 거래가 중지되며, 소명자료 확인 후 정상처리가 됩니다.')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn cancel w-200" onclick="cancelTransaction()">
                        <span class="txt"><?php echo e(__('Cancel Transaction')); ?></span>
                    </button>
                    <button class="ps-process-btn black w-200" id='disputeButton' onclick="confirmDispute();">
                        <span class="txt"><?php echo e(__('분쟁요청')); ?></span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id="chatButtonSell" onclick="openChat('sell')">
                        <span class="txt"><?php echo e(__('채팅')); ?></span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('sell_detail')"><img
                        src="<?php echo e(asset('/public/pub')); ?>/img/close-popup@2x.png" alt="팝업시트 닫기"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->

    <!-- pop-sheet  -->
    <div class="pop-sheet" data-pop-sheet="buy_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit" id='buy_title'><?php echo e(__('거래 상세 정보')); ?></h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="dt-pop">
                        <ul class="summary">
                            <li>
                                <span class="label"><?php echo e(__('상태')); ?></span>
                                <div class="data">
                                    <span class="data-txt going">진행중</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('판매자 이름')); ?></span>
                                <div class="data">
                                    <span class="data-txt">gonikim</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('구매자 이름')); ?></span>
                                <div class="data">
                                    <span class="data-txt">jaeookryu</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('제안가')); ?></span>
                                <div class="data">
                                    <span class="data-price">
                                        991,434 KRW
                                        <i class="up">9.55</i>
                                        <!-- 하락인 경우 -->
                                        <!-- <i class="down">9.55</i> -->
                                        <!-- 하락인 경우 -->
                                    </span>
                                    <p class="market-price">시장가: 1376 KRW</p>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('구매금액')); ?></span>
                                <div class="data">
                                    <span class="data-txt">100,000 KRW</span>
                                </div>
                            </li>
                            <li>
                                <span class="label" ><?php echo e(__('테더 구매 수량')); ?></span>
                                <div class="data">
                                    <span class="data-txt">68.28</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('구매 제안 시간')); ?></span>
                                <div class="data">
                                    <span class="data-txt">2024.08.03 11:41</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('거래 종료 시간')); ?></span>
                                <div class="data">
                                    <span class="data-txt">2024.08.04 12:11</span>
                                </div>
                            </li>
                            <li>
                                <span class="label"><?php echo e(__('거래은행')); ?></span>
                                <div class="data">
                                    <span class="data-txt">카카오뱅크(3333027208895) 홍길동</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div id="qr-code" class="buy-row">
                                <span class="ps-row-tit">QR Code</span>
                                <div class="qr-img">
                                    <img src="<?php echo e(asset('/public/pub')); ?>/img/qr-code@2x.jpg" alt="<?php echo e(__('qr코드 이미지')); ?>">
                                </div>
                                <div class="qr-save-button" style="display: flex; flex-direction: column; align-items: center;">
                                    <button class="ps-process-btn w-150" onclick="saveQRCode()">
                                        <span class="txt"><?php echo e(__('QR 코드 저장')); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div class="buy-row">
                                <span class="ps-row-tit"><?php echo e(__('지급 증빙')); ?></span>

                                <div class="attach-img" id="attachedImage"></div>
                                <div class="attach-file">
                                    <p class="att-txt"><?php echo e(__('※ 지급 증명 첨부 파일은 확장자 JPEG, JPG, PNG, GIF 파일만 가능합니다.')); ?></p>
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
                    <button id="cancel_transaction" class="ps-process-btn cancel w-200" onclick="cancelTransaction()">
                        <span class="txt"><?php echo e(__('거래 취소')); ?></span>
                    </button>
                    <button id="confirm_transaction" class="ps-process-btn w-200" onclick="confirmTransaction()">
                        <span class="txt"><?php echo e(__('이체완료 및 증빙자료전송')); ?></span>
                    </button>
                    <button class="ps-process-btn cancel w-200" id="chatButtonBuy1" onclick="openChat('buy')">
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
                    <button class="ps-process-btn cancel w-200" id='chatButtonBuy2' onclick="openChat('buy')">
                        <span class="txt"><?php echo e(__('채팅')); ?></span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('buy_detail')"><img
                        src="<?php echo e(asset('/public/pub')); ?>/img/close-popup@2x.png" alt="<?php echo e(__('팝업시트 닫기')); ?>"></button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <style>
        .pop-sheet {
            display: none;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .pop-sheet .ps-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.54);
        }

        .pop-sheet .ps-container {
            position: relative;
            width: 620px;
            max-width: 100%;
            padding: 35px;
            border-radius: 15px;
            background-color: #fff;
            transition: 0.3s;
        }

        .pop-sheet .ps-head {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 32px;
            border-bottom: 1px solid #d8d8d8;
        }

        .pop-sheet .ps-content {
            max-height: 60vh;
            overflow-y: auto;
        }

        .pop-sheet .ps-tit {
            font-weight: 700;
            font-size: 30px;
            line-height: 1;
        }

        .pop-sheet .ps-close {
            position: absolute;
            top: 35px;
            right: 35px;
            width: 28px;
            height: 28px;
        }

        .pop-sheet .ps-process {
            display: flex;
            justify-content: center;
            gap: 0 14px;
            padding-top: 40px;
        }

        .pop-sheet .ps-process-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 160px;
            height: 42px;
            border-radius: 6px;
            background-color: #21b8a1
        }

        .pop-sheet .ps-process-btn.cancel {
            background: #eaecef;
        }

        .pop-sheet .ps-process-btn.w-200 {
            width: 200px;
        }

        .pop-sheet .ps-process-btn .txt {
            color: #fff;
            font-weight: 500;
            font-size: 16px;
        }

        .pop-sheet .ps-process-btn.cancel .txt {
            color: #666;
        }

        @media screen and (max-width: 767px) {
            .pop-sheet .ps-container {
                width: 100%;
                padding: 20px 15px 17px;
                border-radius: 15px 15px 0 0;
            }

            .pop-sheet .ps-head {
                padding-bottom: 20px;
            }

            .pop-sheet .ps-content {
                max-height: 70vh;
            }

            .pop-sheet .ps-tit {
                font-size: 20px;
            }

            .pop-sheet .ps-close {
                top: 16px;
                right: 15px;
                width: 28px;
                height: 28px;
                padding: 0 5px;
            }

            .pop-sheet .ps-process {
                padding-top: 30px;
            }

            .pop-sheet .ps-process-btn {
                width: 100%;
                height: 38px;
            }

            .pop-sheet .ps-process-btn .txt {
                font-weight: 500;
                font-size: 14px;
            }
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
        let currentPage = 1;
        let orderId;
        let itemId;
        let chat_id;

        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadMoreItems);
            }

            const order_id = '<?php echo e($order_id); ?>';
            const encrypt_order_id = '<?php echo e(urlSafeEncrypt($order_id)); ?>';
            
            if (order_id && order_id.trim() !== '') {
                // console.log('Order ID:', order_id);
                showTradeDetails(order_id, encrypt_order_id, true);
            }
        });

        function loadMoreItems() {
            currentPage++;
            fetch(`<?php echo e(route('user.dashboard')); ?>?page=${currentPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    if (html.trim() === '') {
                        document.getElementById('load-more-container').style.display = 'none';
                        return;
                    }
                    const container = document.getElementById('trade-list-container');
                    container.insertAdjacentHTML('beforeend', html);

                    if (!document.querySelector('.purchase-more')) {
                        document.getElementById('load-more').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<?php echo e(__('An error occurred while loading data. Please try again.')); ?>');
                });
        }

        function showTradeDetails(_orderId, encryptOrderId, openchat) {
            // console.log(_orderId, encryptOrderId, openchat);
            orderId = _orderId;
            itemId = encryptOrderId;

            // AJAX request to fetch trade details
            fetch(`<?php echo e(route('user.mytrade.details', '')); ?>/${encryptOrderId}`)
                .then(response => response.json())
                .then(data => {
                    // console.log(JSON.stringify(data));
                    // console.log("data.order_type", data.order_type);
                    // console.log("data.is_buyer", data.is_buyer);
                    // console.log(data);
                    // console.log('chat_id', data.chat_id);

                    chat_id = data.chat_id;
                    window.receiverId = data.receiver_id;

                    if ((data.order_type === 'sell' && data.is_buyer == false) ||
                        (data.order_type === 'buy' && data.is_buyer)) {
                        updateSellDetailPopup(data);
                        openPopSheet('sell_detail');
                        
                        if (data.state === "open") {
                            document.getElementById('send_tether').disabled = true;
                        } else if (data.state === "send") {
                            document.getElementById('send_tether').disabled = false;
                        }
                        startPayProofCheck(encryptOrderId);
                    } else {
                        updateBuyDetailPopup(data);
                        openPopSheet('buy_detail');
                    }
                    
                      // 수신자New Chat 버튼 제거 및 바로 채팅창 오픈
                    var newChatSign = document.getElementById(chat_id+'-'+<?php echo e(Auth::user()->id); ?>);
                    if (newChatSign) {
                        startChatAfterDelay();
                        newChatSign.remove();
                    }

                    if (openchat) {
                        startChatAfterDelay();
                    }

                })
                .catch(error => console.error('Error:', error));
        }
        
        // 0.5초 딜레이후 채팅창 열기
        function startChatAfterDelay() {
            setTimeout(function() {
                window.startChat(orderId, window.receiverId);
            }, 500);
        }

        function updateSellDetailPopup(data) {
            console.log('sell');
            const statusElement = document.querySelector('[data-pop-sheet="sell_detail"] .data-txt.going');
           
            // 거래 상세 제목
            const sellTitle = document.querySelector('#sell_title');
           if(data.order_type === 'buy'){
                if(data.client_email === "<?php echo e(auth()->user()->email); ?>" ){
                    sellTitle.textContent = "<?php echo e(__('판매하기-판매자 거래정보')); ?>";
                } else {
                    sellTitle.textContent = "<?php echo e(__('판매하기-구매자 거래정보')); ?>";
                }
            } else {
                if(data.client_email === "<?php echo e(auth()->user()->email); ?>" ){
                    sellTitle.textContent = "<?php echo e(__('구매하기-구매자 거래정보')); ?>";
                } else {
                    sellTitle.textContent = "<?php echo e(__('구매하기-판매자 거래정보')); ?>";
                }
            }
        
            if (data.state === "open" || data.state === 'send') {
                statusElement.textContent = "<?php echo e(__('진행중')); ?>";
                statusElement.className = "data-txt going";
            } else if (data.state === "dispute") {
                statusElement.textContent = "<?php echo e(__('분쟁중')); ?>";
                statusElement.className = "data-txt going";
            } else if (data.state === "done") {
                statusElement.textContent = "<?php echo e(__('완료')); ?>";
                statusElement.className = "data-txt completed";
            } else if (data.state === "cancel") {
                statusElement.textContent = "<?php echo e(__('취소')); ?>";
                statusElement.className = "data-txt cancelled";
            }
            
            if(data.order_type === 'sell' ){
                document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(2) .data-txt').textContent = 
                data.offer_realname == null ? data.offer_username : data.offer_realname ;
                document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(3) .data-txt').textContent = 
                data.client_realname == null ? data.client_username :  data.client_realname ;
            } else {
                document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(2) .data-txt').textContent = 
                data.client_realname == null ? data.client_username :  data.client_realname ;
                document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(3) .data-txt').textContent = 
                data.offer_realname == null ? data.offer_username : data.offer_realname ;
            }
  
            // Update seller and buyer names
            // document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(2) .data-txt').textContent = 
            // data.offer_realname == null ? data.offer_username : data.offer_realname ;
            // document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(3) .data-txt').textContent = 
            // data.client_realname == null ? data.client_username :  data.client_realname ;

            //  현재 테터금액과 제안금액 차이( % or 금액)
            const priceElement = document.querySelector('[data-pop-sheet="sell_detail"] .data-price');
            const priceChange = parseFloat(data.price_change);
            let priceGap = '';
            // console.log(data.price_type+'////'+data.price_change+'/////'+data.margin);
            if(data.price_type == 0){ // 시장가
                priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>`;
            } else { // 고정가
                //priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${priceChange} <?php echo e(__('KRW')); ?></i>`;
                priceGap = '';
            }
            priceElement.innerHTML = `
                ${parseFloat(data.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} <?php echo e(__('KRW')); ?>

                ${priceGap}
            `;
            // <i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>
            
            document.querySelector('[data-pop-sheet="sell_detail"] .market-price').textContent =
                `<?php echo e(__('시장가')); ?>: ${parseFloat(data.market_price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} <?php echo e(__('KRW')); ?>`;

            // Update purchase amount and tether amount
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(5) .data-txt').textContent =
                `${parseFloat(data.total_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} <?php echo e(__('KRW')); ?>`;
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(6) .data-txt').textContent = data
                .tether_amount;

            // Update timestamps
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(7) .data-txt').textContent = data
                .created_at;
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(8) .data-txt').textContent = data.ended_at;

            // Update bank information
            const bankInfo = data.bank_name && data.account_number ?
                `${data.bank_name}(${data.account_number}) ${data.account_name}` :
                data.account_name;
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(9) .data-txt').textContent = bankInfo;

            // Update payment proof image
            const payProofContainer = document.querySelector('[data-pop-sheet="sell_detail"] .dt-buy');
            const payProofImg = payProofContainer.querySelector('[data-pop-sheet="sell_detail"] .pay-proof-img img');
            if (payProofImg && data.pay_proof) {
                payProofImg.src = "/frontend/proof/" + data.pay_proof;

                payProofImg.parentElement.classList.remove('attach-img');
                payProofImg.style.display = '';
            }

            // Update balance information (assuming these variables are available globally)
            document.querySelector('[data-pop-sheet="sell_detail"] .mb-data1').innerHTML =
                `<?php echo e(number_format($usdtBalance, 2)); ?> <small>USDT</small>`;
            document.querySelector('[data-pop-sheet="sell_detail"] .mb-data2').textContent =
                `pending:<?php echo e(number_format($usdtPending, 2)); ?>`;

            const emailField = document.querySelector('[data-pop-sheet="sell_detail"] #email');
            if (emailField) {
                if (data.order_type == 'sell') {
                    emailField.value = data.client_email;
                } else {
                    emailField.value = data.offer_email;
                }
            }

            const transferButton = document.querySelector(
                '[data-pop-sheet="sell_detail"] .seller-btn-wrap .ps-process-btn');

            if (data.isTransferred) {
                transferButton.disabled = true;
                transferButton.style.opacity = '0.5';
                transferButton.style.cursor = 'not-allowed';
                transferButton.querySelector('.txt').textContent = "<?php echo e(__('테더 전송 완료')); ?>";
            } else {
                transferButton.disabled = false;
                transferButton.style.opacity = '1';
                transferButton.style.cursor = 'pointer';
                transferButton.querySelector('.txt').textContent = "<?php echo e(__('테더 이체')); ?>";
            }

            if (data.state == 'dispute') {
                document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.cancel').style.display = 'none';
                document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.w-200').style.display = 'none';
                document.querySelector('[data-pop-sheet="sell_detail"] #disputeButton').style.display = 'none';
                
            } else if(data.state =='cancel'){ // 거래 취소면 버튼 제거 
                transferButton.style.display = 'none'; 
            }


            if (data.order_type == 'sell' && data.is_buyer == false) {
                document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.cancel').style.display = 'none';
            }
        }

        // 날짜 형식 변환 함수
        function formatDate(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}.${month}.${day} ${hours}:${minutes}`;
        }

        function startPayProofCheck(tradeId) {
            let checkInterval;
            let attempts = 0;
            const maxAttempts = 60 * 10; // 10분

            function checkPayProof() {
                // console.log('checkPayProof', tradeId);
                fetch(`<?php echo e(route('user.mytrade.checkPayProof', '')); ?>/${tradeId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Deposit found
                            clearInterval(checkInterval);

                            const payProofContainer = document.querySelector('[data-pop-sheet="sell_detail"] .dt-buy');
                            const payProofImg = payProofContainer.querySelector(
                                '[data-pop-sheet="sell_detail"] .pay-proof-img img');
                            if (payProofImg && data.pay_proof) {
                                payProofImg.src = "/frontend/proof/" + data.pay_proof;

                                payProofImg.parentElement.classList.remove('attach-img');
                                payProofImg.style.display = '';
                            }
                            document.getElementById('send_tether').disabled = false;
                            alert(`<?php echo e(__('이체증빙자료를 받았습니다.')); ?>`);
                        } else {
                            attempts++;
                            if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                alert(
                                    "<?php echo e(__('No matching pay proof found after 120 seconds. Please try again or contact support.')); ?>"
                                );
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        clearInterval(checkInterval);
                        alert("<?php echo e(__('An error occurred while checking for deposits. Please try again.')); ?>");
                    });
            }

            checkInterval = setInterval(checkPayProof, 1000); // Check every 1 second
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

        //                     // alert(`'${data.sender_realname}'님 으로부터 구매하신 ${formattedAmount} USDT가 입금 되었습니다.\n  거래 완료 버튼을 눌러주세요.`);
        //                     //alert(`<?php echo e(__('Purchased')); ?> ${formattedAmount} <?php echo e(__('USDT has been deposited')); ?>`);
        //                     // Update UI
        //                     document.querySelector('[data-pop-sheet="buy_detail"] .waiting').style.display = 'none';
        //                     document.querySelector('[data-pop-sheet="buy_detail"] #process_step3').style.display = '';
        //                     document.querySelector('[data-pop-sheet="buy_detail"] #transaction_finished').style
        //                         .display = "none";
        //                 } else {
        //                     attempts++;
        //                     if (attempts >= maxAttempts) {
        //                         clearInterval(checkInterval);
        //                         alert(
        //                             "<?php echo e(__('No matching deposit found after 120 seconds. Please try again or contact support.')); ?>"
        //                         );
        //                     }
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error('Error:', error);
        //                 clearInterval(checkInterval);
        //                 alert("<?php echo e(__('An error occurred while checking for deposits.')); ?> " + error);
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
                            document.querySelector('[data-pop-sheet="buy_detail"] .attach-file').style.display = 'none';
                            document.querySelector('[data-pop-sheet="buy_detail"] .waiting').style.display = '';
                            document.querySelector('[data-pop-sheet="buy_detail"] #process_step1').style.display =
                                'none';
                            document.querySelector('[data-pop-sheet="buy_detail"] #process_step2').style.display = '';

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

        function updateBuyDetailPopup(data) {
             console.log('buy');
              // 거래 상세 제목
            const buyTitle = document.querySelector('#buy_title');
            if(data.order_type === 'sell'){
                if(data.client_email === "<?php echo e(auth()->user()->email); ?>" ){
                    buyTitle.textContent = "<?php echo e(__('구매하기-구매자 거래정보')); ?>";
                } else {
                    buyTitle.textContent = "<?php echo e(__('구매하기-판매자 거래정보')); ?>";
                }
            } else {
                if(data.client_email === "<?php echo e(auth()->user()->email); ?>" ){
                    buyTitle.textContent = "<?php echo e(__('판매하기-판매자 거래정보')); ?>";
                } else {
                    buyTitle.textContent = "<?php echo e(__('판매하기-구매자 거래정보')); ?>";
                }
            }
            // Update status
            const statusElement = document.querySelector('[data-pop-sheet="buy_detail"] .data-txt.going');
            if (data.state === "open" || data.state === 'send') {
                statusElement.textContent = "진행중";
                statusElement.className = "data-txt going";
            } else if (data.state === "dispute") {
                statusElement.textContent = "분쟁중";
                statusElement.className = "data-txt going";
            } else if (data.state === "done") {
                statusElement.textContent = "완료";
                statusElement.className = "data-txt completed";
            } else if (data.state === "cancel") {
                statusElement.textContent = "취소";
                statusElement.className = "data-txt cancelled";
            }
            
            // Update seller and buyer names
            if(data.order_type === 'sell' ){
                document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(2) .data-txt').textContent = 
                data.offer_realname == null ? data.offer_username : data.offer_realname ;
                document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(3) .data-txt').textContent =   
                data.client_realname == null ? data.client_username :  data.client_realname ;
            } else {
                document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(2) .data-txt').textContent = 
                data.client_realname == null ? data.client_username :  data.client_realname ;
                document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(3) .data-txt').textContent =   
                data.offer_realname == null ? data.offer_username : data.offer_realname ;
            }
            
            // Update price information
            const priceElement = document.querySelector('[data-pop-sheet="buy_detail"] .data-price');
            const priceChange = parseFloat(data.price_change);
            // 현재 금액과 제안 금액 차이 
            let priceGap = '';
            if(data.price_type == 0){ // 시장가
                priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>`;
            } else { // 고정가
                //priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${priceChange} <?php echo e(__('KRW')); ?></i>`;
                priceGap ='';
            }
            const formattedPrice = parseFloat(data.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            priceElement.innerHTML = `
                ${formattedPrice} <?php echo e(__('KRW')); ?>

                ${priceGap}
            `;
            // <i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>
            document.querySelector('[data-pop-sheet="buy_detail"] .market-price').textContent =
                `<?php echo e(__('시장가')); ?>: ${parseFloat(data.market_price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} <?php echo e(__('KRW')); ?>`;

            // Update purchase amount and tether amount
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(5) .data-txt').textContent =
                `${parseFloat(data.total_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} <?php echo e(__('KRW')); ?>`;
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(6) .data-txt').textContent = data
                .tether_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // Update timestamps
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(7) .data-txt').textContent = data.created_at;
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(8) .data-txt').textContent = data.ended_at;

            // Update bank information
            const bankInfo = data.bank_name && data.account_number && data.account_name ?
                `${data.bank_name}(${data.account_number}) ${data.account_name}` :
                "정보 없음";
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(9) .data-txt').textContent = bankInfo;

            // Update QR code image
            const qrCode = document.querySelector('[data-pop-sheet="buy_detail"] #qr-code');
            if (data.qr_image) {
                const qrCodeImg = document.querySelector('[data-pop-sheet="buy_detail"] .qr-img img');
                qrCodeImg.src = data.qr_image;
                qrCodeImg.alt = "<?php echo e(__('QR 코드 이미지')); ?>";
                qrCode.style.display = 'block';
            } else {
                qrCode.style.display = 'none';
            }

            // Update payment proof
            const attachImgDiv = document.querySelector('[data-pop-sheet="buy_detail"] .attach-img');
            const attachFileDiv = document.querySelector('[data-pop-sheet="buy_detail"] .attach-file');
            if (data.pay_proof) {
                attachImgDiv.innerHTML = `<img src="/frontend/proof/${data.pay_proof}" alt="<?php echo e(__('지급 증빙 이미지')); ?>">`;
                attachImgDiv.style.display = '';
                // attachFileDiv.style.display = 'none';
            } else {
                // attachImgDiv.style.display = 'none';
                // attachFileDiv.style.display = 'block';
            }

            if (data.state === "open") {
                document.querySelector('[data-pop-sheet="buy_detail"] #cancel_transaction').style.display = '';
                document.querySelector('[data-pop-sheet="buy_detail"] #confirm_transaction').style.display = '';
                document.querySelector('[data-pop-sheet="buy_detail"] #transaction_finished').style.display = 'none';
            } else if (data.state === "send") {
                attachFileDiv.style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #cancel_transaction').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #confirm_transaction').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #transaction_finished').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #process_step1').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #process_step2').style.display = '';
                document.querySelector('.waiting').style.display = '';

                //startDepositCheck();
            } else if (data.state === "dispute") {
                document.querySelector('[data-pop-sheet="buy_detail"] #transaction_finished').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #cancel_transaction').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] #confirm_transaction').style.display = 'none';
                // document.querySelector('[data-pop-sheet="buy_detail"] #transaction_finished').style.display = 'none';
                // document.querySelector('[data-pop-sheet="buy_detail"] #process_step1').style.display = 'none';
                document.querySelector('[data-pop-sheet="buy_detail"] .attach-file').style.display = 'none';
                // 분쟁시 채팅창 살리기
                document.querySelector('#process_step1').style.display = 'block';
                // 창 크기 변환 
                $('.ps-process-btn.cancel.w-200').css('width', '100%');
            } else if(data.state === "cancel"){ // 거래 취소시 버튼 제거 
                document.querySelector('[data-pop-sheet="buy_detail"] #process_step1').style.display = 'none';
            }

            // if (data.is_buyer == false) {
            //     document.querySelector('[data-pop-sheet="buy_detail"] #cancel_transaction').style.display = 'none';
            // }
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
                        // alert("<?php echo e(__('An error occurred while processing your request.')); ?>");
                    });

                document.body.removeChild(form);
            }
        }

        function sendTether() {

            if (!confirm("<?php echo e(__('테더를 전송하시겠습니까?')); ?>")) {
                return;
            }
            
            const sendButton = document.getElementById('send_tether');
            sendButton.disabled = true;
            sendButton.innerText = `<?php echo e(__('전송중')); ?>`;
            sendButton.style.color = 'white';

            const emailField = document.querySelector('[data-pop-sheet="sell_detail"] #email');
            const amount = document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(6) .data-txt').textContent;

            // 데이터 준비
            const data = {
                amounts: parseFloat(amount),
                email: emailField.value,
                itemId: itemId,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            const apiEndpoint = "<?php echo e(setRoute('user.wallet.transferUSDTInternally')); ?>";
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
                        alert('<?php echo e(__('USDT가 성공적으로 전송되었습니다.')); ?>');
                        window.location.href = '<?php echo e(setRoute('user.wallet.index')); ?>'
                    } else {
                        throw new Error(data.message || `<?php echo e(__('전송에 실패했습니다.')); ?>`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`<?php echo e(__('전송 중 오류가 발생했습니다: ')); ?>` + error.message);
                    sendButton.disabled = true;
                    sendButton.innerText = `<?php echo e(__('테더 이체')); ?>`;
                    sendButton.style.color = 'white';
                });
        }

        function openChat(type) {
            $('.chat-layer').show();
            window.startChat(orderId, window.receiverId);
        }

        function closeChat() {
            $('.chat-layer').hide();
        }

        function confirmDispute() {
            const message = "<?php echo e(__('\"분쟁 요청\" 시 거래가 해결될 때까지 한시적으로 정지 됩니다. 고객센터에 접수해 주세요.')); ?>";
            if (confirm(message)) {
                // itemId
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
 
        function saveQRCode() {
            const qrImage = document.querySelector('[data-pop-sheet="buy_detail"] .qr-img img');
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
                    alert('<?php echo e(__('QR 코드 저장 중 오류가 발생했습니다.')); ?>');
                });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/dashboard.blade.php ENDPATH**/ ?>