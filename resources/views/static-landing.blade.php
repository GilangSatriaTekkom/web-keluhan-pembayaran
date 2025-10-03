<!DOCTYPE html>
<html class="no-js" lang="zxx">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Internet Service</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="manifest" href="site.webmanifest" />
    <link
      rel="shortcut icon"
      type="image/x-icon"
      href="assets/img/favicon.ico"
    />

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/owl.carousel.min.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/slicknav.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/flaticon.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/progressbar_barfiller.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/gijgo.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/animate.min.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/animated-headline.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/magnific-popup.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/fontawesome-all.min.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/themify-icons.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/slick.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/nice-select.css" />
    <link rel="stylesheet" href="{{ secure_asset('assets') }}/css/landingpage/style.css" />
  </head>
  <body>
    <!-- ? Preloader Start -->
    <div id="preloader-active">
      <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
          <div class="preloader-circle"></div>
          <div class="preloader-img pere-text">
            <img src="assets/img/logo/Logo untuk website.png" alt="SuperNet Logo" />
          </div>
        </div>
      </div>
    </div>
    <!-- Preloader Start -->
    <header>
      <!-- Header Start -->
      <div class="header-area header-transparent">
        <div class="main-header">
          <div class="header-top d-none d-lg-block">
            <div class="container-fluid">
              <div class="col-xl-12">
                <div class="row d-flex justify-content-between align-items-center">
                  <!-- Info kontak bisa ditambahkan di sini -->
                </div>
              </div>
            </div>
          </div>
          <div class="header-bottom header-sticky">
            <div class="container-fluid">
              <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-xl-2 col-lg-2">
                  <div class="logo">
                    <a href="index.html">
                      <img style="width: 136px; height: auto" src="assets/img/logo/Logo untuk website.png" alt="SuperNet Logo" />
                    </a>
                  </div>
                </div>
                <div class="col-xl-10 col-lg-10">
                  <div class="menu-wrapper d-flex align-items-center justify-content-end">
                    <!-- Main-menu -->
                    <div class="main-menu d-none d-lg-block">
                      <nav>
                        <ul id="navigation">
                          <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                          {{-- <li><a href="about.html">Tentang Kami</a></li> --}}
                          <li><a href="{{ route('login') }}">Login</a></li>
                          <li><a href="{{ route('contact') }}">Kontak</a></li>
                        </ul>
                      </nav>
                    </div>
                    <!-- Header-btn -->
                    <div class="header-right-btn d-none d-lg-block ml-30">
                      <a href="{{ route('login') }}" class="btn header-btn">Mulai Sekarang</a>
                    </div>
                  </div>
                </div>
                <!-- Mobile Menu -->
                <div class="col-12">
                  <div class="mobile_menu d-block d-lg-none"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Header End -->
    </header>
    <!-- header end -->
    <main>
      <!-- slider Area Start-->
      <div class="slider-area">
        <div class="slider-active">
          <!-- Single Slider -->
          <div class="single-slider slider-height d-flex align-items-center">
            <div class="container">
              <div class="row">
                <div class="col-xl-7 col-lg-6 col-md-8 col-sm-10">
                  <div class="hero__caption">
                    <h1 data-animation="fadeInUp" data-delay=".4s">
                      Jangan biarkan buffering mengganggu, dapatkan internet super.
                    </h1>
                    <p data-animation="fadeInUp" data-delay=".6s">
                      Nikmati pengalaman internet terbaik dengan koneksi stabil dan kecepatan tinggi untuk semua kebutuhan digital Anda.
                    </p>
                    <!-- Hero-btn -->
                    <div class="hero__btn">
                      <a href="{{ route('contact') }}" class="btn hero-btn mb-10" data-animation="fadeInUp" data-delay=".8s">
                        Pesan Sekarang
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Single Slider -->
          <div class="single-slider slider-height d-flex align-items-center">
            <div class="container">
              <div class="row">
                <div class="col-xl-7 col-lg-6 col-md-8 col-sm-10">
                  <div class="hero__caption">
                    <h1 data-animation="fadeInUp" data-delay=".4s">
                      Internet Cepat untuk Semua Kebutuhan Anda
                    </h1>
                    <p data-animation="fadeInUp" data-delay=".6s">
                      Dari bekerja dari rumah hingga streaming film favorit, kami hadir dengan solusi internet terpercaya.
                    </p>
                    <!-- Hero-btn -->
                    <div class="hero__btn">
                      <a href="{{ route('contact') }}" class="btn hero-btn mb-10" data-animation="fadeInUp" data-delay=".8s">
                        Pesan Sekarang
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- slider Area End-->

      <!--? service Area Start -->
      <section class="service-area pb-bottom">
        <div class="container">
          <div class="row justify-content-between">
            <div class="col-lg-6 col-md-9">
              <!-- Section Tittle -->
              <div class="section-tittle section-tittle2 mb-50">
                <h2 class="mb-35">
                  Misi kami adalah menghadirkan kekuatan internet untuk setiap bisnis dan rumah tangga.
                </h2>
                <p>
                  Dengan teknologi terkini dan infrastruktur yang handal, kami berkomitmen memberikan layanan internet terbaik yang mendukung produktivitas dan hiburan Anda.
                </p>
                <a href="{{ route('contact') }}" class="btn mt-30">Pesan Sekarang</a>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="row">
                <div class="col-lg-6 col-md-4 col-sm-6">
                  <div class="single-services mb-30 text-center">
                    <i class="flaticon-null"></i>
                    <p>Cakupan Luas</p>
                  </div>
                </div>
                <div class="col-lg-6 col-md-4 col-sm-6">
                  <div class="single-services mb-30 text-center">
                    <i class="flaticon-null-1"></i>
                    <p>Dukungan 24/7</p>
                  </div>
                </div>
                <div class="col-lg-6 col-md-4 col-sm-6">
                  <div class="single-services mb-30 text-center">
                    <i class="flaticon-null-2"></i>
                    <p>Pembayaran Aman</p>
                  </div>
                </div>
                <div class="col-lg-6 col-md-4 col-sm-6">
                  <div class="single-services mb-30 text-center">
                    <i class="flaticon-null-3"></i>
                    <p>Kecepatan hingga 100 Mbps</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- service Area End -->

      <!--? Pricing Card Start -->
      <section class="pricing-card-area section-padding2">
        <div class="container">
          <!-- Section Tittle -->
          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-10">
              <div class="section-tittle text-center mb-100">
                <p>Paket harga kami untuk Anda</p>
                <h2>Tidak ada biaya tersembunyi! Pilih paket yang tepat.</h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 10 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 150.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">
                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 20 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 250.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">
                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 30 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 350.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">
                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 50 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 600.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">
                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 75 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 580.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">

                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
              <div class="single-card text-center mb-30">
                <div class="card-top">
                  <h4>Paket 100 Mbps</h4>
                </div>
                <div class="card-mid">
                  <h4>Rp 600.000 <span>/ bulan</span></h4>
                </div>
                <div class="card-bottom">
                  <a href="{{ route('contact') }}" class="borders-btn">Pesan Sekarang</a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>
      <!-- Pricing Card End -->

      <!--? About-2 Area Start -->
      <section class="about-area2 testimonial-area section-padding30 fix">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 col-md-9 col-sm-9">
              <div class="about-caption">
                <!-- Section Tittle -->
                <div class="section-tittle mb-55">
                  <h2>Apa kata pelanggan tentang kami!</h2>
                </div>
                <!-- Testimonial Start -->
                <div class="h1-testimonial-active">
                  <!-- Single Testimonial -->
                  <div class="single-testimonial">
                    <div class="testimonial-caption">
                      <p>
                        Layanan internet dari SuperNet sangat stabil dan cepat. Saya tidak pernah mengalami masalah buffering saat meeting online atau streaming film. Tim supportnya juga sangat responsif.
                      </p>
                      <div class="rattiong-caption">
                        <span>Budi Santoso<span>Pemilik Bisnis Kecil</span> </span>
                      </div>
                    </div>
                  </div>
                  <!-- Single Testimonial -->
                  <div class="single-testimonial">
                    <div class="testimonial-caption">
                      <p>
                        Sejak beralih ke SuperNet, produktivitas kerja dari rumah meningkat signifikan. Koneksi yang stabil memungkinkan saya bekerja tanpa gangguan. Harga juga sangat kompetitif.
                      </p>
                      <div class="rattiong-caption">
                        <span>Sari Dewi<span>Freelancer</span> </span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Testimonial End -->
              </div>
            </div>
            <div class="col-lg-5 col-md-11 col-sm-11">
              <!-- about-img -->
              <div class="about-img2">
                <img src="assets/img/gallery/about2.png" alt="Testimoni Pelanggan" />
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- About-2 Area End -->

      <!--? Blog Area Start -->
      {{-- <section class="home-blog-area section-padding30">
        <div class="container">
          <!-- Section Tittle -->
          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-10">
              <div class="section-tittle text-center mb-90">
                <span>Artikel Terbaru</span>
                <h2>Tips dan informasi terbaru seputar internet</h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="home-blog-single mb-30">
                <div class="blog-img-cap">
                  <div class="blog-img">
                    <img src="assets/img/gallery/home-blog1.png" alt="Tips Memilih Internet Rumah" />
                  </div>
                  <div class="blog-cap">
                    <h3>
                      <a href="blog_details.html">5 Tips Memilih Provider Internet Rumah Terbaik</a>
                    </h3>
                    <p>22 Januari 2023</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="home-blog-single mb-30">
                <div class="blog-img-cap">
                  <div class="blog-img">
                    <img src="assets/img/gallery/home-blog2.png" alt="Keamanan Jaringan" />
                  </div>
                  <div class="blog-cap">
                    <h3>
                      <a href="blog_details.html">Cara Meningkatkan Keamanan Jaringan Rumah Anda</a>
                    </h3>
                    <p>15 Januari 2023</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="home-blog-single mb-30">
                <div class="blog-img-cap">
                  <div class="blog-img">
                    <img src="assets/img/gallery/home-blog3.png" alt="Internet untuk Bisnis" />
                  </div>
                  <div class="blog-cap">
                    <h3>
                      <a href="blog_details.html">Mengoptimalkan Internet untuk Kebutuhan Bisnis</a>
                    </h3>
                    <p>8 Januari 2023</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> --}}
      <!-- Blog Area End -->
    </main>

    <footer>
      <!--? Footer Start-->
      <div class="footer-area section-bg" data-background="assets/img/gallery/footer_bg.png">
        <div class="container">
          <!-- Brand Area Start -->
          <div class="brand-area pt-25 pb-30">
            <div class="container">
              <div class="brand-active brand-border pt-50 pb-40">
                <div class="single-brand">
                  <img src="assets/img/gallery/brand1.png" alt="Partner 1" />
                </div>
                <div class="single-brand">
                  <img src="assets/img/gallery/brand2.png" alt="Partner 2" />
                </div>
                <div class="single-brand">
                  <img src="assets/img/gallery/brand3.png" alt="Partner 3" />
                </div>
                <div class="single-brand">
                  <img src="assets/img/gallery/brand4.png" alt="Partner 4" />
                </div>
              </div>
            </div>
          </div>
          <!-- Brand Area End -->
          <div class="footer-top footer-padding">
            <div class="row d-flex justify-content-between">
              <div class="col-xl-3 col-lg-4 col-md-5 col-sm-8">
                <div class="single-footer-caption mb-50">
                  <!-- logo -->
                  <div class="footer-logo">
                    <a href="index.html">
                      <img style="width: 136px; height: auto" src="assets/img/logo/Logo untuk website.png" alt="SuperNet Logo" />
                    </a>
                  </div>
                  <div class="footer-tittle">
                    <div class="footer-pera">
                      <p class="info1">
                        Dapatkan update dan berita terbaru langsung dari SuperNet.
                      </p>
                    </div>
                  </div>
                  <div class="footer-number">
                    <h4><span>+62 </span>89609875689</h4>
                  </div>
                </div>
              </div>
              <div class="col-xl-2 col-lg-2 col-md-3 col-sm-5">
                <div class="single-footer-caption mb-50">
                  <div class="footer-tittle">
                    <h4>Tautan Cepat</h4>
                    <ul>
                      <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                          {{-- <li><a href="package.html">Paket Layanan</a></li> --}}
                          <li><a href="{{ route('contact') }}">Kontak</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              {{-- <div class="col-xl-4 col-lg-4 col-md-6 col-sm-8">
                <div class="single-footer-caption mb-50">
                  <div class="footer-tittle">
                    <h4>Newsletter</h4>
                    <div class="footer-pera">
                      <p class="info1">Berlangganan sekarang untuk mendapatkan update harian</p>
                    </div>
                  </div>
                  <!-- Form -->
                  <div class="footer-form">
                    <div id="mc_embed_signup">
                      <form action="#" method="post" class="subscribe_form relative mail_part">
                        <input type="email" name="EMAIL" id="newsletter-form-email" placeholder="Alamat Email" class="placeholder hide-on-focus" />
                        <div class="form-icon">
                          <button type="submit" name="submit" id="newsletter-submit" class="email_icon newsletter-submit button-contactForm">
                            Berlangganan
                          </button>
                        </div>
                        <div class="mt-10 info"></div>
                      </form>
                    </div>
                  </div>
                </div>
              </div> --}}
            </div>
          </div>
          <div class="footer-bottom">
            <div class="row d-flex justify-content-between align-items-center">
              <div class="col-xl-9 col-lg-8">
                <div class="footer-copy-right">
                  <p>
                    &copy; <script>document.write(new Date().getFullYear());</script> SuperNet. All rights reserved.
                  </p>
                </div>
              </div>
              {{-- <div class="col-xl-3 col-lg-4">
                <!-- Footer Social -->
                <div class="footer-social f-right">
                  <a href="#"><i class="fab fa-twitter"></i></a>
                  <a href="#"><i class="fab fa-facebook-f"></i></a>
                  <a href="#"><i class="fab fa-instagram"></i></a>
                  <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
      <!-- Footer End-->
    </footer>

    <!-- Scroll Up -->
    {{-- <div id="back-top">
      <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div> --}}

    <!-- JS here -->
    <!-- Semua script JavaScript tetap sama seperti sebelumnya -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/vendor/jquery-1.12.4.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/popper.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/owl.carousel.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/slick.min.js"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/wow.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/animated.headline.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.magnific-popup.js"></script>

    <!-- Date Picker -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/gijgo.min.js"></script>
    <!-- Nice-select, sticky -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.nice-select.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.sticky.js"></script>
    <!-- Progress -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.barfiller.js"></script>

    <!-- counter , waypoint,Hover Direction -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.counterup.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/waypoints.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.countdown.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/hover-direction-snake.min.js"></script>

    <!-- contact js -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/contact.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.form.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.validate.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/mail-script.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="{{ secure_asset('assets') }}/js/landingpage/plugins.js"></script>
    <script src="{{ secure_asset('assets') }}/js/landingpage/main.js"></script>
</body>
</html>
