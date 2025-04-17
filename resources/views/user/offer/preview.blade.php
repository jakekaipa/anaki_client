@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
<div class="dashboard-area mt-10">
    <div class="dashboard-header-wrapper">
        <h3 class="title">Transaction</h3>
    </div>
</div>
<div class="row justify-content-center mb-30-none">
    <div class="col-lg-6 mb-30">
        <div class="dash-payment-item-wrapper">
            <div class="dash-payment-item active">
                <div class="dash-payment-title-area">
                    <span class="dash-payment-badge">!</span>
                    <h5 class="title">Transaction Preview</h5>
                </div>
                <div class="dash-payment-body">
                    <div class="preview-list-wrapper">
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-funnel-dollar"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Subtotal:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ getDynamicAmount($excrow_offer->rate, $excrow_offer->rateCurrency->code) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-quarter"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Fees & Charge:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ getDynamicAmount($total_charge, $excrow_offer->rateCurrency->code) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-quarter"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Will Pay:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ getDynamicAmount($total_charge + $excrow_offer->rate, $excrow_offer->rateCurrency->code) }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-money-check-alt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span class="last">Seller will Pay:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning last">{{ getDynamicAmount($excrow_offer->amount, $excrow_offer->saleCurrency->code) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="card-form mt-20" action="{{ setRoute('user.offer.buy') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="note-area mb-3">
                                <code class="d-block text--warning">Available Balance: {{ getDynamicAmount($balance, get_default_currency_code()) }}</code>
                            </div>
                        </div>
                        <input type="hidden" name="excrow_id" value="{{ $excrow_offer->id }}">
                        <div class="col-xl-12">
                            <label>Receiving Gateway</label>
                            <select class="form--control nice-select" name="payment_gateway">
                                <option value="">Select Gateway</option>
                                @foreach ($payment_gatewaies as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-12 col-lg-12 mt-3">
                            <button type="submit" class="btn--base w-100">Confirm <i class="fas fa-angle-right ms-1"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush

