@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp
@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Faq
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="faq-section ptb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 text-center">
                <div class="section-header">
                    <span class="section-sub-titel"><i class="las la-dot-circle"></i> {{ strtoupper(@$faq->value->language->$defualt->title) }}</span>
                    <h2 class="section-title"> {{ @$faq->value->language->$defualt->heading }}</h2>
                    <p>{{ @$faq->value->language->$defualt->sub_heading }}</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6">
                <div class="faq-wrapper">
                    @if(isset($faq->value->items))
                        @php
                            $count = 1;
                        @endphp
                        @foreach($faq->value->items ?? [] as $key => $item)
                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">{{ $item->language->$defualt->question }}</span><span
                                        class="right-icon"></span></h3>
                                <div class="faq-content">
                                    <p>{{ $item->language->$defualt->answer }}</p>
                                </div>
                            </div>
                        @php
                            $count++;
                        @endphp
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Faq
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push("script")

@endpush
