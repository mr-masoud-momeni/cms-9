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
    <style>
        .store-header{background:#fff;border-bottom:1px solid #eee;padding:14px 0}
        .store-header-inner{max-width:980px;margin:0 auto;padding:0 18px}
        .store-header-top{display:flex;align-items:center;gap:12px}
        .store-avatar{width:48px;height:48px;min-width:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#f3f3f3;border:1px solid #ddd;font-size:20px;font-weight:700;color:#222}
        .store-title{font-size:18px;font-weight:700;color:#171717;margin:0}
        .store-cart{margin-right:auto;width:40px;height:40px;border:1px solid #e5e5e5;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#222;text-decoration:none;font-size:19px}
        @media(max-width:767px){.store-header{padding:11px 0}.store-avatar{width:42px;height:42px;min-width:42px;font-size:18px}.store-title{font-size:16px}.store-cart{width:38px;height:38px;font-size:18px}}
    </style>
</head>
<body>
<header class="store-header">
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
