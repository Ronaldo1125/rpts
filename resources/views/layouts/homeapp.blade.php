<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>DEPDev 5 - Regional Project Tracking System</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="/assets/img/favicon/favicon.ico" rel="icon">
  <link href="/assets/onepage/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="/assets/onepage/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/onepage/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/onepage/vendor/aos/aos.css" rel="stylesheet">
  <link href="/assets/onepage/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/assets/onepage/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="/assets/onepage/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: OnePage
  * Template URL: https://bootstrapmade.com/onepage-multipurpose-bootstrap-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ url("/") }}" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="/assets/onepage/img/DEPDev_Logo.png" alt="">
        <h4 class="sitename">Regional Project Tracking System</h4>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url("/") }}" 
            @if(document_path() == '')
             class="active" 
             @endif
             >Home<br></a></li>
          <li><a href="https://dro5.depdev.gov.ph" target="_blank">DEPDev 5 Website</a></li>
          <li><a href="{{ route('projectDashboard.index')}}" 
            @if(document_path() == 'projectDashboard')
             class="active"
             @endif
             >Project Dashboard</a>
           </li>
          <li><a href="{{ route('about.index')}}" 
            @if(document_path() == 'about')
                class="active"
            @endif
            >About RDIP</a></li>
          <li><a href="{{ route('cipg_submissions.index') }}"  @if(document_path() == 'cipgSubmission')
                class="active"
            @endif>CIPG Submission</a></li>
          {{-- <li><a href="#team">Team</a></li>
          <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>
          <li><a href="#contact">Contact</a></li> --}}
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{ route('login') }}">Login</a>

    </div>
  </header>

  <main class="main">

    @yield('content')

  </main>

  <footer id="footer" class="footer light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="{{url("/")}}" class="logo d-flex align-items-center">
            <span class="sitename">DEPDev 5-RPTS</span>
          </a>
          <p>The Regional Project Tracking System (RPTS) is a system database containing the 
                       priority programs, activities, and projects (PAPs) of regional line agencies, 
                       government owned and controlled corporations, and state universities and colleges in the 
                       Bicol region that are included in the Regional Development Investment Program (RDIP) 2023-2028. 
                       It is being developed to facilitate the tracking and updating of PAPs and can generate reports 
                       as well as investment programming-related documents such as the RDIP, status of RDC-endorsed 
                       projects, and list of projects per province/district/city/municipality, among others.</p>
          {{-- <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div> --}}
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="{{ url("/") }}" 
            @if(document_path() == '')
             class="active" 
             @endif
             >Home</a></li>
            <li><a href="https://dro5.depdev.gov.ph">DEPDev 5 Website</a></li>
            <li><a href="{{ route('projectDashboard.index')}}" 
            @if(document_path() == 'projectDashboard')
             class="active"
             @endif
             >Project Dashboard</a></li>
            <li><a href="{{ route('about.index')}}" 
            @if(document_path() == 'about')
                class="active"
            @endif
            >About RDIP</a></li>
            <li><a href="{{ route('cipg_submissions.index') }}">CIPG Submission</a></li>
            <li><a href="{{ route('login')}}">Login</a></li>
          </ul>
        </div>

        {{-- <div class="col-lg-2 col-6 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Contact Us</h4>
          <p>A108 Adam Street</p>
          <p>New York, NY 535022</p>
          <p>United States</p>
          <p class="mt-4"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
          <p><strong>Email:</strong> <span>info@example.com</span></p>
        </div> --}}

      </div>
    </div>
    @php
      $currentYear = now()->year;
    @endphp

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> {{ $currentYear }} <strong class="px-1 sitename">DEPDev 5-RPTS</strong> <span>Information Technology Unit, All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="/assets/onepage/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/onepage/vendor/php-email-form/validate.js"></script>
  <script src="/assets/onepage/vendor/aos/aos.js"></script>
  <script src="/assets/onepage/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/assets/onepage/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/assets/onepage/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="/assets/onepage/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="/assets/onepage/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="/assets/onepage/js/main.js"></script>

</body>

</html>