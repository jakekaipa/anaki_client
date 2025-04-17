<!-- favicon -->
<link rel="shortcut icon" href="{{ get_fav($basic_settings) }}" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<!-- line-awesome-icon css -->
{{-- <link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/line-awesome.css">
<!-- fontawesome-icon css -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/fontawesome-all.css">
<!-- bootstrap css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/bootstrap.css">
<!-- swipper css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/swiper.css">
<!-- animate css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/animate.css">
<!-- odometer css link -->
<link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/odometer.css">
<!-- Magnific popup css link -->
<link rel="stylesheet" href="{{ asset('public/backend/css/select2.css') }}"> --}}

{{-- <link rel="stylesheet" href="{{ asset('public/backend/library/popup/magnific-popup.css') }}"> --}}
<!-- nice-select css link -->
{{-- <link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/nice-select.css"> --}}
<!-- lightcase css link -->
{{-- <link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/lightcase.css"> --}}
<!-- Fileholder CSS CDN -->
{{-- <link rel="stylesheet" href="https://cdn.appdevs.net/fileholder/v1.0/css/fileholder-style.css" type="text/css"> --}}
<!-- style css link -->
{{-- <link rel="stylesheet" href="{{ asset('public/frontend/') }}/css/style.css"> --}}

@php
    $color = @$basic_settings->base_color ?? '#5fffca';
@endphp

<style>
    :root {
        --primary-color: {{ $basic_settings->base_color }};
    }

    .preloader .preloader-title:before {
        content: "{{ $basic_settings->site_name }}";
        font-size: 18px;
        -webkit-animation: preloader-title-anim 0.3s 0.6s ease-out forwards;
                animation: preloader-title-anim 0.3s 0.6s ease-out forwards;
    }
</style>
