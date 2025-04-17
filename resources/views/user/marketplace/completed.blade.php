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
<div class="card-area">
    <div class="row justify-content-center mb-20-none">
        <div class="col-lg-8 col-md-8 col-12 mb-20">
            <div class="card custom--card text-center">
                <div class="user-text">
                    <h4>Transaction Created!</h4>
                    <p>Your post will be approved once your funds have been received. Send your funds to forexcrow@gmail.com. You can share this URL or QR code with a potential buyer if you already have one!</p>
                </div>
                <div class="card-body">
                    <div class="form-group mx-auto mt-4 text-center">
                        <img class="mx-auto" src="{{ generateQr('sohag') }}">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="key" value="https://www.appdevs.net/" class="form--control form--control ref-input" id="referralURL" readonly="">
                            <div class="input-group-append">
                                <span class="input-group-text copytext" id="copyBoard" onclick="myFunction()">
                                    <i class="la la-copy"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group shear-icon text-center mt-4">
                        <i class="lab la-whatsapp"></i>
                    </div>
                </div>
                <div class="btn">
                    <button type="button" class="btn--base w-50">Okay</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
