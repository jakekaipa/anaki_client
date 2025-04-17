@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="content">
        <h2 class="page-tit">{{ __($page_title) }}</h2>

        <div class="container-row">
            <div class="card-box">
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
                            <col class="col10">
                        </colgroup>

                        <thead>
                            <tr>
                                <th>{{ __('거래 구분') }}</th>
                                <th>{{ __('거래자 이름') }}</th>
                                <th>{{ __('Tether Purchase Amount') }}</th>
                                <th>{{ __('Purchase Price') }}</th>
                                <th>{{ __('Offered Price') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Trade start time') }}</th>
                                <th>{{ __('Trade end time') }}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="trade-list-container">
                            @include('user.my-trade.partials.list')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;" id="load-more-container">
            <button class="btn-other btn-small" id="load-more" style="padding-left: 30px; padding-right:30px;">
                <span class="txt">{{ __('Load More Trades') }}</span>
            </button>
        </div>
    </div>

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="sell_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('거래 상세 정보') }}</h2>
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
                                    <span class="data-txt">gonikim</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매자 이름') }}</span>
                                <div class="data">
                                    <span class="data-txt">jaeookryu</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('Offered Price') }}</span>
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
                                <span class="label">{{ __('판매금액') }}</span>
                                <div class="data">
                                    <span class="data-txt">100,000 KRW</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('테더 판매 수량') }}</span>
                                <div class="data">
                                    <span class="data-txt">68.28</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매 제안 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt">2024.08.03 11:41</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래 종료 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt">2024.08.04 12:11</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래은행') }}</span>
                                <div class="data">
                                    <span class="data-txt">카카오뱅크(3333027208895) 홍길동</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div class="buy-row">
                                <span class="ps-row-tit">{{ __('입금증빙') }}</span>
                                <div class="pay-proof-img"><img src="" alt="{{ __('입금 증빙 이미지') }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn white w-200" onclick="onDownloadPDFButton()">
                        <span class="txt pdf">{{ __('PDF 출력') }}</span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('sell_detail')"><img
                        src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="{{ __('팝업시트 닫기') }}"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->

    <!-- pop-sheet -->
    <div class="pop-sheet" data-pop-sheet="buy_detail">
        <div class="ps-inner">
            <div class="ps-container">
                <div class="ps-head">
                    <h2 class="ps-tit">{{ __('거래 상세 정보') }}</h2>
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
                                    <span class="data-txt">gonikim</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매자 이름') }}</span>
                                <div class="data">
                                    <span class="data-txt">jaeookryu</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('제안가') }}</span>
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
                                <span class="label">{{ __('구매금액') }}</span>
                                <div class="data">
                                    <span class="data-txt">100,000 KRW</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('테더 구매 수량') }}</span>
                                <div class="data">
                                    <span class="data-txt">68.28</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('구매 제안 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt">2024.08.03 11:41</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래 종료 시간') }}</span>
                                <div class="data">
                                    <span class="data-txt">2024.08.04 12:11</span>
                                </div>
                            </li>
                            <li>
                                <span class="label">{{ __('거래은행') }}</span>
                                <div class="data">
                                    <span class="data-txt">카카오뱅크(3333027208895) 홍길동</span>
                                </div>
                            </li>
                        </ul>

                        <div class="dt-buy">
                            <div id="qr-code" class="buy-row">
                                <span class="ps-row-tit">QR Code</span>
                                <div class="qr-img">
                                    <img src="{{ asset('/public/pub') }}/img/qr-code@2x.jpg" alt="{{ __('qr코드 이미지') }}">
                                </div>
                                <div class="qr-save-button" style="display: flex; flex-direction: column; align-items: center;">
                                    <button class="ps-process-btn w-150" onclick="saveQRCode()">
                                        <span class="txt">{{ __('QR 코드 저장') }}</span>
                                    </button>
                                </div>
                            </div>

                            <div class="buy-row">
                                <span class="ps-row-tit">{{ __('지급 증빙') }}</span>

                                <div class="attach-img" id="attachedImage"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ps-process">
                    <button class="ps-process-btn white w-200" onclick="onDownloadPDFButton()">
                        <span class="txt pdf">{{ __('PDF 출력') }}</span>
                    </button>
                </div>

                <button class="ps-close" onclick="closePopSheet('buy_detail')"><img
                        src="{{ asset('/public/pub') }}/img/close-popup@2x.png" alt="{{ __('팝업시트 닫기') }}"></button>
            </div>
        </div>
    </div>
    <!-- pop-sheet -->
@endsection

@push('style')
    <style>
        .qr-save-button {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
@endpush

@push('script')
    <script>
        let currentPage = 1;
        let itemId;

        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadMoreItems);
            }
        });

        function loadMoreItems() {
            currentPage++;
            fetch(`{{ route('user.mytrade.index') }}?page=${currentPage}`, {
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
                    if (html.trim() === '' || html.includes('<div class="deal-empty">')) {
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
                    alert('{{ __('An error occurred while loading data. Please try again.') }}');
                });

        }

        function showTradeDetails(_order_id, encryptOrderId) {

            itemId = encryptOrderId;

            fetch(`{{ route('user.mytrade.details', '') }}/${encryptOrderId}`)
                .then(response => response.json())
                .then(data => {
                    // console.log(JSON.stringify(data));

                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    if ((data.order_type === 'sell' && data.is_buyer == false) ||
                        (data.order_type === 'buy' && data.is_buyer)) {
                        updateSellDetailPopup(data);
                        openPopSheet('sell_detail');
                    } else {
                        updateBuyDetailPopup(data);
                        openPopSheet('buy_detail');
                    }

                    // if (data.order_type == 'sell') {
                    //     updateSellDetailPopup(data);
                    //     openPopSheet('sell_detail');
                    // } else {
                    //     updateBuyDetailPopup(data);
                    //     openPopSheet('buy_detail');
                    // }

                })
                .catch(error => {
                    console.error('Error fetching trade details:', error);
                    alert('{{ __('Error fetching trade details. Please try again.') }}');
                });
        }

        function updateSellDetailPopup(data) {
            // console.log(11);
            let statusMsg = "";
            switch(data.state) {
                case "cancel":
                    statusMsg = "{{ __('Cancel') }}"
                    break;
                case "done":
                    statusMsg = "{{ __('Complete') }}"
                    break;
            }
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(1) .data-txt').textContent = statusMsg;


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

            // // Update seller and buyer names
            // document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(2) .data-txt').textContent =
            // data.offer_realname == null ? data.offer_username : data.offer_realname ;
            // document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(3) .data-txt').textContent =
            // data.client_realname == null ? data.client_username :  data.client_realname ;


            // Update price information
            const priceElement = document.querySelector('[data-pop-sheet="sell_detail"] .data-price');
            const priceChange = parseFloat(data.price_change);
            let priceGap = '';
            // console.log(data.price_type+'////'+data.price_change+'/////'+data.margin);
            if(data.price_type == 0){ // 시장가
                priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>`;
            } else { // 고정가
                //priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${priceChange} {{ __('KRW') }}</i>`;
                priceGap = '';
            }
            priceElement.innerHTML = `
                ${parseFloat(data.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} {{ __('KRW') }}
                ${priceGap}
            `;

            /*
                ${Number(data.price).toLocaleString()} {{ __('KRW') }}
                ${priceGap}
            */

            document.querySelector('[data-pop-sheet="sell_detail"] .market-price').textContent =
                `{{ __('시장가') }}: ${Number(parseFloat(data.market_price).toFixed(2)).toLocaleString()} {{ __('KRW') }}`;

            // Update purchase amount and tether amount
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(5) .data-txt').textContent =
                `${Number(parseFloat(data.total_amount).toFixed(2)).toLocaleString()} {{ __('KRW') }}`;
            document.querySelector('[data-pop-sheet="sell_detail"] li:nth-child(6) .data-txt').textContent = Number(
                parseFloat(data
                    .tether_amount).toFixed(2)).toLocaleString();

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
            const payProofImg = payProofContainer.querySelector('.pay-proof-img img');
            if (payProofImg && data.pay_proof) {
                payProofImg.src = "/frontend/proof/" + data.pay_proof;
            } else {
                payProofImg.style.display = 'none';
            }

            // Update buttons based on trade status
            // const cancelButton = document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.cancel');
            const pdfButton = document.querySelector('[data-pop-sheet="sell_detail"] .ps-process-btn.white');
            // if (data.state === "open") {
            //     cancelButton.style.display = 'block';
            // } else {
            //     cancelButton.style.display = 'none';
            // }
            if (data.state === "done") {
                pdfButton.style.display = 'block';
            } else {
                pdfButton.style.display = 'none';
            }
        }

        function updateBuyDetailPopup(data) {
            // console.log(22);
            let statusMsg = "";
            switch(data.state) {
                case "cancel":
                    statusMsg = "{{ __('Cancel') }}"
                    break;
                case "done":
                    statusMsg = "{{ __('Complete') }}"
                    break;
            }
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(1) .data-txt').textContent = statusMsg;
            
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
            
            // Update seller and buyer names
            // document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(2) .data-txt').textContent =
            // data.client_realname == null ? data.client_username :  data.client_realname ;
            // document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(3) .data-txt').textContent = 
            // data.offer_realname == null ? data.offer_username : data.offer_realname ;

            // Update price information
            const priceElement = document.querySelector('[data-pop-sheet="buy_detail"] .data-price');
            const priceChange = parseFloat(data.price_change);
            let priceGap = '';
            // console.log(data.price_type+'////'+data.price_change+'/////'+data.margin);
            if(data.price_type == 0){ // 시장가
                priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${data.margin}%</i>`;
            } else { // 고정가
                //priceGap = `<i class="${priceChange >= 0 ? 'up' : 'down'}">${priceChange} {{ __('KRW') }}</i>`;
                priceGap = '';
            }
            priceElement.innerHTML = `
                ${parseFloat(data.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} {{ __('KRW') }}
                ${priceGap}
            `;
           
            // const formattedPrice = Number(parseFloat(data.price).toFixed(2)).toLocaleString();
            // priceElement.innerHTML = `
            //     ${formattedPrice} {{ __('KRW') }}
            //     <i class="${priceChange >= 0 ? 'up' : 'down'}">${Math.abs(priceChange).toFixed(2)}</i>
            // `;

            document.querySelector('[data-pop-sheet="buy_detail"] .market-price').textContent =
                `{{ __('시장가') }}: ${Number(parseFloat(data.market_price).toFixed(2)).toLocaleString()} {{ __('KRW') }}`;

            // Update purchase amount and tether amount
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(5) .data-txt').textContent =
                `${Number(parseFloat(data.total_amount).toFixed(2)).toLocaleString()} KRW`;
            document.querySelector('[data-pop-sheet="buy_detail"] li:nth-child(6) .data-txt').textContent = Number(
                parseFloat(data
                    .tether_amount).toFixed(2)).toLocaleString();

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
                qrCodeImg.alt = "QR 코드 이미지";
                qrCode.style.display = 'block';
            } else {
                qrCode.style.display = 'none';
            }

            // Update payment proof
            const attachImgDiv = document.querySelector('[data-pop-sheet="buy_detail"] .attach-img');
            const attachFileDiv = document.querySelector('[data-pop-sheet="buy_detail"] .attach-file');
            if (data.pay_proof) {
                attachImgDiv.innerHTML = `<img src="/frontend/proof/${data.pay_proof}" alt="지급 증빙 이미지">`;
                attachImgDiv.style.display = '';
                // attachFileDiv.style.display = 'none';
            } else {
                // attachImgDiv.style.display = 'none';
                // attachFileDiv.style.display = 'block';
            }

            // Update buttons based on trade status
            // const cancelButton = document.querySelector('[data-pop-sheet="buy_detail"] .ps-process-btn.cancel');
            const pdfButton = document.querySelector('[data-pop-sheet="buy_detail"] .ps-process-btn.white');
            // if (data.state === "open") {
            //     cancelButton.style.display = 'block';
            // } else {
            //     cancelButton.style.display = 'none';
            // }
            if (data.state === "done") {
                pdfButton.style.display = 'block';
            } else {
                pdfButton.style.display = 'none';
            }
        }

        function onDownloadPDFButton() {
            window.location.href = `{{ route('user.mytrade.downloadpdf', '') }}/${itemId}`;
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
                    alert('{{ __('QR 코드 저장 중 오류가 발생했습니다.') }}');
                });
        }
    </script>
@endpush
