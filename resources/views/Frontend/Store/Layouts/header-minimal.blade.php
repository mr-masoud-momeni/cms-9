<header class="store-header store-header-minimal">
    <div class="store-header-inner">
        <div class="store-header-top">
            <div class="store-header-info">
                <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
                @if(!empty($shop->description))
                    <p class="store-description">{{ $shop->description }}</p>
                @endif
            </div>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
                <span id="cart-val" class="store-cart-badge">{{ $orderNumber ?? 0 }}</span>
            </a>
        </div>
    </div>
</header>
