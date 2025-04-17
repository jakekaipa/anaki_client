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
                <div class="row mb-30-none">
                    @foreach($journals as $key => $item)
                        <div class="col-lg-12 mb-30">
                            <div class="blog-item">
                                <div class="blog-item-thumb">
                                    <img src="{{ get_image($item->image, 'web-jornal') }}" alt="blog">
                                </div>
                                <div class="blog-item-content">
                                    <h4 class="title"><a href="{{ setRoute('web-journal.details', [$item->id,$item->slug]) }}">{{ @$item->title->language->$defualt->title }}</a></h4>
                                    <p>{!! Str::limit(@$item->details->language->$defualt->details, 200, '...') !!}</p>
                                    <div class="blog-item-bottom-content">
                                        <ul class="blog-item-list">
                                            <li><i class="las la-user"></i> {{ $item->admin->fullName }}</li>
                                            <li><i class="las la-clock"></i> {{ dateFormat('d F, Y', $item->created_at) }}</li>
                                        </ul>
                                        <div class="blog-item-btn-area">
                                            <a href="{{ setRoute('web-journal.details', [$item->id, $item->slug]) }}">{{ __('Read More') }} <i class="las la-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-12 mb-30">
                <div class="blog-sidebar">
                    @include('frontend.components.web-journal-aside')
                    <div class="widget-box">
                        <h4 class="widget-title">{{ __('Tags') }}</h4>
                        <div class="tag-widget-box">
                            <ul class="tag-list">
                                @foreach ($journals as $tag_data)
                                    @isset($tag_data->tags->language->$defualt->tags)
                                        @foreach ($tag_data->tags->language->$defualt->tags as $tag)
                                            <li><a href="#0">{{ $tag }}</a></li>
                                        @endforeach
                                    @endisset
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav>
            {{ $journals->links() }}
        </nav>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Blog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

@endsection


@push("script")

@endpush
