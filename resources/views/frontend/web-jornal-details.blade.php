@extends('frontend.layouts.master')

@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
@endphp
@section('content')
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="blog-section ptb-120">
    <div class="container">
        <div class="row mb-30">
            <div class="col-xl-8 col-lg-7 col-md-12 mb-30">
                <div class="row justify-content-center mb-30-none">
                    <div class="col-xl-12 mb-30">
                        <div class="blog-item-details">
                            <div class="blog-thumb">
                                <img src="{{ get_image($journal->image, 'web-jornal') }}" alt="blog">
                            </div>
                            <div class="blog-content">
                                <h3 class="title">{{ $journal->title->language->$defualt->title }}</h3>
                                <p>{!! $journal->details->language->$defualt->details !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $journal_details = (array) $journal;
            @endphp
            <div class="col-xl-4 col-lg-5 col-md-12 mb-30">
                <div class="blog-sidebar">
                    @include('frontend.components.web-journal-aside')
                    <div class="widget-box">
                        <h4 class="widget-title">Tags</h4>
                        <div class="tag-widget-box">
                            <ul class="tag-list">
                                @isset($journal->tags->language->$defualt->tags)
                                    @foreach ($journal->tags->language->$defualt->tags as $tag)
                                        <li><a href="#0">{{ $tag }}</a></li>
                                    @endforeach
                                @endisset
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection


@push("script")

@endpush
