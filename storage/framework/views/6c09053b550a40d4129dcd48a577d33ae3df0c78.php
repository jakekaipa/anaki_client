<?php
    $default = get_default_language_code();
?>



<style>
.flex-container {
    display: flex;
    justify-content: flex-start; /* 섹션들을 왼쪽 정렬로 배치 */
    gap: 10px; /* 두 섹션 간의 간격을 설정 */
    background-color:#ffffff;
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

.changeUnit {
            padding: 10px 15px;
            font-size: 16px;
            color: #333;
            background-color: #ffffff;
            border: 2px solid #ffffff;
            border-radius: 8px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            outline: none;
            transition: border-color 0.3s ease;
        }

.btn-submit {
        background-color:#13c59b; /* 버튼 배경색 */
        color: white; /* 글자 색 */
        padding: 10px 20px; /* 버튼 내 여백 */
        border: none; /* 테두리 제거 */
        border-radius: 5px; /* 둥근 모서리 */
        font-size: 16px; /* 글자 크기 */
        cursor: pointer; /* 마우스 포인터 변경 */
        text-align: center; /* 텍스트 가운데 정렬 */
        width: 100%; /* 너비를 부모 요소에 맞춤 */
        transition: background-color 0.3s; /* 배경색 전환 효과 */
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
<?php $__env->startSection('content'); ?>
<div id="Phase2" class="content offer offer2">
    <h2 class="page-tit" id="phase2Title"></h2>
    <form id='submitForm'>
    <?php echo csrf_field(); ?>
    <input type='hidden' name='tradeOfferId' value="<?php echo e($tradeOfferId); ?>">
    <input type='hidden' name='orderType' id='orderTypes' value="<?php echo e($tradeInfo->order_type); ?>">
    <div class="container-row">
        <div class="flex-container">
            <!-- Left Section (Price Selection) -->
            <div class="card-box border">
                <h3 class="step-tit"><?php echo e(__('Select the price you want to use')); ?></h3>
                <div>
                    <div class="select-price">
                        <button type="button" class="sp-col btn-radio active">
                            <div class="radio">
                                <input type="radio" class="radio-inp" id="priceType1" name="priceType"
                                    value="0" <?php echo e(($tradeInfo->priceType == 0)? "checked":""); ?>  >
                                <label for="priceType1"><?php echo e(__('Market Price')); ?></label>
                            </div>
                            <p class="sptxt">
                                <?php echo e(__('The selling price of the transaction will change according to fluctuations in the Tether market price.')); ?>

                            </p>
                        </button>
                        <button type="button" class="sp-col btn-radio">
                            <div class="radio">
                                <input type="radio" class="radio-inp" id="priceType2" name="priceType"
                                    value="1" <?php echo e(($tradeInfo->priceType == 1)? "checked":""); ?> >
                                <label for="priceType2"><?php echo e(__('Fixed Price')); ?></label>
                            </div>
                            <p class="sptxt">
                                <?php echo e(__('The selling price of the transaction is fixed and remains constant regardless of market price fluctuations.')); ?>

                            </p>
                        </button>
                    </div>

                    <div class="dt-row">
                        <!-- 최소 금액 입력란 -->
                        <div class="dt-col">
                            <label for="tradeVolMin" class="dt-label"><?php echo e(__('Set Transaction Amount')); ?></label>
                            <label class="dt-label"style="margin-left: 10px; color: rgb(83,126,238)"><?php echo e(__('Min. 15,000원 ~ Max. 제한없음')); ?></label>
                            <div class="dt-inp">
                                <input type="text" id="tradeVolMin" class="input" placeholder="<?php echo e(__('Minimum Amount')); ?>" name="tradeVolMin"
                                    value='<?php echo e(number_format($tradeInfo->tradeVolMin)); ?>'>
                                <span class="unit"><?php echo e(__('KRW')); ?></span>
                            </div>
                        </div>

                        <!-- 대시 (구분선) -->
                        <span class="dash" style="font-size: 20px;">-</span>

                        <!-- 최대 금액 입력란 -->
                        <div class="dt-col">
                            <div class="dt-inp">
                                <input type="text" id="tradeVolMax" class="input" placeholder="<?php echo e(__('Maximum Amount')); ?>" name="tradeVolMax"
                                    value='<?php echo e(number_format($tradeInfo->tradeVolMax)); ?>'>
                                <span class="unit"><?php echo e(__('KRW')); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- 시장가격 --> 
                    <div class="section" id="MarketPriceGroup">
                        <h4 class="section-tit"><?php echo e(__('Transaction Margin')); ?></h4>

                        <div class="setting-inp money">
                            <input type="text" class="si-inp inp-m" name="offerMargin" value="<?php echo e(($tradeInfo->priceType == 0)? $tradeInfo->offerMargin : 5); ?>"
                                id="offerMargin" readonly>
                            <span class="si-display">
                                <select id='changeUnitPoint' class='changeUnit' onchange="changeUnitPoints()">
                                    <option value = 0.1 >0.1</option>
                                    <option value = 1 >1</option>
                                </select>
                            </span>
                            <button type="button" class="btn-set decrease" id="margin-decrease">-</button>
                            <button type="button" class="btn-set increase" id="margin-increase">+</button>
                        </div>
                        <p class="setting-txt"><?php echo e(__('Current Tether Market Price:')); ?>

                            <strong><?php echo e(number_format($prices['KRW'], 2)); ?> <?php echo e(__('KRW')); ?></strong> <!--  <?php echo e(number_format($prices['KRW'], 2)); ?> <?php echo e(__('KRW')); ?> -->
                        </p>
                    </div>

                    <!-- 고정가격 -->
                    <div class="section" id="FixedPriceGroup">
                        <h4 class="section-tit"><?php echo e(__('Fixed price')); ?></h4>

                        <div class="setting-inp money">
                            <input type="number" class="si-inp inp-m" value="<?php echo e(($tradeInfo->priceType == 1)? intval($tradeInfo->fixedPrice) : $prices['KRW']); ?>" 
                            name="fixedPrice" id="fixedPrice" readonly>
                            <span class="si-display">
                                <select id='changeUnits' class='changeUnit' onchange="changeUnit()">
                                    <option value = 1 >1</option>
                                    <option value = 10 >10</option>
                                    <option value = 100 >100</option>
                                </select>
                            </span>
                            <button type="button" class="btn-set decrease" id="price-decrease">-</button>
                            <button type="button" class="btn-set increase" id="price-increase">+</button>
                        </div>
                        <p class="setting-txt"><?php echo e(__('Current Tether Market Price:')); ?>

                            <strong> <?php echo e(number_format($prices['KRW']), 2); ?> <?php echo e(__('KRW')); ?></strong>
                        </p>
                    </div>

                    <div class="section">
                        <h4 class="section-tit"><?php echo e(__('Transaction Time Limit')); ?></h4>

                        <div class="setting-inp time">
                            <input type="text" class="si-inp inp-s" value="<?php echo e(($tradeInfo->offerTimeLimit)? $tradeInfo->offerTimeLimit : 20); ?>" name="offerTimeLimit"
                                id="offerTimeLimit">
                            <span class="si-display"><?php echo e(__('minutes')); ?></span>
                            <button type="button" class="btn-set decrease">-</button>
                            <button type="button" class="btn-set increase">+</button>
                        </div>

                        <p class="setting-txt">
                            <?php echo e(__('The transaction will be automatically cancelled after the set time for possible transactions has passed. (Minimum input is 20 minutes)')); ?>

                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Section (Other Options) -->
            <div class="offer3-inner">
                <div class="section">
                    <div class="checkbox aic">
                        <input type="checkbox" name="secretTrade" id="secretTrade" class="checkbox__chk"
                           <?php echo e(($tradeInfo->password != null)? 'checked':''); ?>>
                        <label for="secretTrade"
                            class="checkbox__label section-tit"><?php echo e(__('Private Trade')); ?></label>
                        <label class="" style="margin-left: 10px;"><?php echo e(__('비밀번호 잠금기능')); ?></label>
                    </div>
                    <div class="input" name="passwordGroup" id="passwordGroup">
                        <div class="i-txt password w-full">
                            <div class="i-txt__wrap">
                                <input type="password" class="i-txt__input" placeholder="<?php echo e(__('Pin Number')); ?>"
                                    name="password" id="password" maxlength="4" pattern="\d{4}"
                                    inputmode="numeric" onkeypress="return onlyNumberKey(event)"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                    value='<?php echo e($tradeInfo->password); ?>'
                                    >
                                <!-- 
                                <button type="button" class="i-txt__btn-look" title="<?php echo e(__('비밀번호 보기 실행')); ?>">
                                    <span class="txt blind"><?php echo e(__('비밀번호 보기')); ?></span>
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h4 class="section-tit"><?php echo e(__('Transaction Limit')); ?></h4>
                    <div class="checkbox aic">
                        <input type="checkbox" name="checkboxKYCAuth" id="checkboxKYCAuth"
                            class="checkbox__chk" <?php echo e(($tradeInfo->needKYCAuth == 1)? 'checked':''); ?>>
                        <label for="checkboxKYCAuth"
                            class="checkbox__label"><?php echo e(__('Only partners who have completed KYC verification can trade')); ?></label>
                    </div>
                    <div>  <span class="checkbox__note"><?php echo e(__('To prevent illegal funds and third-party fraud, we recommend trading with partners who have completed KYC certification')); ?></span></div>
                </div>

                <div class="section">
                    <h4 class="section-tit required"><?php echo e(__('Transaction Title')); ?></h4>
                    <div class="input">
                        <input type="text" size="10" name="offerLabel" id="offerLabel" value='<?php echo e($tradeInfo->offerLabel); ?>'
                            class="inp-txt w-full" placeholder="<?php echo e(__('한글 10자 이내 입력')); ?>">
                    </div>
                </div>

                <div class="section">
                    <label for="dealtitle"
                        class="checkbox__label required section-tit"><?php echo e(__('Transaction Conditions')); ?></label>
                    <div class="textarea">
                        <textarea name="offerCondition" id="offerCondition" class="w-full" style='height:25%'><?php echo e($tradeInfo->offerCondition); ?></textarea>
                    </div>
                </div>

                <div class="section">
                    <button type="button" onclick="copyLink('<?php echo e($tradeOfferId); ?>')" class="btn-submit w-full"><?php echo e(__('링크주소 복사')); ?></button>
                </div>      

            </div>
        </div>
    </div>
    </form>
    <div class="pc-btn">
        <button type="button" class="process-btn" id="nextBtn" onclick="onSubmit()">
            <span class="txt"><?php echo e(__('Update')); ?></span>
        </button>
        <button type="button" class="process-btn" style='background-color:red' id="nextBtn" onclick="goBack()">
            <span class="txt"><?php echo e(__('Cancel')); ?></span>
        </button>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script>
    var phase = 2;
    const elements = {
        Phase2: document.getElementById('Phase2'),
        FixedPriceGroup: document.getElementById('FixedPriceGroup'),
        conditionsTextarea: document.getElementById('conditionsTextarea')
       
    };
    const form = document.querySelector('form[action="<?php echo e(setRoute('user.make-offer.submit')); ?>"]');
    
    function refreshPhase() {
        if (phase === 2) {
            elements.Phase2.style.display = "";
            const action = document.querySelector('input[name="action"]:checked').value;
            const phase2Title = document.getElementById('phase2Title');
            phase2Title.textContent = action === 'buy' 
                ? "<?php echo e(__('How much would you like to buy?')); ?>" 
                : "<?php echo e(__('How much would you like to sell?')); ?>";
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
            if (priceType == 0) { // 시장가
                document.getElementById('MarketPriceGroup').style.display = "";
                document.getElementById('FixedPriceGroup').style.display = "none";
            } else { // 고정가
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
        
        console.log(delta+"/////"+newValue);
        // 옵션 1: 0 이상의 값만 허용 (기존 로직 유지)
        if(newValue < 0)
        {
            alert("<?php echo e(__('최저 퍼센트 입니다')); ?>");
            $('#offerMargin').val(0.0);
        }
        if (newValue >= 0) {
            $('#offerMargin').val(newValue.toFixed(1));
        }
    }

    //시장 가격 % 단위 변경
    function changeUnitPoints() {
        let unitPoint = document.getElementById('changeUnitPoint').value;  // changeUnits 선택된 값을 가져옴
        switch(unitPoint) {
            case '0.1':
                return 0.1;
            case '1':
                return 1;
            default:
                return 0.1;  // 기본값 1 설정
        }
    }

    let valueUnitPoint = changeUnitPoints();
    document.getElementById('changeUnitPoint').addEventListener('change', function() {
        valueUnitPoint = changeUnitPoints();  
    });

    // $('#margin-decrease').click(
    // 감소 버튼
    document.querySelector('#margin-decrease').addEventListener('click', function() {
        changeOfferMargin(-valueUnitPoint);
    });

    // $('#margin-increase').click(function() {
    // 증가 버튼
    document.querySelector('#margin-increase').addEventListener('click', function() {
        changeOfferMargin(valueUnitPoint);
    });
    // ---- 시장가격 ---- //

    // -----고정 가격---- ///
    function changeFixedPrice(delta) {
        var currentValue = parseInt($('#fixedPrice').val(), 10);
        var newValue = currentValue + delta;
      
        if(newValue <= 0 )
        {
            alert("<?php echo e(__('최저 금액 입니다')); ?>");
            $('#fixedPrice').val(1);
        }
        if (newValue > 0) {
            $('#fixedPrice').val(newValue);
        }
    }

    // 고정 가격 증가 감소 단위 단위 1,10,100
    function changeUnit() {
        let unit = document.getElementById('changeUnits').value;  // changeUnits 선택된 값을 가져옴
        switch(unit) {
            case '1':
                return 1;
            case '10':
                return 10;
            case '100':
                return 100;
            default:
                return 1;  // 기본값 1 설정
        }
    }

    let valueUnit = changeUnit();
    document.getElementById('changeUnits').addEventListener('change', function() {
        valueUnit = changeUnit();  // `changeUnits` 값이 변경될 때마다 `valueUnit` 업데이트
    });
   
    // 고정가격 감소
    document.querySelector('#price-decrease').addEventListener('click', function() {
        changeFixedPrice(-valueUnit);
    });

    // 고정거래 증가
    document.querySelector('#price-increase').addEventListener('click', function() {
        changeFixedPrice(valueUnit);
    });

    // 거래 시간 제한
    function changeTime(delta) {
        var currentValue = parseInt($('.inp-s').val(), 10);
        var newValue = currentValue + delta;
        if (newValue >= 20) {
            $('.inp-s').val(newValue);
        } else if(newValue < 20 ) {
            alert("<?php echo e(__('최소거래시간')); ?>");
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
        const password = document.getElementById('password').value
        const orderTypes = document.getElementById('orderTypes').value;
        // 'secretTrade' 체크박스가 선택되었고, 비밀번호가 비어 있다면 경고
        if(secretTradeCheckbox.checked){
            if(!password){
                alert("<?php echo e(__('Enter 4-digit password')); ?>");
                return false;
            }
        }

        // 폼 데이터를 JSON으로 변환
        const formData = new FormData(form);
        const jsonData = {};

        formData.forEach((value, key) => {
            if(key === 'tradeVolMin' || key === 'tradeVolMax') {
                jsonData[key] = value.replace(/\,/g, '');  // ,를 제거하여 숫자로 처리
            } else {
                jsonData[key] = value;
            }
        });
        
          // 'sell' 주문이 선택되었고, 'maxAmount'가 사용 가능한 'USDT'보다 크면 정지
          if(orderTypes === 'sell'){
            const availableUsdt = <?php echo e($availableUsdt); ?>; 
            if(jsonData.priceType == 1 ) { // 고정가
                if(parseFloat(jsonData.tradeVolMax) > ( availableUsdt * jsonData.fixedPrice)){
                    alert("<?php echo e(__('User does not have sufficient balance.')); ?>")
                    return false;
                }
            } else { // 시장가 
                if(parseFloat(jsonData.tradeVolMax) > ( availableUsdt * ( ( <?php echo e($prices['KRW']); ?> * (100 + jsonData.offerMargin) ) / 100 ) )) {
                    alert("<?php echo e(__('User does not have sufficient balance.')); ?>")
                    return false;
                }
            }
        }

        fetch('/user/make-offer/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            if (data == 1) {
                alert("<?php echo e(__('업데이트 성공하였습니다')); ?>");
                location.reload();
            } else if(data == -1){
                alert("<?php echo e(__('User does not have sufficient balance.')); ?>")
            } else {
                alert("<?php echo e(__('Something went wrong.')); ?>");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("<?php echo e(__('Something went wrong.')); ?>");
        });
    }

    // 뒤로가기
    function goBack(){
        var tradeId = "<?php echo e($tradeOfferId); ?>";
        if('<?php echo e($tradeInfo->order_type); ?>' === 'buy'){
            location.href = '/user/buy-list/preview/'+tradeId;
        } else {
            location.href = '/user/sell-list/preview/'+tradeId;
        }   
    }

    function copyLink(id){
        var input = document.createElement('input');
        input.value = 'https://anakip2p.com/user/sell-list/preview/' + id;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('Link copied to clipboard!');
    }

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/make-offer/updateForm.blade.php ENDPATH**/ ?>