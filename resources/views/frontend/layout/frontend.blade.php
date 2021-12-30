<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />
  <title>{{ $title ?? "Home" }} | Hi Hello</title>
  <link rel="manifest" href="{{ asset('frontend/images/manifest.json') }}">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  @stack('extra-css')
</head>

<body @stack('body-tag')>
  <header id="myheader">
    <div class="container-fluid">
        @include('flash::message')
      <nav class="navbar navbar-expand-md cust-navbar">
        <a class="navbar-brand" href="{{ route('home') }}"><img src="{{ asset('frontend/images/logo.png') }}" alt="{{ str_slug(config('app.name')) }}" class="logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="nav-link" rel='m_PageScroll2id' href="{{ route('home') }}">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" rel='m_PageScroll2id' href="{{ route('home') }}#how-it-works">How it Works</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" rel='m_PageScroll2id' href="{{ route('home') }}#features">Features</a>
            </li>
        </div>
      </nav>
    </div>
  </header>
  <!--******************* Header Section End *********************-->
  <!--******************* Banner Section Start ******************-->

    {{-- Banner Section --}}
        @stack('banner-section')
    
    {{-- Main Section --}}
    <main> @yield('main-content') </main>

    {{-- Footer --}}
        <footer>
            <article class="container">
              <div class="footer-wrap">
                <div class="row justify-content-between align-items-baseline">
                  <div class="col-lg-4 col-md-5 col-sm-7">
                    <div class="footer-left-box">
                      <a class="" href="{{ route('home') }}"><img src="{{ asset('frontend/images/footer-logo.svg') }}" alt="logo"></a>
                      <p>Hi Hello Dating App For Bharat.</p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-3 col-sm-4">
                    <div class="footer-middle-box">
                      <h5>Helpful Links</h5>
                      <ul>
                        <li><a href="{{ route('terms') }}">Terms & Condition</a></li>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('about.us') }}">About Us</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-4">
                    <div class="footer-right-box">
                      <h5>Get In Touch</h5>
                      <ul class="social-list d-flex">
                        <li><a href="#" target=”_blank”><i class="fab fa-google"></i></a></li>
                        <li><a href="#" target=”_blank”><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#" target=”_blank”><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#" target=”_blank”><i class="fab fa-linkedin-in"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="copyright-box text-center">
                <p>© {{ \Carbon\Carbon::today()->format('Y') }} <a href="{{ route('home') }}">Hi Hello</a> All Rights Reserved</p>
              </div>
            </article>
        </footer>
    <div id="data-sticky-offset"></div>
</body>
  
  <link href="{{ asset('frontend/css/all.min.css') }}" type="text/css" rel="stylesheet" />
  <link href="{{ asset('frontend/css/bootstra') }}p.css" type="text/css"  rel="stylesheet">
  <link href="{{ asset('frontend/css/style.css') }}" type="text/css" rel="stylesheet" />
  <script src="{{ asset('frontend/js/lib/jquery.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('frontend/js/bootstrap.js') }}" type="text/javascript"></script>
  {{-- <script src="{{ asset('frontend/js/jquery.malihu.PageScroll2id.min.js') }}" type="text/javascript"></script> --}}
  <script src="{{ asset('frontend/js/all.min.js') }}" type="text/javascript"></script>

    <script>
      $(document).ready(function () {
        var width = $(window).width();    
        window.onscroll = function () { myFunction() };

        var header = document.getElementById("myheader");
        var sticky = header.offsetTop;

        function myFunction() {
          if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
          } else {
            header.classList.remove("sticky");
          }
        }
      })
      $(document).ready(function () {
        $('.navbar-nav .nav-link').click(function () {
          $('.navbar-collapse').removeClass('show')
        })
        // $("a[rel='m_PageScroll2id']").mPageScroll2id({
        //   offset: $("#data-sticky-offset")
        // });
      })
    </script>
    <script>
      $('div.alert').not('.alert-important').delay(3000).fadeOut(450);
    </script>
    @stack('extra-js')
</html>