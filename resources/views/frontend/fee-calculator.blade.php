@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp
@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start fee calculator section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="fee-calculator-section ptb-120">
    <div class="container">
        <div class="row mb-30-none align-items-center">
            <div class="col-xl-6 col-lg-6 col-md-12 mb-30">
                <div class="thumb me-5">
                    <img src="{{ get_image(@$fee_calculator->value->images->banner_image,'site-section') }}" alt="thumb">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 mb-30">
                <div class="dash-payment-item-wrapper">
                    <div class="dash-payment-item active">
                        <div class="dash-payment-title-area">
                            <span class="dash-payment-badge">!</span>
                            <h5 class="title">{{ @$fee_calculator->value->language->$defualt->heading }}</h5>
                        </div>
                        <form class="card-form mt-30">
                            <div class="intervals" data-intervals="{{ json_encode($intervals) }}"></div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 form-group text-center">
                                    <div class="exchange-area">
                                        <code class="d-block text-center"><span>Exchange Rate</span> <span class="exchange_rate">--</span></code>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <input type="number" class="form--control" placeholder="0.00" name="amount" required>
                                        <select class="form--control nice-select" name="currency">
                                            @foreach ($currencies as $item)
                                                <option value="{{ $item->id }}"
                                                    data-currency="{{ $item->code }}"
                                                    data-rate="{{ $item->rate }}"
                                                >{{ $item->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 form-group">
                                    <label>Escrow Fee</label>
                                    <div class="input-group">
                                        <input type="number" class="form--control" name="rate" placeholder="0.00" value="" readonly>
                                        <select class="form--control nice-select">
                                            <option value="{{ $default_currency->id }}">{{ $default_currency->code }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End fee calculator section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push("script")
    <script>
        var defualCurrency = "{{ get_default_currency_code() }}";
        var defualCurrencyRate = "{{ get_default_currency_rate() }}";

        $(document).ready(function () {
            getPreview();
        });

        $('select[name=currency]').on('change',function(){
            getFees();
            getPreview();
        });

        $("input[name=amount]").keyup(function(){
            getFees();
            getPreview();
        });

        $("input[name=rate]").keyup(function(){
            getFees();
            getPreview();
        });


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
            var sender_amount_cal = charges.sender_amount_cal == undefined ? 0 : charges.sender_amount_cal;

            $('input[name=rate]').val(parseFloat(total_charge).toFixed(2));

            if(min_limit > sender_amount_cal){
                $('input[name=amount]').val('');
                $('input[name="rate"]').val('');
                notification('danger','Minimum fee calculation limit is = '+ min_limit +' '+ sender_currency)
            }
            if(max_limit < sender_amount_cal){
                $('input[name=amount]').val('');
                $('input[name="rate"]').val('');
                notification('danger','Maximum fee calculation limit is = '+ max_limit + ' ' + sender_currency)
            }
        }

        function acceptVar() {
            var selectedVal = $("select[name=currency] :selected");
            var currencyCode = $("select[name=currency] :selected").attr("data-currency");
            var currencyRate = $("select[name=currency] :selected").attr("data-rate");

            return {
                currencyCode: currencyCode,
                currencyRate: currencyRate,
                selectedVal : selectedVal,
            };
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
            var counter = 1;

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
                    total_charge        = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                    total_percent       = value.percent
                }
                counter++;
            });

            return {
                total            : total_charge,
                fixed            : fixed_charge_calc,
                percent          : percent_charge_calc,
                total_percent    : total_percent,
                min_limit        : min_limit,
                max_limit        : max_limit,
                sender_amount_cal: sender_amount_cal,
            };
        }

        function getPreview() {
            var sender_currency = acceptVar().currencyCode;
            var currency_rate = acceptVar().currencyRate;

            // Sending Amount
            $('.exchange_rate').text("1 "+ defualCurrency+ " = " + parseFloat(1 / currency_rate).toFixed(10) +" "+ sender_currency);
        }

    </script>
@endpush

