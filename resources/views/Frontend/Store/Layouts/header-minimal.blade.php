<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/custom.css') }}" rel="stylesheet">
</head>
<body>
<header class="store-header store-header-minimal">
    <div class="store-header-inner">
        <div class="store-header-top">
            <div class="store-avatar">{{ mb_substr($shop->name ?? 'ف', 0, 1) }}</div>
            <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
            </a>
        </div>
    </div>
</header>
