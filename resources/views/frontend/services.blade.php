@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp
@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start service section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="service-section ptb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="section-header">
                    <span class="section-sub-titel"><i class="las la-dot-circle"></i> {{ strtoupper(@$service->value->language->$defualt->title) }}</span>
                    <h2 class="section-title">{{ @$service->value->language->$defualt->heading }}</h2>
                    <p>{{ @$service->value->language->$defualt->sub_heading }}</p>
                </div>
            </div>
        </div>
        <div class="row mb-30-none">
            @if(isset($service->value->items))
                @php
                    $count = 1;
                @endphp
                @foreach($service->value->items ?? [] as $key => $item)
                    <div class="col-lg-6 col-md-6 mb-30">
                        <div class="service-item">
                            <span class="icon"><i class="{{ $item->icon ?? 'fas fa-user' }}"></i></span>
                            <div class="service-content">
                                <h4 class="title">{{ @$item->language->$defualt->name }}</h4>
                                <p>{{ @$item->language->$defualt->details }}</p>
                                <div class="service-bg"></div>
                            </div>
                        </div>
                    </div>
                @php
                    $count++;
                @endphp
                @endforeach
            @endif
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End service section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push("script")

@endpush
