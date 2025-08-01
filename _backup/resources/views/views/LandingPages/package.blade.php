@extends('appLandingPage')

@section('content')
    <!-- ? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/loder.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
    @include('LandingPages.landingHeader')
    <!-- header end -->
    <main>
        <!--? Pricing Card Start -->
        <section class="pricing-card-area section-padding2">
            <div class="container">
                <!-- Section Tittle -->
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10 col-sm-10">
                        <div class="section-tittle text-center mb-100">
                            <p>Our pricing plan for you</p>
                            <h2>No hidden charges! choose  your plan wisely.</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
                        <div class="single-card text-center mb-30">
                            <div class="card-top">
                                <p>Single Package</p>
                                <h4>Only Broadband connection</h4>
                            </div>
                            <div class="card-mid">
                                <h4>$20.00 <span>/ mo</span></h4>
                            </div>
                            <div class="card-bottom">
                                <ul>
                                    <li>Line Rental Included</li>
                                    <li>12 Month Contract</li>
                                    <li>No Activation Charges</li>
                                    <li>Up to 12Mbps average Speed</li>
                                    <li>Enjoy family on weekends</li>
                                </ul>
                                <a href="#" class="borders-btn">View Packages</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
                        <div class="single-card text-center mb-30">
                            <div class="card-top">
                                <p>Half Package</p>
                                <h4>Broadband & TV connection</h4>
                            </div>
                            <div class="card-mid">
                                <h4>$25.00 <span>/ mo</span></h4>
                            </div>
                            <div class="card-bottom">
                                <ul>
                                    <li>Line Rental Included</li>
                                    <li>12 Month Contract</li>
                                    <li>No Activation Charges</li>
                                    <li>Up to 12Mbps average Speed</li>
                                    <li>Enjoy family on weekends</li>
                                </ul>
                                <a href="#" class="borders-btn">View Packages</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-10">
                        <div class="single-card text-center mb-30">
                            <div class="card-top">
                                <p>Full  Package</p>
                                <h4>TV, Broadband And Phone</h4>
                            </div>
                            <div class="card-mid">
                                <h4>$29.00 <span>/ mo</span></h4>
                            </div>
                            <div class="card-bottom">
                                <ul>
                                    <li>Line Rental Included</li>
                                    <li>12 Month Contract</li>
                                    <li>No Activation Charges</li>
                                    <li>Up to 12Mbps average Speed</li>
                                    <li>Enjoy family on weekends</li>
                                </ul>
                                <a href="#" class="borders-btn">View Packages</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Pricing Card End -->
        <!-- Latest Offers Start -->
        <div class="latest-wrapper pb-bottom">
            <div class="container">
                <div class="latest-area latest-height  section-bg2" data-background="assets/img/gallery/section_bg01.png">
                    <div class="row  align-items-center justify-content-between">
                        <div class="col-xl-5 col-lg-7">
                            <div class="latest-caption">
                                <h2>Check our unbelievable super  fast Broadband availability in your area.</h2>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-10 ">
                            <div class="latest-subscribe">
                                <form action="#">
                                    <input type="email" placeholder="Enter Zipcode">
                                    <button>Check Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Latest Offers End -->
    </main>
     @include('LandingPages.landingFooter')

    <!-- Scroll Up -->
    <div id="back-top" >
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>

    @endsection
