@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@push('style')
    <style>
        .jp-card .jp-card-back, .jp-card .jp-card-front {

        background-image: linear-gradient(160deg, #084c7c 0%, #55505e 100%) !important;
        }
        label{
            color: #000 !important;
        }
        .form--control{
            color: #000 !important;
        }
    </style>
@endpush

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
        <h3 class="title">Add Money</h3>
    </div>
</div>

<div class="row mb-30-none justify-content-center">
    <div class="col-lg-8 mb-30">
        <div class="dash-payment-item-wrapper">
            <div class="dash-payment-item active">
                <div class="dash-payment-title-area">
                    <span class="dash-payment-badge">!</span>
                    <h5 class="title">{{ __($page_title) }}</h5>
                </div>
                <div class="dash-payment-body">
                    <div class="card-wrapper"></div>
                    <br><br>
                    <form role="form" id="payment-form" action="{{setRoute('user.add.money.stripe.payment.confirmed')}}" method="POST">
                        @csrf
                        {{-- <input type="hidden" value="{{$data->track}}" name="track"> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name" class="form--label">@lang('Name on Card')</label>
                                <div class="input-group">
                                    <input type="text" class="form--control custom-input" name="name" autocomplete="off"/>
                                    <span class="input-group-text bg--base"><i class="fa fa-font"></i></span>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <label for="cardNumber" class="form--label">@lang('Card Number')</label>
                                <div class="input-group">
                                    <input type="tel" class="form--control custom-input" name="cardNumber" autocomplete="off" required/>
                                    <span class="input-group-text bg--base"><i class="fa fa-credit-card"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label for="cardExpiry" class="form--label">@lang('Expiration Date')</label>
                                <input type="tel" class="form--control input-sz custom-input" name="cardExpiry" autocomplete="off" required/>
                            </div>
                            <div class="col-md-6 ">
                                <label for="cardCVC" class="form--label">@lang('CVC Code')</label>
                                <input type="tel" class="form--control input-sz custom-input" name="cardCVC" autocomplete="off" required/>
                            </div>
                        </div>
                        <br>
                        <button class="btn--base w-100 text-center btn-loading my-3" type="submit">
                            @lang('PAY NOW') ( {{ number_format(@$hasData->data->amount->total_amount,2 )}} {{ @$hasData->data->amount->sender_cur_code }} )
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('public/frontend/') }}/js/card.js"></script>

<script>
    (function ($) {
        "use strict";
        var card = new Card({
            form: '#payment-form',
            container: '.card-wrapper',
            formSelectors: {
                numberInput: 'input[name="cardNumber"]',
                expiryInput: 'input[name="cardExpiry"]',
                cvcInput: 'input[name="cardCVC"]',
                nameInput: 'input[name="name"]'
            }
        });
    })(jQuery);
</script>
<script>
    $('.cancel-btn').click(function(){
        var dataHref = $(this).data('href');
        if(confirm("Are you sure?") == true) {
            window.location.href = dataHref;
        }
    });
  </script>

@endpush
