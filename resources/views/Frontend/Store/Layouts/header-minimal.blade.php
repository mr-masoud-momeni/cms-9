<header class="store-header store-header-minimal">
    <div class="store-header-inner">
        <div class="store-header-top">
            <div class="store-avatar">
                @if(!empty($shop->logo))
                    <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name ?? 'فروشگاه' }}">
                @else
                    {{ mb_substr($shop->name ?? 'ف', 0, 1) }}
                @endif
            </div>
            <div class="store-header-info">
                <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
                @if(!empty($shop->description))
                    <p class="store-description">{!! nl2br(e($shop->description)) !!}</p>
                @endif
            </div>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
                <span id="cart-val" class="store-cart-badge">{{ $orderNumber ?? 0 }}</span>
            </a>
        </div>
    </div>
</header>
