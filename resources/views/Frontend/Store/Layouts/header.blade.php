<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('/frontend/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        .store-header{background:#fff;border-bottom:1px solid #eee;padding:22px 0 18px}
        .store-header-inner{max-width:980px;margin:0 auto;padding:0 18px}
        .store-header-top{display:flex;align-items:center;gap:16px}
        .store-avatar{width:78px;height:78px;min-width:78px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#f3f3f3;border:1px solid #ddd;font-size:28px;font-weight:700;color:#222}
        .store-title{font-size:21px;font-weight:700;color:#171717;margin:0}
        .store-cart{margin-right:auto;width:42px;height:42px;border:1px solid #e5e5e5;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#222;text-decoration:none;font-size:20px}
        .store-stats{display:flex;gap:30px;margin:17px 0 10px;padding-right:94px;color:#222;font-size:14px}
        .store-stats strong{font-weight:700;margin-left:4px}
        .store-description{padding-right:94px;color:#666;font-size:13px;line-height:1.9;margin:0;max-width:700px}
        @media(max-width:767px){.store-header{padding:16px 0}.store-avatar{width:66px;height:66px;min-width:66px;font-size:24px}.store-title{font-size:18px}.store-stats{padding-right:82px;gap:22px;margin-top:14px}.store-description{padding-right:0;margin-top:10px}.store-cart{width:38px;height:38px;font-size:18px}}
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

        <div class="store-stats">
            <span><strong>{{ $postCount }}</strong> پست</span>
            <span><strong>{{ $productCount }}</strong> محصول</span>
        </div>

        <p class="store-description">{{ $shop->description ?? 'محصولات و مطالب این فروشگاه را اینجا ببینید.' }}</p>
    </div>
</header>
