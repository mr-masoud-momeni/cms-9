<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>قالب بوت استرپ فارسی</title>

    <!-- بوت‌استرپ RTL -->
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap-icons.css')}}" rel="stylesheet">
    <script src="{{asset('/frontend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <link href="{{asset('/fonts/font-awesome.min.css')}}" rel="stylesheet" type="text/css" media="all">
    <link href="{{asset('/frontend/css/custom.css')}}" rel="stylesheet">
    <script src="{{asset('/frontend/js/jquery-3-5-0.js')}}"></script>
</head>
<body>
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>
<!-- هدر -->
<header class="bg-light py-3 shadow">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <img src="logo.png" alt="لوگو" height="40">
            </div>
            <div>

                <a href="{{route('order.index')}}" class="shop-ico"><i class="bi bi-cart-fill" style="font-size: 1.5rem;" id="cart-val" value={{$orderNumber}}></i></a>

                @auth('buyer')
                    <span style="margin-left: 10px;">{{ __('ui.hello')}} {{ auth('buyer')->user()->name }} {{ __('ui.dear')}}</span>
                    <a href="{{route('buyer.logout')}}" >{{ __('ui.logout')}}</a>
                @else
                    <a href="{{route('buyer.login.path')}}" >{{ __('ui.login')}}</a> / <a href="{{route('buyer.show.register')}}" >{{ __('ui.membership')}}</a>
                @endauth
            </div>

        </div>
    </div>
</header>
