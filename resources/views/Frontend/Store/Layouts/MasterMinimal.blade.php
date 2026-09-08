<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name ?? 'فروشگاه' }}</title>
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/custom.css') }}" rel="stylesheet">
    <script src="{{ asset('/frontend/js/jquery-3-5-0.js') }}"></script>
    <script src="{{ asset('/frontend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</head>
<body>
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>

@include('Frontend.Store.Layouts.header-minimal')

@yield('Main')
@yield('scripts')

</body>
</html>
