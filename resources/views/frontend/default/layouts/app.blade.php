<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title') | {{ setting('site_title') }}</title>
    <meta name="keywords" content="@yield('meta_keywords')">
    <meta name="description" content="@yield('meta_description')">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(setting('site_favicon')) }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/fontawesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/atom-one-dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
</head>

<body>

    <!--[if lte IE 9]>
   <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
   <![endif]-->

    <!-- Back to top start -->
    <div class="back-to-top-wrap">
        <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- Back to top end -->

    <!-- Header section start -->
    <header class="header-section">
        <div class="header-area header-style-one py-3" id="header-sticky">
            <div class="container">
                <div class="header-inner">
                    <div class="header-left">
                        <div class="header-logo">
                            <a href="#">
                                <img src="{{ asset(setting('site_logo')) }}" alt="{{ setting('site_title') }}">
                            </a>
                        </div>
                    </div>
                    <div class="header-middle"></div>
                    <div class="header-right">
                        <div class="header-quick-actions d-flex align-items-center">

                            <div class="header-btns-wrap">
                                <a class="td-btn primary-btn btn-h-40" href="mailto:{{ setting('support_email') }}">
                                    <span class="btn-text">{{ __('Email Us') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header section end -->

    <!-- Body main wrapper start -->
    <main>

        <!-- Breadcrumb section start -->
        <div class="td-breadcrumb-area p-relative zi-11">
            <div class="container">
                <div class="row gy-50 justify-content-center">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                        <div class="breadcrumb-contents text-center">
                            <h1 class="banner-title">@yield('title')</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="breadcrumb-bg" data-background="{{ asset('frontend/images/bg/breadcrumb.png') }}"></div>
        </div>
        <!-- Banner section end -->

        @yield('content')

    </main>
    <!-- Body main wrapper end -->


    <!-- Footer area start -->
    <footer>
        <div class="td-footer-section">
            <div class="footer-copyright-area">
                <div class="container">
                    <div class="footer-copyright">
                        <div class="copyright-text">
                            <p class="description">&copy; {{ date('Y') }} {{ setting('site_title') }}. All rights
                                reserved.</p>
                        </div>
                        <div class="footer-terms">
                            <a href="{{ url('/terms-conditions') }}">{{ __('Terms & Conditions') }}</a> |
                            <a href="{{ url('/privacy-policy') }}"> {{ __('Privacy Policy') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer area end -->

    <!-- JS here -->
    <script src="{{ asset('frontend/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/js/select2.js') }}"></script>
    <script src="{{ asset('frontend/js/highlight.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
    @stack('script')
</body>

</html>
