@php
    $defualt = get_default_language_code()??'en';
    $contact_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::CONTACT_SECTION);
    $contact = App\Models\Admin\SiteSections::getData($contact_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
    $type = App\Constants\GlobalConst::SETUP_PAGE;
    $menues = DB::table('setup_pages')
            ->where('status', 1)
            ->where('type', Str::slug($type))
            ->get();
@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<header class="header-section">
    <div class="header">
        <div class="header-top-area">
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-xxl-9 col-xl-9">
                        <div class="header-top-wrapper">
                            <ul class="header-top-list">
                                @if(isset($footer->value->items))
                                    @foreach($footer->value->items ?? [] as $key => $item)
                                        <li><a href="{{ @$item->language->$defualt->link }}" target="_blank"><i class=" {{ @$item->language->$defualt->social_icon }}"></i></a></li>
                                    @endforeach
                                @endif
                            </ul>
                            <div class="info-wrapper">
                                <span>{{ @$contact->value->language->$defualt->office_hours }}</span>
                            </div>
                            <!-- <div class="info-wrapper">
                                <span><b>{{ __('Call Us') }} :</b> {{ @$contact->value->language->$defualt->phone }} <i class="las la-phone-volume"></i></span>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title" href="{{ setRoute('index') }}">
                            <img src="{{ get_logo($basic_settings) }}"  data-white_img="{{ get_logo($basic_settings,'white') }}"
                            data-dark_img="{{ get_logo($basic_settings,'dark') }}"
                                alt="site-logo">
                        </a>
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto me-auto me">
                                @php
                                    $current_url = URL::current();
                                @endphp
                                @foreach ($menues as $item)
                                    @php
                                        $title = json_decode($item->title);
                                    @endphp
                                    <li><a href="{{ url($item->url) }}" class="@if ($current_url == url($item->url)) active @endif">{{ __($title->title) }} <i class="fas fa-arrow-right"></i></a></li>
                                @endforeach
                            </ul>
                            <div class="header-action">
                                @auth
                                    <a href="{{ setRoute('user.dashboard') }}" class="btn--base"><i class="fas fa-th-large me-1"></i>{{ __('Dashboard') }}</a>
                                @else
                                    <a href="{{ setRoute('user.login') }}" class="btn--base"><i class="fas fa-user-circle me-1"></i>{{ __('Login Now') }}</a>
                                @endauth
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
