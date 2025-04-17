@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="content purchase">
        <h2 class="page-tit">{{ __('Sell Tether (USDT)') }}</h2>
        {{-- <h2 class="page-tit">{{ __($page_title) }}</h2> --}}

        {{-- <form class="filter-form" method="GET">
            <div class="row mb-30-none">
                <div class="col-lg-2 mb-30">
                    <label>내 거래 여부</label>
                    <select class="form--control nice-select" name="myOffer">
                        <option value="">{{ __('All') }}</option>
                        <option value="0">내 거래</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>가격 타입</label>
                    <select class="form--control nice-select" name="priceType">
                        <option value="">{{ __('All') }}</option>
                        <option value="0">{{ __('Market Order') }}</option>
                        <option value="1">{{ __('Limit Order') }}</option>
                    </select>
                </div>
                <div class="col-lg-3 mb-30">
                    <label>제안 태그</label>
                    <select class="form--control nice-select" name="offerTag">
                        <option value="">{{ __('All') }}</option>
                        <option value="needID" style="color: black">{{ __('Need ID Card Picture') }}</option>
                        <option value="specApproval" style="color: black">{{ __('Specification Approval') }}</option>
                        <option value="noNeedReceipt" style="color: black">{{ __('No Receipt Required') }}</option>
                        <option value="onlySameBank" style="color: black">{{ __('Same Bank Only') }}</option>
                        <option value="receiptRequired" style="color: black">{{ __('Receipt Required') }}</option>
                        <option value="noThirdParty" style="color: black">{{ __('No Third Party') }}</option>
                        <option value="noAuthRequired" style="color: black">{{ __('No Authentication Required') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>정렬</label>
                    <select id="input-sort" name="sort_by" class="form--control nice-select">
                        <option value="MostRecent">{{ __('Most Recent') }}</option>
                        <option value="Oldest">{{ __('Oldest') }}</option>
                        <option value="RecentLogin">최신 로그인 순</option>
                        <option value="OldestLogin">오래된 로그인 순</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>로그인 시간</label>
                    <select id="loginTime" name="loginTime" class="form--control nice-select">
                        <option value="">전체</option>
                        <option value="In30Min">30분 이내</option>
                        <option value="In1Hour">1시간 이내</option>
                        <option value="In5Hour">5시간 이내</option>
                        <option value="In10Hour">10시간 이내</option>
                        <option value="In24Hour">24시간 이내</option>
                    </select>
                </div>
                <div class="col-lg-2 mb-30">
                    <label>검색</label>
                    <button type="submit" class="btn--base w-100"><i
                            class="fas fa-search me-1"></i>{{ __('Search') }}</button>
                </div>
            </div>
        </form> --}}

        <div class="container-row">
            <ul class="plist" id="buy-list-container">
                @if (isset($listData) && $listData->count() > 0)
                    @include('user.buy-list.partials.list')
                @else
                    <li class="plist__item">
                        <p class="text-center">{{ __('No data available') }}</p>
                    </li>
                @endif
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
        });

        function loadMoreItems() {
            currentPage++;
            fetch(`{{ route('user.buy-list.index') }}?page=${currentPage}`, {
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
                        document.querySelector('.purchase-more').style.display = 'none';
                        return;
                    }
                    const container = document.getElementById('buy-list-container');
                    container.insertAdjacentHTML('beforeend', html);
                    addItemClickListeners();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('An error occurred while loading data. Please try again.') }}');
                });
        }

        function addItemClickListeners() {
            document.querySelectorAll('.plist__btn button, .plist__btn a').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        }
    </script>
@endpush
