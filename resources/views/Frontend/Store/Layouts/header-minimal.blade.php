<header class="store-header store-header-minimal">
    <div class="store-header-inner">
        <div class="store-header-top">
            <div class="store-avatar">{{ mb_substr($shop->name ?? 'ف', 0, 1) }}</div>
            <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
                <span id="cart-val" class="store-cart-badge">{{ $orderNumber ?? 0 }}</span>
            </a>
        </div>
    </div>
</header>
