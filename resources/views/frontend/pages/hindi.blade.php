<!DOCTYPE html>
<html>
    <head>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />
        <title>Hi Hello Dating App</title>
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('frontend/images/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('frontend/images/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('frontend/images/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/images/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('frontend/images/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('frontend/images/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('frontend/images/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('frontend/images/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontend/images/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('frontend/images/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('frontend/images/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend/images/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('frontend/js/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <!--Font Awesome Included-->
        <link href="{{ asset('frontend/css/all.min.css') }}" type="text/css" rel="stylesheet" />
        <!-- Bootstrap -->
        <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Slick css included -->
        <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/slick.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/slick-theme.css') }}"/>
        <!--Main Style Included-->
        <link href="{{ asset('frontend/css/style.css') }}" type="text/css" rel="stylesheet" />
        <!--Extra Style Included-->
        <!--Main Js Included-->
        <script src="{{ asset('frontend/js/lib/jquery-3.6.0.js') }}" type="text/javascript"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
        <!--Font Awesome Included-->
        <script src="{{ asset('frontend/js/all.min.js') }}"></script>
        
    </head>
    <body>
        <section class="banner-landing-page">
            <div class="banner-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="banner-left-box">
                                <a href="{{ route('home') }}" class="logo"><img src="{{ asset('frontend/images/logo.png') }}" alt="logo"></a>
                                <div class="banner-txt-wrap">
                                    <h1 class="text-uppercase">
                                        हम आ रहे हैं
                                        <span> जल्द ही !! </span>
                                    </h1>
                                    <h4>हम <span>iOS</span> और <span>Android</span> दोनों प्लेटफॉर्म पर आने वाले हैं</h4>
                                </div>
                                <div class="social-box d-none d-lg-flex align-items-center">
                                    <h4>हमारा अनुसरण इस पर कीजिये: </h4>
                                    <ul class="social-list d-flex ">
                                        <li><a href="{{ $instagram }}" target=”_blank”><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="{{ $facebook }}" target=”_blank”><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="{{ $twitter }}" target=”_blank”><i class="fab fa-twitter"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mobile-slider-wrapper">
                                <div class="landing-page-slider">
                                    <div>
                                            <img src="{{ asset('frontend/images/boy-mobile-hin.png') }}" alt="app-image">
                                    </div>
                                    <div>
                                            <img src="{{ asset('frontend/images/girl-mobile-hin.png') }}" alt="app-image">
                                    </div>
                                    <div>
                                            <img src="{{ asset('frontend/images/boy-mobile-hin.png') }}" alt="app-image">
                                    </div>
                                    <div>
                                            <img src="{{ asset('frontend/images/girl-mobile-hin.png') }}" alt="app-image">
                                    </div>
                                </div>
                            </div>
                            <div class="social-box d-flex d-lg-none justify-content-center align-items-center">
                                <h4>हमारा अनुसरण इस पर कीजिये: </h4>
                                <ul class="social-list d-flex ">
                                    <li><a href="{{ $instagram }}" target=”_blank”><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="{{ $facebook }}" target=”_blank”><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="{{ $twitter }}" target=”_blank”><i class="fab fa-twitter"></i></a></li>
                                </ul>
                            </div>
                            <!-- mobile-slider-wrapper ends -->
                        </div>
                       
                    </div>
                    <!-- row ends -->
                    <div class="bg-wrapper d-flex">
                        <div class="main-image"></div>
                        <div class="gradient"></div>
                    </div>

                </div>
                <!-- container fluid ends -->
                
            </div>
        </section>
        <div class="footer-section">
            <div class="container">
                <div class="d-flex justify-content-between">
                    <div class="copyright-box">
                        <p>{{ $footer_text }}</p>
                    </div>
                    <div class="footer-links">
                        <ul>
                            <li><a href="{{ route('terms') }}">{{ __("Terms & Conditions") }}</a></li>
                            <li><a href="{{ route('privacy.policy') }}">{{ __("Privacy Policy") }}</a></li>
                            <li><a href="{{ route('about.us') }}">{{ __("About Us") }}</a></li>
                            <li><a href="{{ route('community.safety') }}">{{ __("Community & Safety Guidelines") }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- slick js included -->
        <script type="text/javascript" src="{{ asset('frontend/js/slick.min.js') }}"></script>
        <script>
                $('.landing-page-slider').slick({
                dots: false,
                arrows: false,
                infinite: false,
                fade: true,
                autoplay: true,
                autoplaySpeed: 3000,
                slidesToShow: 1,
                slidesToScroll: 1
                });
        </script>
    </body>
</html>