<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,400,500,600,700" rel="stylesheet">
    {{--<link href="{{asset('/frontend/css/front.css')}}" rel="stylesheet" type="text/css" media="all">--}}
    {{--<link href="{{asset('/frontend/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">--}}
<!-- Vendor CSS Files -->
    <link href="{{asset('/frontend/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
    <link href="{{asset('/frontend/vendor/custom.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

    <!-- Template Main CSS File -->
    <link href="{{asset('/frontend/css/style.css')}}" rel="stylesheet">
    <!-- =======================================================
    * Template Name: Rapid - v4.7.1
    * Template URL: https://bootstrapmade.com/rapid-multipurpose-bootstrap-business-template/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>
<!-- ======= Header ======= -->
<header id="header" class="fixed-top d-flex align-items-center header-transparent">
    <div class="container d-flex align-items-center">

        <h1 class="logo logo-align"><a href="index.html">Rapid</a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

        <nav id="navbar" class="navbar order-last order-lg-0">
            <ul>
                @php
                    if(isset($menu->content)){
                        $arrays = json_decode($menu->content , TRUE);
                    }
                @endphp
                @if(isset($arrays))
                    {{menu_navigation($arrays)}}
                @endif
                {{--<li><a class="nav-link scrollto active" href="#hero">Home</a></li>--}}
                {{--<li><a class="nav-link scrollto" href="#about">About</a></li>--}}
                {{--<li><a class="nav-link scrollto" href="#services">Services</a></li>--}}
                {{--<li><a class="nav-link scrollto " href="#portfolio">Portfolio</a></li>--}}
                {{--<li><a class="nav-link scrollto" href="#team">Team</a></li>--}}
                {{--<li class="dropdown"><a href="#"><span>Drop Down</span> <i class="bi bi-chevron-down"></i></a>--}}
                {{--<ul>--}}
                {{--<li><a href="#">Drop Down 1</a></li>--}}
                {{--<li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-right"></i></a>--}}
                {{--<ul>--}}
                {{--<li><a href="#">Deep Drop Down 1</a></li>--}}
                {{--<li><a href="#">Deep Drop Down 2</a></li>--}}
                {{--<li><a href="#">Deep Drop Down 3</a></li>--}}
                {{--<li><a href="#">Deep Drop Down 4</a></li>--}}
                {{--<li><a href="#">Deep Drop Down 5</a></li>--}}
                {{--</ul>--}}
                {{--</li>--}}
                {{--<li><a href="#">Drop Down 2</a></li>--}}
                {{--<li><a href="#">Drop Down 3</a></li>--}}
                {{--<li><a href="#">Drop Down 4</a></li>--}}
                {{--</ul>--}}
                {{--</li>--}}
                {{--<li><a class="nav-link scrollto" href="#footer">Contact</a></li>--}}
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->
        <div class="social-links">
{{--developer comment: the orderNumber parameter share with view composer from a provider called "ShareDataServiceProvider".--}}
            <span class="badge badge-success">Success</span><a href="{{route('order.index')}}" class="shop-ico"><i class="bi bi-cart-fill" id="cart-val" value={{$orderNumber}}></i></a>
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
        </div>

    </div>
</header><!-- End Header -->
