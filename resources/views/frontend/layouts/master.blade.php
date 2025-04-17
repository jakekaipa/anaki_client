@php
    $cookie_accepted = session()->get('cookie_accepted');
    $cookie_decline = session()->get('cookie_decline');
@endphp
<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $basic_settings->sitename(__($page_title??'')) }}</title>
    
    @include('partials.header-asset')
    @vite('resources/js/app.js')

    @stack('css')
</head>
<body class="{{ selectedLangDir() ?? "ltr"}}">
@include('frontend.partials.preloader')
@include('frontend.partials.scroll-to-top')
@include('frontend.partials.header')

@yield("content")



@include('frontend.partials.footer')
@include('partials.footer-asset')
@include('admin.partials.notify')
@include('frontend.partials.extensions.tawk-to')
@stack('script')

<script>

    $(document).ready(function () {
        $(".language-select").change(function(){
            var submitForm = `<form action="{{ setRoute('languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
            $("body").append(submitForm);
            $("#local_submit").submit();
        });
    });

    //*************** Newsletter Form Submit Start ******************
    // $(document).on('submit', '#newslatter-form', function(e){
    //     e.preventDefault();
    //     $.ajax({
    //         type: "POST",
    //         url: "{{ setRoute('subscriber') }}",
    //         data: new FormData(this),
    //         dataType: "json",
    //         contentType: false,
    //         processData: false,
    //         cache: false,
    //         beforeSend: function(){
    //             $('#newslatter-form .button-icon').addClass('d-none');
    //             $('#newslatter-form .fa-spinner').removeClass('d-none');
    //         },
    //         complete: function(){
    //             $('#newslatter-form .button-icon').removeClass('d-none');
    //             $('#newslatter-form .fa-spinner').addClass('d-none');
    //             $('#newslatter-form .newsletter-btn').attr('disabled', false);
    //         },
    //         success: function (data) {
    //             $('#newslatter-form')[0].reset();
    //             throwMessage('success',data.message.success);
    //         },
    //         error: function(xhr, ajaxOption, thrownError){
    //             var errorObj = JSON.parse(xhr.responseText);
    //             throwMessage(errorObj.type,errorObj.message.error.errors);
    //         },
    //     });
    // });

    // //*************** Newsletter Form Submit End ******************
    // //*************** Contact Form Submit Start ******************
    // $(document).on('submit', '#contact-form', function(e){
    //     e.preventDefault();
    //     $.ajax({
    //         type: "POST",
    //         url: "{{ setRoute('contact.store') }}",
    //         data: new FormData(this),
    //         dataType: "json",
    //         contentType: false,
    //         processData: false,
    //         cache: false,
    //         beforeSend: function(){
    //             $('#contact-form .button-icon').addClass('d-none');
    //             $('#contact-form .fa-spinner').removeClass('d-none');
    //         },
    //         complete: function(){
    //             $('#contact-form .button-icon').removeClass('d-none');
    //             $('#contact-form .fa-spinner').addClass('d-none');
    //             $('#contact-form .contact-btn').attr('disabled', false);
    //         },
    //         success: function (data) {
    //             $('#contact-form')[0].reset();
    //             throwMessage('success',data.message.success);
    //         },
    //         error: function(xhr, ajaxOption, thrownError){
    //             var errorObj = JSON.parse(xhr.responseText);
    //             throwMessage(errorObj.type,errorObj.message.error.errors);
    //         },
    //     });
    // });
    //*************** Contact Form Submit End ******************
</script>

<script>
    // var status = "{{ @$site_cookie->status }}";
    // var cookie_accepted = "{{ @$cookie_accepted }}";
    // var cookie_decline = "{{ @$cookie_decline }}";
    // const pop = document.querySelector('.cookie-main-wrapper')
    // if (status == 1) {
    //     if (cookie_accepted == true || cookie_decline == true) {
    //         pop.style.bottom = "-300px";
    //     } else {
    //         window.onload = function() {
    //             setTimeout(function() {
    //                 pop.style.bottom = "0";
    //             }, 2000)
    //         }
    //     }
    // } else {
    //     pop.style.bottom = "-300px";
    // }
</script>
<script>
    (function($) {
        "use strict";
        $('.cookie-btn').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.get('{{ url('cookie/accept') }}', function(response) {
                throwMessage('success', [response]);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
        $('.cookie-btn-cross').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.get('{{ url('cookie/decline') }}', function(response) {
                throwMessage('error', [response]);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
    })(jQuery)
</script>
</body>
</html>
