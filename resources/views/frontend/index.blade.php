@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp

@section('content')


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner flotting
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="banner-flotting-section">
    <div class="container">
        <div class="banner-flotting-wrapper">
            <div class="thumb">
                <img src="{{ asset('public/frontend/') }}/images/element/bg-sale-1.png" alt="thumb">
            </div>
            <div class="content-wrapper">
                <div class="content">
                    <h2 class="title">{{ @$download->value->language->$defualt->heading }}</h2>
                    <p>{{ @$download->value->language->$defualt->sub_heading }}</p>
                </div>
                <div class="btn-area">
                    <a href="{{ @$download->value->button_link }}">{{ @$download->value->language->$defualt->button_name }}</a>
                    <div class="content">
                        <span><i class="las la-lightbulb"></i> {{ @$download->value->language->$defualt->title }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner flotting
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->




@endsection


@push("script")

@endpush
