@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp
@section('content')

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start about section
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <section class="about-section ptb-120">
        <div class="container">
            <div class="row mb-30-none align-items-center">
                <div class="col-xl-6 col-lg-6 col-md-12 mb-30">
                    <div class="about-content-wrapper">
                        <div class="about-content-area">
                            <div class="section-header">
                                <span class="section-sub-titel"><i class="las la-dot-circle"></i>
                                    {{ strtoupper(@$about->value->language->$defualt->title) }}</span>
                                <h2 class="section-title">{{ @$about->value->language->$defualt->heading }}</h2>
                                <p>{{ @$about->value->language->$defualt->sub_heading }}</p>
                            </div>
                        </div>
                        <div class="about-feature-area">
                            <ul class="feature-list">
                                @if (isset($about->value->items))
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($about->value->items ?? [] as $key => $item)
                                        <li>{{ $item->language->$defualt->title }}</li>
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="feature-statistics-wrapper">
                            <div class="row mb-30-none">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 mb-30">
                                    <div class="statistics-item">
                                        <div class="statistics-content">
                                            <div class="odo-area">
                                                <h3 class="odo-title odometer odometer-auto-theme"
                                                    data-odometer-final="{{ formatNumberInKNotationValue(@$about->value->language->$default_lng->experience) }}">
                                                </h3>
                                                <h3 class="title">
                                                    {{ formatNumberInKNotationUnit(@$about->value->language->$default_lng->experience) }}
                                                </h3>
                                            </div>
                                            <p>{{ __('Years Of Experience') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 mb-30">
                                    <div class="statistics-item">
                                        <div class="statistics-content">
                                            <div class="odo-area">
                                                <h3 class="odo-title odometer odometer-auto-theme"
                                                    data-odometer-final="{{ formatNumberInKNotationValue(@$about->value->language->$default_lng->feedback) }}">
                                                </h3>
                                                <h3 class="title">
                                                    {{ formatNumberInKNotationUnit(@$about->value->language->$default_lng->feedback) }}
                                                </h3>
                                            </div>
                                            <p>{{ __('Feedback') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 mb-30">
                                    <div class="statistics-item">
                                        <div class="statistics-content">
                                            <div class="odo-area">
                                                <h3 class="odo-title odometer odometer-auto-theme"
                                                    data-odometer-final="{{ formatNumberInKNotationValue(@$about->value->language->$default_lng->contributors) }}">
                                                </h3>
                                                <h3 class="title">
                                                    {{ formatNumberInKNotationUnit(@$about->value->language->$default_lng->contributors) }}
                                                </h3>
                                            </div>
                                            <p>{{ __('Contributors') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 mb-30">
                    <div class="row">
                        <div class="col-6">
                            <div class="about-img">
                                <img src="{{ get_image($about->value->images->image_one, 'site-section') }}"
                                    alt="Image">
                            </div>
                            <div class="about-img">
                                <img src="{{ get_image($about->value->images->image_two, 'site-section') }}"
                                    alt="Image">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="about-img">
                                <img src="{{ get_image($about->value->images->image_three, 'site-section') }}"
                                    alt="Image">
                            </div>
                            <div class="about-img">
                                <img src="{{ get_image($about->value->images->image_four, 'site-section') }}"
                                    alt="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End about section
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push('script')
@endpush
