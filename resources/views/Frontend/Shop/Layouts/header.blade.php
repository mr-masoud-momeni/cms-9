<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>قالب بوت استرپ فارسی</title>

    <!-- بوت‌استرپ RTL -->
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css')}}" rel="stylesheet">
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
                <span class="badge badge-success"></span><a href="{{route('order.index')}}" class="shop-ico"><i class="fa fa-shopping-cart fa-2x" id="cart-val" value={{$orderNumber}}></i></a>

            </div>

        </div>
    </div>
</header>
