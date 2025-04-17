@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
<div class="dashboard-area mt-10">
    <div class="dashboard-header-wrapper">
        <h3 class="title">{{ __($page_title) }}</h3>
        <button class="finter-btn"><i class="las la-sort-amount-down"></i></button>
    </div>
</div>
<form class="filter-form" method="GET">
    <div class="row mb-30-none">
        <div class="col-lg-2 mb-30">
            <label>Select Currency</label>
            <select class="form--control nice-select" name="currency">
                @foreach ($currencies as $item)
                <option value="{{ $item->id }}">{{ $item->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 mb-30">
            <label>Select Amount</label>
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" class="form--control" placeholder="Min" name="min_amount">
                </div>
                <div class="col-lg-6">
                    <input type="text" class="form--control" placeholder="Max" name="max_amount">
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-30">
            <label>Select Rate</label>
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" class="form--control" placeholder="Min" name="min_rate">
                </div>
                <div class="col-lg-6">
                    <input type="text" class="form--control" placeholder="Max" name="max_rate">
                </div>
            </div>
        </div>
        <div class="col-lg-2 mb-30">
            <label>Sort Buy</label>
            <select id="input-sort" name="sort_by" class="form--control nice-select">
                <option value="">Default</option>
                <option value="ASC">Price (Low &gt; High)</option>
                <option value="DESC">Price (High &gt; Low)</option>
            </select>
        </div>
        <div class="col-lg-2 mb-30">
            <label>Action</label>
            <button type="submit" class="btn--base w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
    </div>
</form>
<div class="marketplace-wrapper mt-30">
    <div class="row mb-30-none">
        @forelse ($excrows as $item)
            <div class="col-lg-3 mb-30">
                <div class="marketplace-item">
                    <div class="thumb-area">
                        <img src="{{ $item->user->userImage }}" alt="thumb">
                    </div>
                    <div class="content-area">
                        <div class="top-wrapper">
                            <h3 class="title">{{ $item->user->fullname }}</h3>
                            <p><i class="las la-certificate"></i>
                                @if ($item->user->email_verified == 1 && $item->user->kyc_verified == 1)
                                    Verified
                                @else
                                    Unverified
                                @endif
                            </p>
                        </div>
                        <div class="bottom-wrapper">
                            <span class="exchange-badge">1 {{ $item->saleCurrency->symbol }} = {{ getDynamicAmount(($item->rate / $item->amount), null, 10) }} {{ $item->rateCurrency->symbol }}</span>
                            <h4 class="amount-title">{{ getDynamicAmount($item->amount, $item->saleCurrency->code) }}</h4>
                            <span class="divider"><i class="las la-exchange-alt"></i></span>
                            <h4 class="amount-title">{{ getDynamicAmount($item->rate, $item->rateCurrency->code) }}</h4>
                            <div class="btn-area">
                                <a href="" class="btn--base bg-warning make_offer_button" data-forexcrow="{{ json_encode($item) }}">Counter</a>
                                <a href="{{ setRoute('user.marketplace.preview', $item->id) }}" class="btn--base">Buy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <h3 class="text-warning text-center">No Data Found</h3>
            </div>
        @endforelse
    </div>
</div>
<nav>
    {{ $excrows->links() }}
</nav>

<div class="modal fade" id="offerCounterModal" tabindex="-1" aria-labelledby="offerCounterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content counter-modal">
        <h4 class="title"><i class="fas fa-sync title-icon"></i> Make Counter Offer</h4>
        <form class="card-form mt-20" id="offer_form" action="{{ setRoute('user.offer.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="OFFER">
            <input type="hidden" name="receiver_id" value="">
            <input type="hidden" name="creator_id" value="">
            <input type="hidden" name="forexcrow_id" value="">
            <div class="row">
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>Amount</label>
                    <div class="input-group">
                        <input type="text" name="amount" class="form--control" placeholder="" disabled required>
                        <div class="input-group-append">
                            <span class="input-group-text copytext sale_currency"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <div class="note-area">
                        <code class="d-block amount_text">--</code>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>Rate</label>
                    <div class="input-group">
                        <input type="text" name="rate" class="form--control" placeholder="" required>
                        <div class="input-group-append">
                            <span class="input-group-text copytext rate_curency"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <div class="note-area">
                        <code class="d-block rate_text">--</code>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12">
                    <button type="submit" class="btn--base w-100 btn-loading">Send Now</button>
                    {{-- <button class="btn--base w-100 btn-loading">Send Now <i class="fas fa-paper-plane ms-1"></i></button> --}}
                </div>
            </div>
        </form>
      </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $("select").niceSelect();

    var offerModal = new bootstrap.Modal(document.getElementById("offerCounterModal"), {});
    @if (Session::has('modal'))
        document.onreadystatechange = function () {
            offerModal.show();
        };
    @endif

    $(document).on('click', '.make_offer_button', function(e){
        e.preventDefault();

        var forexcrow =  JSON.parse($(this).attr('data-forexcrow'));

        $('#offer_form input[name="forexcrow_id"]').val(forexcrow.id);
        $('#offer_form input[name="amount"]').val(parseFloat(forexcrow.amount).toFixed(2));
        $('#offer_form .amount_text').text('Amount : '+ parseFloat(forexcrow.amount).toFixed(2) + ' ' + forexcrow.sale_currency.code);
        $('#offer_form .rate_text').text('Rate : '+ parseFloat(forexcrow.rate).toFixed(2) + ' ' + forexcrow.rate_currency.code);
        $('#offer_form input[name="currency"]').val(forexcrow.currency_id);
        $('#offer_form input[name="rate_currency"]').val(forexcrow.rate_currency_id);
        $('#offer_form .sale_currency').text(forexcrow.sale_currency.code);
        $('#offer_form .rate_curency').text(forexcrow.rate_currency.code);
        $('#offer_form input[name="rate"]').val(parseFloat(forexcrow.rate).toFixed(2));

        $('#offerCounterModal').modal('show');
    })

    $(document).ready(function () {
        $(document).on('click', '#sort-modal .range_select', function (e) {
            var original_range = $(this).val();
            const range_array = original_range.split("|");
            $('#sort-modal input[name="min_amount"]').val(range_array[0]);
            $('#sort-modal input[name="max_amount"]').val(range_array[1]);
        });
    });

</script>
@endpush
