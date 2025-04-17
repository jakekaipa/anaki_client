<?php
    $defualt = get_default_language_code()??'en';
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
    $type = Illuminate\Support\Str::slug(App\Constants\GlobalConst::USEFUL_LINKS);
    $useful_links = App\Models\Admin\SetupPage::where('type',$type)->where('status', 1)->get();
    $download_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::DOWNLOAD_SECTION);
    $download = App\Models\Admin\SiteSections::getData($download_slug)->first();
?>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start app
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@include('frontend.components.download-app',compact('download', 'defualt'))
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End app
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Scroll-To-Top
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<a href="#" class="scrollToTop">
    <i class="las la-angle-up"></i>
    <small>top</small>
</a>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Scroll-To-Top
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start footer section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<footer class="footer-section">
    <div class="container">
        <div class="footer-wrapper">
            <!-- <ul class="footer-list">
                @foreach ($useful_links as $item)
                    <li><a href="{{route('page.view',$item->slug)}}">{{ @$item->title->language->$defualt->title }} <i class="las la-arrow-right"></i></a></li>
                @endforeach
            </ul> -->
            <!-- <div class="footer-social-area">
                <h4 class="social-title">{{ __('Follow Us') }} :</h4>
                <ul class="social-list">
                    @if(isset($footer->value->items))
                        @foreach($footer->value->items ?? [] as $key => $item)
                            <li><a href="{{ @$item->language->$defualt->link }}" target="_blank"><i class=" {{ @$item->language->$defualt->social_icon }}"></i></a></li>
                        @endforeach
                    @endif
                </ul>
            </div> -->
            <div class="row justify-content-center">
                <div class="col-lg-3">
                    <div class="lang-select">
                        @php
                            $session_lan = session('local')??get_default_language_code();
                        @endphp
                        <select name="lang_switch" class="form--control language-select nice-select" id="language-select">
                            @foreach($__languages as $item)
                            <option value="{{$item->code}}" @if( $session_lan == $item->code) selected  @endif>{{ __($item->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="footer-copyright-area">
                <span>
                    {!! @$footer->value->language->$defualt->footer_text !!}
            </div>
        </div>
    </div>
</footer>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End footer section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Subscribe
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="subscribe-section pt-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="subscribe-area">
                    <!-- <div class="subscribe-content">
                        <h2 class="title">{{ @$footer->value->language->$defualt->newsltter_details }}</h2>
                    </div>
                    <form class="subscribe-form" method="POST" id="newslatter-form">
                        @csrf
                        <input type="email" name="email" required class="form--control" placeholder="Enter Your Email...">
                        <button type="submit" class="newsletter-btn"><i class="las la-angle-right button-icon"></i> <i class="fa fa-spinner d-none fa-pulse fa-fw"></i></button>
                    </form> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Subscribe
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

