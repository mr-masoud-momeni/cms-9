<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', $shop->name ?? 'فروشگاه')
    </title>


    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css')}}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/custom.css') }}" rel="stylesheet">
    <script src="{{asset('/frontend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('/frontend/js/jquery-3-5-0.js')}}"></script>

    {{-- CSS مشترک تمام صفحات Store --}}
    <link rel="stylesheet" href="{{ asset('/frontend/css/store.css') }}">

    {{-- CSS مشترک هدر --}}
    <link rel="stylesheet" href="{{ asset('/frontend/css/store-header.css') }}">

    {{-- CSS اختصاصی صفحه --}}
    @yield('page-styles')

    {{-- امکان اضافه کردن CSS دلخواه از خود صفحه --}}
    @stack('styles')

    @yield('head')
</head>

<body class="store-page">

    {{-- Header --}}
    @yield('header')

    {{-- Main Content --}}
    <main class="store-main">
        @yield('Main')
    </main>
    <div
        id="toastContainer"
        class="toast-container position-fixed top-0 end-0 p-3"
        style="z-index: 99999;"
    ></div>
    {{-- Scripts --}}
    @yield('scripts')

    @stack('scripts')

</body>
</html>