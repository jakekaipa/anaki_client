@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="content purchase">
        {{-- <h2 class="page-tit">{{ __($page_title) }}</h2> --}}
        <h2 class="page-tit">{{ __('Buy Tether (USDT)') }}</h2>

        {{-- <form class="filter-form" method="GET">
            <div class="row mb-30-none">
                <div class="col-lg-2 mb-30">
                    <label>{{ __('my_trades') }}</label>
                    <select class="form--control nice-select" name="myOffer">
                        <option value="">{{ __('all') }}</option>
                        <option value="0">{{ __('my_trades') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>{{ __('price_type') }}</label>
                    <select class="form--control nice-select" name="priceType">
                        <option value="">{{ __('all') }}</option>
                        <option value="0">{{ __('market_order') }}</option>
                        <option value="1">{{ __('limit_order') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 mb-30">
                    <label>{{ __('offer_tag') }}</label>
                    <select class="form--control nice-select" name="offerTag">
                        <option value="">{{ __('all') }}</option>
                        <option value="needID">{{ __('need_id_card_picture') }}</option>
                        <option value="specApproval">{{ __('specification_approval') }}</option>
                        <option value="noNeedReceipt">{{ __('no_receipt_required') }}</option>
                        <option value="onlySameBank">{{ __('same_bank_only') }}</option>
                        <option value="receiptRequired">{{ __('receipt_required') }}</option>
                        <option value="noThirdParty">{{ __('no_third_party') }}</option>
                        <option value="noAuthRequired">{{ __('no_authentication_required') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>{{ __('sort') }}</label>
                    <select id="input-sort" name="sort_by" class="form--control nice-select">
                        <option value="MostRecent">{{ __('most_recent') }}</option>
                        <option value="Oldest">{{ __('oldest') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>{{ __('login_time') }}</label>
                    <select id="loginTime" name="loginTime" class="form--control nice-select">
                        <option value="">{{ __('all') }}</option>
                        <option value="In30Min">{{ __('within_30_min') }}</option>
                        <option value="In1Hour">{{ __('within_1_hour') }}</option>
                        <option value="In5Hour">{{ __('within_5_hours') }}</option>
                        <option value="In10Hour">{{ __('within_10_hours') }}</option>
                        <option value="In24Hour">{{ __('within_24_hours') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>{{ __('search') }}</label>
                    <button type="submit" class="btn--base w-100"><i
                            class="fas fa-search me-1"></i>{{ __('search') }}</button>
                </div>
            </div>
        </form> --}}

        <div class="container-row">
            <ul class="plist" id="sell-list-container">
                @include('user.sell-list.partials.list')
            </ul>
        </div>

        <div class="purchase-more">
            @if ($listData->hasMorePages())
                <button class="btn-other btn-small" id="load-more">
                    <span class="txt">{{ __('Load More Trades') }}</span>
                </button>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadMoreItems);
            }

            addItemClickListeners();

            function loadMoreItems() {
                currentPage++;
                fetch(`{{ route('user.sell-list.index') }}?page=${currentPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const container = document.getElementById('sell-list-container');
                        container.insertAdjacentHTML('beforeend', html);
                        addItemClickListeners();

                        // 더 이상 페이지가 없으면 "더 불러오기" 버튼을 숨깁니다
                        if (html.trim() === '') {
                            document.querySelector('.purchase-more').style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function addItemClickListeners() {
                document.querySelectorAll('.plist__btn button, .plist__btn a').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            }
        });
    </script>
@endpush
