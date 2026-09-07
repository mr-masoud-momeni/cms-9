<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $shop->name ?? 'فروشگاه' }}</title>

    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap-rtl.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/vendor/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('/fonts/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('/frontend/css/custom.css') }}" rel="stylesheet">
    <script src="{{ asset('/frontend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/frontend/js/jquery-3-5-0.js') }}"></script>
</head>
<body>
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>

<header class="shop-topbar">
    <div class="shop-topbar-inner container">
        <a href="{{ url('/') }}" class="shop-home" aria-label="صفحه اصلی فروشگاه">
            <i class="bi bi-house"></i>
        </a>

        <div class="shop-topbar-actions">
            @auth('buyer')
                <a href="{{ route('buyer.orders.completed') }}" class="shop-topbar-link">
                    <i class="bi bi-receipt"></i>
                    <span>سفارش‌ها</span>
                </a>
            @else
                <a href="{{ route('buyer.login') }}" class="shop-topbar-link">
                    <i class="bi bi-person"></i>
                    <span>ورود</span>
                </a>
            @endauth

            <a href="{{ route('order.index') }}" class="shop-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
                @if(($orderNumber ?? 0) > 0)
                    <span class="shop-cart-badge">{{ $orderNumber }}</span>
                @endif
            </a>
        </div>
    </div>
</header>

<style>
.shop-topbar{position:sticky;top:0;z-index:1000;background:rgba(255,255,255,.94);border-bottom:1px solid #eee;backdrop-filter:blur(10px)}
.shop-topbar-inner{height:58px;display:flex;align-items:center;justify-content:space-between;padding:0 16px}
.shop-home,.shop-topbar-link,.shop-cart{color:#222;text-decoration:none}
.shop-home{font-size:19px}
.shop-topbar-actions{display:flex;align-items:center;gap:20px}
.shop-topbar-link{display:flex;align-items:center;gap:6px;font-size:13px}
.shop-topbar-link i{font-size:18px}
.shop-cart{position:relative;font-size:21px;display:flex;align-items:center}
.shop-cart-badge{position:absolute;top:-8px;right:-9px;min-width:17px;height:17px;padding:0 4px;border-radius:10px;background:#111;color:#fff;font-size:10px;line-height:17px;text-align:center}
@media(max-width:575px){.shop-topbar-link span{display:none}.shop-topbar-actions{gap:17px}.shop-topbar-inner{padding:0 14px}}
</style>
