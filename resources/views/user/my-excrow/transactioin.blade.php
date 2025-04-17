@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Dashboard")])
@endsection

@section('content')
<div class="dashboard-area mt-10">
    <div class="dashboard-header-wrapper">
        <h3 class="title">{{ __($page_title) }}</h3>
    </div>
</div>
<div class="row mb-30-none">
    <div class="col-lg-6 mb-30">
        <div class="dash-payment-item-wrapper">
            <div class="dash-payment-item active">
                <div class="dash-payment-title-area">
                    <span class="dash-payment-badge">!</span>
                    <h5 class="title">Start Transaction</h5>
                </div>
                <div class="dash-payment-body">
                    <div class="intervals" data-intervals="{{ json_encode($intervals) }}"></div>
                    <form class="card-form" action="{{ setRoute('user.my-excrow.submit') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 form-group text-center">
                                <div class="exchange-area">
                                    <code class="d-block text-center"><span>Selling Exchange Rate</span> <span class="exchange_rate">--</span></code>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>Selling Amount <span class="text--base">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form--control" placeholder="0.00" required name="amount" maxlength="20" value="{{ old('amount') }}">
                                    <select class="form--control nice-select" style="display: none;" name="currency">
                                        @foreach ($currency as $item)
                                            <option value="{{ $item->id }}"
                                                data-currency="{{ $item->code }}"
                                                data-rate="{{ $item->rate }}"
                                            >{{ $item->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>Asking Rate <span class="text--base">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form--control rate_amount" placeholder="0.00" name="rate" required maxlength="20" value="{{ old('rate') }}">
                                    <select class="form--control nice-select" style="display: none;" name="rate_currency">
                                        <option value="{{ $default_currency->id }}">{{ $default_currency->code }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                <div class="note-area">
                                    <code class="d-block">Available Balance: {{ get_amount($wallet->balance) }} {{ $default_currency->code }}</code>
                                    <code class="d-block fees-show">Charge: 0.00 USD + 0.00% = 0.00 USD</code>
                                </div>
                                <div class="note-area mt-2">
                                    <code class="d-block limit-show">Limit: 0.00 USD - 0.00 USD</code>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12">
                                <button type="submit" class="btn--base w-100 btn-loading">Confirm <i class="fas fa-angle-right ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                                        <span>Enter Selling Amount:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="request_amount">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-sync"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Buying Excange Rate:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="buying_exchange_rate">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-calculator"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Total Buying Amount:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="subtotal">--</span>
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
                                <span class="total_fees">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="lab la-get-pocket"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>Buyer Will Pay:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="buyer_will_pay">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-money-check-alt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span class="last">You Will Pay:</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning last will_pay">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $("select").niceSelect()

        var defualCurrency = "{{ get_default_currency_code() }}";
        var defualCurrencyRate = "{{ get_default_currency_rate() }}";

        $(document).ready(function () {
            getPreview();
            getFees();
        });


        $('select[name=currency]').on('change',function(){
            getPreview();
            getFees();
        });

        $("input[name=amount]").keyup(function(){
            getPreview();
            getFees();
        });

        $("input[name=rate]").keyup(function(){
            getPreview();
            getFees();
        });


        function acceptVar() {
            var selectedVal = $("select[name=currency] :selected");
            var currencyCode = $("select[name=currency] :selected").attr("data-currency");
            var currencyRate = $("select[name=currency] :selected").attr("data-rate");

            return {
                currencyCode:currencyCode,
                currencyRate:currencyRate,
                selectedVal:selectedVal,
            };
        }

        // Get Fees
        function getFees() {
            var sender_currency = acceptVar().currencyCode;
            var sender_currency_rate = acceptVar().currencyRate;

            var charges = feesCalculation();

            if (charges == false) {
                return false;
            }

            var total_charge   = charges.total   == undefined ? 0 : charges.total;
            var fixed_charge   = charges.fixed   == undefined ? 0 : charges.fixed;
            var percent_charge = charges.percent == undefined ? 0 : charges.percent;
            var total_percent = charges.total_percent == undefined ? 0 : charges.total_percent;
            var min_limit = charges.min_limit == undefined ? 0 : charges.min_limit * sender_currency_rate;
            var max_limit = charges.max_limit == undefined ? 0 : charges.max_limit * sender_currency_rate;

            $('.fees_charge').text(parseFloat(total_charge).toFixed(2)+ ' ' + defualCurrency);
            $('.fees-show').text('Charge: '+ parseFloat(fixed_charge).toFixed(2) + ' ' +defualCurrency+ ' + '+ parseFloat(total_percent).toFixed(2) + '%');
            $('.limit-show').text('Limit: '+ parseFloat(min_limit).toFixed(2) + ' ' +sender_currency+ ' - '+ parseFloat(max_limit).toFixed(2) + ' ' + sender_currency);
        }

        // Fees Calculation

        function feesCalculation(){
            var currency_rate = acceptVar().currencyRate;
            var sender_amount = $("input[name=amount]").val();
            var sender_amount_cal = (sender_amount/currency_rate);

            var total_charge;
            var fixed_charge_calc;
            var percent_charge_calc;
            var total_percent;

            let intervals = JSON.parse($('.intervals').attr("data-intervals"));

            var min_limit;
            var max_limit;
            var n = intervals.length;
            var counter = 1

            $.each(intervals, function (key, value) {
                if(counter == 1){
                    min_limit = value.min_limit;
                }
                if(key == n - 1){
                    max_limit = value.max_limit;
                }
                max_limit = value.max_limit;
                if (value.min_limit <= sender_amount_cal && value.max_limit >= sender_amount_cal) {
                    fixed_charge_calc   = value.charge;
                    percent_charge_calc = (parseFloat(sender_amount_cal) * parseFloat(value.percent)) / 100;
                    total_charge = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                    total_percent = value.percent
                }
                counter++;
            });

            return {
                total: total_charge,
                fixed: fixed_charge_calc,
                percent: percent_charge_calc,
                total_percent: total_percent,
                min_limit: min_limit,
                max_limit: max_limit
            };
        }

        function getPreview() {
            var senderAmount = $("input[name=amount]").val();
            var rate_amount = $(".rate_amount").val();
            var sender_currency = acceptVar().currencyCode;
            var currency_rate = acceptVar().currencyRate;

            senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

            subtotal = (senderAmount / currency_rate);

            var charges = feesCalculation();
            var total_charge = charges.total == undefined ? 0 : charges.total;
            var fixed_charge   = charges.fixed;
            var percent_charge = charges.percent;
            var pay_total = parseFloat(total_charge) + parseFloat(subtotal);

            var will_pay = isNaN(pay_total) ? 0 : pay_total;
            var rate = rate_amount == "" ? 0 : rate_amount;

            let exchange_rate =  rate / (senderAmount == 0 ? currency_rate : senderAmount);
            exchange_rate = exchange_rate == '' ? 0 : exchange_rate;
            exchange_rate = parseFloat(exchange_rate).toFixed(10);

            // Sending Amount
            $('.exchange_rate').text("1 "+ sender_currency+ " = " + exchange_rate +" "+ defualCurrency);
            $('.buying_exchange_rate').text("1 "+ sender_currency+ " = " + parseFloat(1 / currency_rate).toFixed(10) +" "+ defualCurrency);
            $('.total_fees').text(parseFloat(total_charge).toFixed(2)+ ' ' + defualCurrency);
            $('.request_amount').text(parseFloat(senderAmount).toFixed(2) + " " + sender_currency);
            $('.subtotal').text(parseFloat(subtotal).toFixed(2) + " " + defualCurrency);
            $('.will_pay').text(parseFloat(will_pay).toFixed(2) + " " + defualCurrency);
            $('.buyer_will_pay').text(parseFloat(rate).toFixed(2) + " " + defualCurrency);
        }

    </script>
@endpush
