<?php
    $app_settings = App\Models\Admin\AppSettings::first();
?>
<section class="app-section ptb-100 bg_img" data-background="{{ asset('public/frontend/') }}/images/app/app-bg.jpg">
    <div class="container">
        <div class="app-wrapper">
            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-8">
                    <div class="section-header text-center">
                        <h2 class="section-title">{{ $download->value->language->$defualt->heading_two }}</h2>
                        <p>{{ $download->value->language->$defualt->sub_heading_two }}</p>
                    </div>
                    <ul class="action-btn-list">
                        <li>
                            <a href="{{ @$app_settings->android_url }}">
                                <i class="fab fa-apple"></i>
                                <div class="download-text">
                                    <small>{{ __('Download form') }}</small>
                                    <h5>{{ __('App Store') }}</h5>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ @$app_settings->iso_url }}">
                                <i class="fab fa-google-play"></i>
                                <div class="download-text">
                                    <small>{{ __('Download form') }}</small>
                                    <h5>{{ __('Google Play') }}</h5>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
