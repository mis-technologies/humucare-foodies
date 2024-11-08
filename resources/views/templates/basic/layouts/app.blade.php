<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @include('partials.seo')

    @stack('og')
    <title> {{ $general->sitename(__($pageTitle)) }}</title>

    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/main.css') }}">
    <link rel="shortcut icon" href="{{ getImage(imagePath()['logoIcon']['path'] . '/favicon.png', '?' . time()) }}"
        type="image/x-icon">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}">

    @stack('style-lib')

    @stack('style')

    <style>
        .cmn--btnn {
            background-color: #000000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Hover effect */
        .cmn--btnn:hover {
            background-color: #000000;
            /* Darker blue on hover */
            color: #000000;
            /* Change text color on hover */
        }

        /* Optional: active/focused state */
        .cmn--btnn:active,
        .cmn--btnn:focus {
            outline: none;
            box-shadow: 0 0 5px #0056b3;
        }

        .forMilliliters {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: opacity 0.5s ease, max-height 0.5s ease;
        }

        .forMilliliters.show {
            opacity: 1;
            max-height: 200px;
        }

        .forLiters {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: opacity 0.5s ease, max-height 0.5s ease;
        }

        .forLiters.show {
            opacity: 1;
            max-height: 200px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #F3E521;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #F3E521;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
</head>

<body class="overflow-hidden">

    <div class="preloader">
        <div class="loader-bg">
            <div class="loader-inner">
                <span></span>
            </div>
        </div>
    </div>

    @stack('fbComment')

    <div class="overlay"></div>

    @yield('app')

    @php
    $cookie = App\Models\Frontend::where('data_keys','cookie.data')->first();
    @endphp
    @if(@$cookie->data_values->status && !session('cookie_accepted'))
    <div class="cookies-card bg--light text-center cookies--dark radius--10px">
        <div class="cookies-card__icon">
            <i class="fas fa-cookie-bite"></i>
        </div>
        <p class="mt-4 cookies-card__content text--dark"> @php echo @$cookie->data_values->description @endphp
            <a class="d-inline text--base" href="{{ @$cookie->data_values->link }}">@lang('Read Policy')</a>
        </p>
        <div class="cookies-card__btn mt-4">
            <button class="cookies-btn btn--base w-100" id="allow-cookie">@lang('Allow')</button>
        </div>
    </div>
    @endif

    <a href="javascript:void(0)" class="scrollToTop"><i class="las la-angle-double-up"></i></a>

    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/rafcounter.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/lightbox.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/owl.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/main.js') }}"></script>

    @stack('script-lib')

    @stack('script')

    @include('partials.plugins')

    @include('partials.notify')

    <script>
        (function ($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{route('home')}}/change/"+$(this).val() ;
            });
            var url = `{{ route('cookie.accept') }}`;
            $('#allow-cookie').on('click', function(){
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function (response) {
                        $('.cookies-card').hide();
                    }
                });
            });
            if($(window).width() < 991)
            {
                $('.category-link .cate-icon').each(function(i, obj) {
                    $(this).children(":first").attr('href','javascript: void(0)');
                });
            }
        })(jQuery);


    </script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6728dc904304e3196adce530/1ibrpr5me';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
    <!--End of Tawk.to Script-->
</body>

</html>
