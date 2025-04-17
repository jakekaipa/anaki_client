@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp
@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="contact-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-7 col-lg-7 col-md-6 mb-30">
                <div class="row justify-content-center mb-30-none">
                    <div class="col-lg-6 col-md-12 col-sm-6 mb-30">
                        <div class="contact-widget-item">
                            <div class="icon-area">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('PHONE NUMBER') }}</h4>
                                <span>{{ @$contact_us->value->language->$defualt->phone }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-6 mb-30">
                        <div class="contact-widget-item">
                            <div class="icon-area">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('EMAIL ADDRESS') }}</h4>
                                <span>{{ @$contact_us->value->language->$defualt->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-6 mb-30">
                        <div class="contact-widget-item">
                            <div class="icon-area">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('LOCATION') }}</h4>
                                <span>{{  @$contact_us->value->language->$defualt->location  }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-6 mb-30">
                        <div class="contact-widget-item">
                            <div class="icon-area">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('SCHEDULE') }}</h4>
                                <span>{{  @$contact_us->value->language->$defualt->office_hours  }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-md-6 mb-30">
                <div class="contact-form-area">
                    <form class="contact-form" method="POST" id="contact-form">
                        @csrf
                        <h3 class="contact-form-title">{{ @$contact_us->value->language->$defualt->heading }}</h3>
                        <div class="row justify-content-center mb-10-none">
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <label>Name <span>*</span></label>
                                <input type="text" name="name" class="form--control" placeholder="Enter Name..." required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 form-group">
                                <label>Email <span>*</span></label>
                                <input type="email" name="email" class="form--control" placeholder="Enter Email..." required>
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                <label>Message <span>*</span></label>
                                <textarea class="form--control" placeholder="Write Here..." name="message" required></textarea>
                            </div>
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn--base contact-btn"><i class="fas fa-paper-plane ms-1 button-icon"></i> <i class="fa fa-spinner d-none fa-pulse fa-fw"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Contact
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push("script")

@endpush

