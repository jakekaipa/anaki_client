<?php
    $default = get_default_language_code();
?>


<?php $__env->startSection('breadcrumb'); ?>
    <?php echo $__env->make('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Dashboard'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<style>
      #qrCodeButton {
            background-color: #21b8a1 !important;  /* 배경색 */
            width: 80px !important;  /* 너비 */
            height: 31px !important;  /* 높이 */
            color: white !important;  /* 글자색 하얀색 */
            font-size: 14px !important;  /* 글자 크기 */
            text-align: center !important;  /* 텍스트 가운데 정렬 */
            line-height: 31px !important;  /* 텍스트 세로 정렬 */
            border: none !important;  /* 테두리 없애기 */
            border-radius: 5px !important;  /* 모서리 둥글게 */
            cursor: pointer !important;  /*마우스 올리면 손 모양 */
            margin-left: auto !important;
        }

        .flex-container {
            display: flex;  /* flex 레이아웃을 사용하여 자식 요소들을 가로로 배치 */
            align-items: center;  /* 세로 중앙 정렬 */
        }
</style>

<?php $__env->startSection('content'); ?>
    <div class="content wallet">
        <h2 class="page-tit"><?php echo e(__('내 지갑')); ?></h2>

        <div class="container-row card-box-wrap mt-0">
            <div class="card-box wallet-box">
                <strong class="cb-tit"><?php echo e(__('자산 (USDT)')); ?></strong>
                <p class="wallet-property tether">
                    <strong>$&nbsp<?php echo e(number_format($usdtBalance, 2)); ?> </strong>
                </p>
                <p class="wallet-property-sub"><?php echo e(__('Pending')); ?> : $&nbsp<?php echo e(number_format($usdtPending, 2)); ?></p>

                <div class="wb-button">
                    <button class="btn-wb" onclick="openPopSheet('sheet-send_usdt')"><span
                            class="send txt"><?php echo e(__('보내기')); ?></span></button>
                    <button class="btn-wb" onclick="openPopSheet('sheet-receive_usdt')"><span
                            class="recieve txt"><?php echo e(__('받기')); ?></span></button>
                </div>
            </div>
        </div>

        <div class="container-row card-box-wrap">
            <div class="card-box">
                <strong class="cb-tit"><?php echo e(__('최근 트랜잭션')); ?></strong>

                <div class="table-wrap">
                    <table class="wallet-tbl">
                        <caption><?php echo e(__('최근 트랜잭션 게시판')); ?></caption>
                        <colgroup>
                            <col class="wt-col1">
                            <col class="wt-col2">
                            <col class="wt-col3">
                        </colgroup>

                        <thead>
                            <tr>
                                <th><?php echo e(__('트랜잭션')); ?></th>
                                <th><?php echo e(__('세부 정보')); ?></th>
                                <th><?php echo e(__('금액')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $listData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php if($item->type === __('출금')): ?>
                                            <div class="result r-complete">
                                                <?php if(!empty($item->txid)): ?>
                                                    <p class="rc-txt1"><?php echo e(__('송금 완료')); ?></p>
                                                    <p class="rc-txt2"> <?php
                                                    $creationDateTime = new DateTime($item->created_at);
                                                    $creationDateTime->setTimezone($curTimeZone);
                                                    echo $creationDateTime->format('Y-m-d H:i') . ' ';
                                                    ?></p>
                                                <?php else: ?>
                                                    <p class="rc-txt1"><?php echo e(__('전송중')); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif($item->type === __('입금')): ?>
                                            <div class="result r-received">
                                                <p class="rc-txt2"> <?php
                                                $creationDateTime = new DateTime($item->created_at);
                                                $creationDateTime->setTimezone($curTimeZone);
                                                echo $creationDateTime->format('Y-m-d H:i') . ' ';
                                                ?></p>
                                                <p class="rc-txt1"><?php echo e(__('입금 완료')); ?></p>
                                            </div>
                                        <?php endif; ?>

                                    </td>
                                    <td>
                                        <p class="detail">
                                            <?php if(!empty($item->txid)): ?>
                                                <button type="button" class="name"
                                                    onclick="onViewDetail('<?php echo e($item->txid); ?>')">
                                                    <?php echo e($item->txid); ?>

                                                </button>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="value">
                                            <span class="val1"><?php echo e(number_format($item->amount, 2)); ?> USDT</span>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center"><span
                                            class="text-danger"><?php echo e(__('No Records Found')); ?></span></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- pop-sheet 보내기 팝업 -->
    <div class="pop-sheet" data-pop-sheet="sheet-send_usdt">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit"><?php echo e(__('Tether 보내기')); ?></h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="ws2">
                        <div class="amw-qr">
                            <p class="txt"><?php echo e(__('ANAKI는 TRON 네트워크(TRC-20)만을 사용합니다.')); ?></p>
                        </div>

                        <div class="send-row">
                            <span class="sr-txt"><?php echo e(__('테더(TRC20)보내기')); ?> </span>
                            
                            <div class="sr-btn">
                                <button class="sr-btn2"><?php echo e(__('외부지갑')); ?></button>
                                <button class="sr-btn1"><?php echo e(__('ANAKI USER')); ?></button>
                            </div>
                        </div>

                        <div class="i-txt__wrap">
                            <div class="i-txt__box">
                                <input type="text" id="recipientAddress" name="walletAddr" class="i-txt__input"
                                    title="<?php echo e(__('받을 사람의 지갑 주소를 입력하세요')); ?>" placeholder="<?php echo e(__('받을 사람의 지갑 주소를 입력하세요')); ?>">
                                <button type="button" class="i-txt__del" title="<?php echo e(__('입력내용 삭제')); ?>"><img
                                        src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button>
                            </div>
                        </div>
                        
                        <div class="flex-container">
                            <p class="chk-addr">TRC-20 address</p>
                            <button id="qrCodeButton" onclick='qrCodeScan()'><?php echo e(__('QrCode Scan')); ?></button>
                        </div>

                        <div class="amout-box">
                            <div class="ab-col">
                                
                                <div class="abc1">Amount to send</div>
                                <div class="abc2">
                                    <input type="text" class="itxt" placeholder="0.000000" id="sendAmount" >
                                    <span class="dp">~0 KRW</span>
                                </div>
                                <br>
                                
                                <div id="networkFeeInfo">
                                    <div class="abc1">Network Fee</div>
                                    <div class="abc2">
                                        <input type="text" class="itxt" placeholder="0" value=0 id="networkFee">
                                        <!-- <span class="dp">~0 KRW</span> -->
                                    </div>
                                </div>

                                <div class="abc3">
                                    <button class="abc3-btn1" id="min_amount">Min</button>
                                    <button class="abc3-btn2" id="max_amount">Max</button>
                                </div>
                            </div>
                            <div class="ab-col">
                                <div class="kw1">Available 0</div>
                                <div class="kw2">USDT</div>
                                <div class="kw2" id='networkFeeInfoUnit' style='margin-top: 98%'>USDT</div>            
                                <!-- <div class="kw3">
                                    <button class="abc3-btn2">KRW</button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div id="twoFactorInputWrapper" style="display: none;">
                    <div class="sa-wrap">
                        <div class="sa">
                            <p class="sa-txt1">Google 2FA Code</p>

                            <div class="sa-inp-row">
                                <input type="text" id="otpInput1" class="sa-inp otp" name="code[]" maxlength="1"
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
                                    oninput="digitValidate(this)" onkeyup="tabChange(6)">
                            </div>

                            <p class="sa-txt2"><?php echo e(__('Enter the 6-digit code from Google Authenticator.')); ?></p>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn">
                        <span class="txt"><?php echo e(__('보내기')); ?></span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('sheet-send_usdt')"><img
                        src="<?php echo e(asset('/public/pub')); ?>/img/close-popup@2x.png" alt="팝업시트 닫기"></button>
            </div>
        </div>
    </div>

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="sheet-receive_usdt">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit"><?php echo e(__('Tether 받기')); ?></h2>
                </div>

                <div class="ps-content custom-scroll trans">
                    <div class="wr2">
                        <div class="amw-qr">
                            <p class="txt"><?php echo e(__('ANAKI는 TRON 네트워크(TRC-20)만을 사용합니다.')); ?></p>
                            <div class="qr-img"><img src="<?php echo e(asset('/public/pub')); ?>/img/auth-qr@2x.png"></div>
                        </div>
                        <div class="i-txt__wrap">
                            <label for="wallet1" class="i-txt__label"><?php echo e(__('내 TRC-20 주소')); ?></label>
                            <div class="i-txt__box">
                                <input type="text" id="wallet1" class="i-txt__input addr-wallet" readonly
                                    title="내 지갑 주소가 입력 되어 있습니다." value="<?php echo e($user->walletAddress); ?>">
                                <button type="button" class="i-txt__copy btn-addr-wallet" title="입력내용 복사"><img
                                        src="<?php echo e(asset('/public/pub')); ?>/img/copy@2x.png"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn cancel">
                        <span class="txt"><?php echo e(__('주소공유')); ?></span>
                    </button>
                    <button class="ps-process-btn">
                        <span class="txt"><?php echo e(__('클립보드에 복사')); ?></span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('sheet-receive_usdt')"><img
                        src="<?php echo e(asset('/public/pub')); ?>/img/close-popup@2x.png" alt="팝업시트 닫기"></button>
            </div>
        </div>
    </div>

    <!-- pop-sheet -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <style>
        .form--control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-copy {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            height: 100%;
        }

        .input-group-append {
            display: flex;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .input-group-append {
            display: flex;
        }

        .btn-copy {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-copy:hover {
            background-color: #0056b3;
        }

    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        var krwRate = <?php echo e($prices['KRW']); ?>;

        const TRON_NETWORK = "<?php echo e(config('services.tron.network')); ?>";
    
        function getTronScanUrl() {
            return TRON_NETWORK === 'shasta' 
                ? "https://shasta.tronscan.org" 
                : "https://tronscan.org";
        }

        function onViewDetail(hash) {
            // location.href = "https://etherscan.io/tx/" + hash;
            window.open(`${getTronScanUrl()}/#/transaction/${hash}`, "_blank");
        }

        function onViewDetailAddress(address) {
            // location.href = "https://etherscan.io/tx/" + hash;
            window.open(`${getTronScanUrl()}/#/address/${address}/transfers`, "_blank");
        }

        function openTronScan() {
            var walletAddress = "<?php echo e($user->walletAddress); ?>";
            window.open(`${getTronScanUrl()}/#/address/${walletAddress}/transfers`, "_blank");
        }

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
                }
            } else {
                if (val > 1) {
                    ele[val - 2].focus();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // QR 코드 생성 함수
            function generateQRCode(elementId, text) {
                const qrContainer = document.getElementById(elementId);
                qrContainer.innerHTML = ''; // 기존 내용을 지웁니다

                new QRCode(qrContainer, {
                    text: text,
                    width: 128,
                    height: 128,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }

            // 지갑 주소를 가져와서 QR 코드 생성
            const walletAddressInput = document.getElementById('wallet1');
            const walletAddress = walletAddressInput.value;

            if (walletAddress) {
                // qr-img div의 내용을 비우고 새로운 id를 부여합니다
                const qrImgDiv = document.querySelector('[data-pop-sheet="sheet-receive_usdt"] .qr-img');
                qrImgDiv.innerHTML = '<div id="qrcode"></div>';

                // QR 코드 생성
                generateQRCode('qrcode', walletAddress);
            } else {
                console.error('Wallet address not found');
            }

            function copyToClipboard(text, successMessage = `<?php echo e(__('텍스트가 클립보드에 복사되었습니다!')); ?>`) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(function() {
                        alert(successMessage);
                    }).catch(function(err) {
                        console.error('텍스트 복사 실패:', err);
                        alert(`<?php echo e(__('복사에 실패했습니다. 수동으로 복사해 주세요.')); ?>`);
                    });
                } else {
                    var tempInput = document.createElement('textarea');
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    try {
                        document.execCommand('copy');
                        alert(successMessage);
                    } catch (err) {
                        console.error('텍스트 복사 실패:', err);
                        alert(`<?php echo e(__('복사에 실패했습니다. 수동으로 복사해 주세요.')); ?>`);
                    }
                    document.body.removeChild(tempInput);
                }
            }

            // '지갑 주소 복사' 버튼 기능
            $('.btn-addr-wallet').on('click', function() {
                var walletAddress = this.parentElement.querySelector('.addr-wallet').value;
                copyToClipboard(walletAddress, `<?php echo e(__('지갑 주소가 클립보드에 복사되었습니다!')); ?>`);
            });

            // '클립보드에 복사' 버튼 기능
            const copyToClipboardBtn = document.querySelector(
                '[data-pop-sheet="sheet-receive_usdt"] .ps-process-btn:not(.cancel)');
            copyToClipboardBtn.addEventListener('click', function() {
                const walletAddress = document.querySelector(
                    '[data-pop-sheet="sheet-receive_usdt"] #wallet1').value;
                copyToClipboard(walletAddress, `<?php echo e(__('지갑 주소가 클립보드에 복사되었습니다!')); ?>`);
            });

            // '주소공유' 버튼 기능 추가
            const shareAddressBtn = document.querySelector(
                '[data-pop-sheet="sheet-receive_usdt"] .ps-process-btn.cancel');

            shareAddressBtn.addEventListener('click', function() {
                const walletAddress = document.querySelector(
                    '[data-pop-sheet="sheet-receive_usdt"] #wallet1').value;

                if (navigator.share) {
                    navigator.share({
                        title: 'My Wallet Address',
                        text: 'Here is my wallet address: ' + walletAddress,
                        url: window.location.href
                    }).then(() => {
                        console.log('주소가 성공적으로 공유되었습니다.');
                    }).catch((error) => {
                        console.error('공유 중 오류 발생:', error);
                        fallbackShare(walletAddress);
                    });
                } else {
                    fallbackShare(walletAddress);
                }
            });

            function fallbackShare(address) {
                // Web Share API가 지원되지 않는 경우의 대체 동작
                const tempInput = document.createElement('textarea');
                tempInput.value = address;
                document.body.appendChild(tempInput);
                tempInput.select();

                try {
                    document.execCommand('copy');
                    alert(`<?php echo e(__('지갑 주소가 클립보드에 복사되었습니다. 원하는 앱에 붙여넣기 하세요.')); ?>`);
                } catch (err) {
                    console.error('주소 복사 실패:', err);
                    alert(`<?php echo e(__('주소 복사에 실패했습니다. 수동으로 복사해 주세요.')); ?>`);
                }

                document.body.removeChild(tempInput);
            }

            const sendAmountInput = document.getElementById('sendAmount');
            const krwDisplay = document.querySelector('.dp');
            const krwRate = <?php echo e($prices['KRW']); ?>; // PHP에서 JavaScript로 환율 전달
            const networkFee = document.getElementById('networkFee'); // 네트워크 피 금액

            function updateKRWAmount() {
                const usdtAmount = parseFloat(sendAmountInput.value) || 0;
                const krwAmount = usdtAmount * krwRate;
                krwDisplay.textContent = `~${krwAmount.toLocaleString('ko-KR', { maximumFractionDigits: 0 })} KRW`;
            }

            function updateNetWorkFee(){
                const usdtAmount = parseFloat(sendAmountInput.value) || 0;
                let fee = 0;
                
                if(usdtAmount == 0 ){
                    fee = 0;
                }
                else if(usdtAmount > 0 && usdtAmount < 20  ){
                    fee = 6;
                } else if(usdtAmount >= 20 ){
                    fee = 6; 
                }

                networkFee.value = fee;
            }  

            sendAmountInput.addEventListener('input', function(e) {
                const value = e.target.value;

                // 숫자와 소수점만 허용하는 정규식
                const regex = /^[0-9]*\.?[0-9]*$/;

                if (regex.test(value)) {
                    // 유효한 입력
                    sendAmountInput.classList.remove('is-error');
                } else {
                    // 유효하지 않은 입력
                    sendAmountInput.classList.add('is-error');
                }

                // 입력값이 비어있으면 에러 메시지를 숨깁니다
                if (value === '') {
                    sendAmountInput.classList.remove('is-error');
                }

                // 소수점 이하 최대 6자리까지 허용
                const regexCount = /^\d*\.?\d{0,6}$/;
                if (!regexCount.test(value)) {
                    // 유효하지 않은 값이 입력되면 마지막 문자를 제거
                    event.target.value = value.slice(0, -1);
                }

                updateKRWAmount();
                updateNetWorkFee();
            });

            const minButton = document.querySelector('#min_amount');
            const maxButton = document.querySelector('#max_amount');
            const usdtBalance = "<?php echo e($usdtBalance); ?>"; // Get the USDT balance from PHP

            console.log(minButton);
            console.log(maxButton);
            // Min button click event
            minButton.addEventListener('click', function() {
                console.log("click");

                sendAmountInput.value = '0';
                updateKRWAmount();
                updateNetWorkFee();
            });

            // Max button click event
            maxButton.addEventListener('click', function() {
                console.log("click");

                sendAmountInput.value = usdtBalance;
                updateKRWAmount();
                updateNetWorkFee();
            });

            const externalWalletBtn     = document.querySelector('.sr-btn2');
            const anakiUserBtn          = document.querySelector('.sr-btn1');
            const recipientAddressInput = document.getElementById('recipientAddress');
            const sendButton            = document.querySelector('[data-pop-sheet="sheet-send_usdt"] .ps-process-btn');
            let isExternalWallet        = true; // 기본값은 외부 지갑
            const networkFeeInfo        = document.querySelector('#networkFeeInfo'); // 네트워크 피 항목
            const networkFeeInfoUnit    = document.querySelector('#networkFeeInfoUnit'); // 네트워크 피 단위 
            const qrCodeButton          = document.querySelector('#qrCodeButton'); // 큐알코드 버튼 

            function updateUIForTransferType(isExternal) {
                if (isExternal) { // 외부 
                    externalWalletBtn.classList.add('active');
                    anakiUserBtn.classList.remove('active');
                    recipientAddressInput.placeholder = "<?php echo e(__('받을 사람의 지갑 주소를 입력하세요')); ?>";
                    document.querySelector('.chk-addr').textContent = "TRC-20 address";
                    networkFeeInfo.style.display = 'block';
                    networkFeeInfoUnit.style.display = 'block';
                    if(isMobileDevice()){ // 모바일은 큐알코드 스캔 버튼
                        qrCodeButton.style.display = 'block'; 
                    }

                } else { // 내부 
                    anakiUserBtn.classList.add('active');
                    externalWalletBtn.classList.remove('active');
                    recipientAddressInput.placeholder = "<?php echo e(__('받을 사람의 이메일 주소를 입력하세요')); ?>";
                    document.querySelector('.chk-addr').textContent = "ANAKI USER email";
                    networkFeeInfo.style.display = 'none';
                    networkFeeInfoUnit.style.display = 'none';
                    qrCodeButton.style.display = 'none';
                }
                isExternalWallet = isExternal;
            }

            externalWalletBtn.addEventListener('click', () => updateUIForTransferType(true));
            anakiUserBtn.addEventListener('click', () => updateUIForTransferType(false));

            // 초기 UI 설정
            updateUIForTransferType(false);

            const twoFactorEnabled = <?php echo e(auth()->user()->two_factor_tether_transfer ? 'true' : 'false'); ?>;
            const twoFactorInputWrapper = document.getElementById('twoFactorInputWrapper');
            
            sendButton.addEventListener('click', function() {
                const recipientAddress = recipientAddressInput.value;
                let amount = document.getElementById('sendAmount').value;
                
                // 외부거래 수수료 피 차감 소수점 이하 6자리
                if(isExternalWallet){   
                    amount = Math.round( (amount - networkFee.value) *1000000 ) / 1000000; 
                }
                // console.log(amount);
              
                // 입력값 검증
                if (isNaN(amount) || parseFloat(amount) <= 0) {
                    alert(`<?php echo e(__('유효한 금액을 입력해주세요.')); ?>`);
                    return;
                }
                
                if (!recipientAddress || !amount) {
                    alert(`<?php echo e(__('받는 사람의 주소/이메일과 금액을 모두 입력해주세요.')); ?>`);
                    return;
                }

                // 외부거래 
                if (isExternalWallet) {                 
                    function isTronAddress(address) {
                        // Tron addresses are 34 characters long and start with T
                        if (!/^T[1-9A-HJ-NP-Za-km-z]{33}$/.test(address)) {
                            return false; 
                        }
                        return true;
                    }

                    if (!isTronAddress(recipientAddress)) {
                        alert(`<?php echo e(__('주소가 정확한지 확인을 하신 후 다시 보내기를 하세요')); ?>`);
                        return;
                    }
                    
                } else {
                    function isValidEmail(email) {
                        // 간단한 이메일 정규식 패턴
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return emailPattern.test(email);
                    }

                    if (!isValidEmail(recipientAddress)) {
                        alert(`<?php echo e(__('주소가 정확한지 확인을 하신 후 다시 보내기를 하세요')); ?>`);
                        return;
                    }
                }

                if (twoFactorEnabled) {
                    twoFactorInputWrapper.style.display = 'block';
                    const firstOtpInput = document.getElementById('otpInput1');
                    firstOtpInput.focus();
                    return;
                }

                // 사용자 확인
                const confirmMessage = isExternalWallet ?
                    `${amount} USDT ${recipientAddress} <?php echo e(__('주소로 보내시겠습니까?')); ?>` :
                    `${amount} USDT ${recipientAddress} <?php echo e(__('이메일 주소로 보내시겠습니까?')); ?>`;

                if (!confirm(confirmMessage)) {
                    return;
                }

                sendButton.disabled = true;
                sendButton.innerText = `<?php echo e(__('전송중')); ?>`;
                sendButton.style.color = 'white';

                sendUSDT();
            });

            // 보내기 버튼 클릭시
            function sendUSDT() {
                const amount = document.getElementById('sendAmount').value;
                const recipientAddress = document.getElementById('recipientAddress').value;
                const networkFees = (isExternalWallet)?  networkFee.value : 0;
                // console.log(amount+"////"+recipientAddress+"////"+networkFees);
                const data = {
                    amounts: amount,
                    networkFee: networkFees,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                if (twoFactorEnabled) {
                    const twoFactorCode = Array.from(document.querySelectorAll('.otp')).map(input => input.value)
                        .join('');
                    if (twoFactorCode.length !== 6) {
                        alert(`<?php echo e(__('올바른 2FA 코드를 입력해주세요.')); ?>`);
                        return;
                    }
                    data.twoFactorCode = twoFactorCode;
                    // console.log(data);
                    // return;
                }

                if (isExternalWallet) {
                    data.walletAddr = recipientAddress;
                } else {
                    data.email = recipientAddress;
                }
                
                // API 엔드포인트 선택
                const apiEndpoint = isExternalWallet ?
                    "<?php echo e(setRoute('user.wallet.sendUSDT')); ?>" : 
                    "<?php echo e(setRoute('user.wallet.transferUSDTInternallyWalletToWallet')); ?>";

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
                            alert(`<?php echo e(__('USDT가 성공적으로 전송되었습니다.')); ?>`);
                            closePopSheet('sheet-send_usdt');
                            location.reload();
                        } else {
                            throw new Error(data.message || `<?php echo e(__('전송에 실패했습니다.')); ?>`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(`<?php echo e(__('전송 중 오류가 발생했습니다: ')); ?>` + error.message);
                        sendButton.disabled = false;
                        sendButton.innerText = `<?php echo e(__('보내기')); ?>`;
                        sendButton.style.color = 'white';
                    });
            }

            // 2FA 입력 완료 시 자동으로 전송
            const otpInputs = document.querySelectorAll('.otp');
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (index === 5 && this.value) {
                        sendUSDT();
                    }
                });
            });
            
        })

        // 모바일 체크 
        function isMobileDevice() {
            return /Mobi|Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);
        }

        // qr코드 스캔
        function qrCodeScan(){
            if(isMobileDevice()){
                const newWindow = window.open('/user/wallet/qrCode-reader', '_blank','width=100%,height=100%');
                // 부모 창에서 QR 코드 스캔 결과 받는 함수
                window.setScanResult = function(result) {
                    document.getElementById("recipientAddress").value = result;
                };
            } else {
                alert("<?php echo e(__('QrScanCheck')); ?>");
            }
        }           
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/wallet/index.blade.php ENDPATH**/ ?>