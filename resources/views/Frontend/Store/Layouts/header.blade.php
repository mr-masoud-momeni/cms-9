<header class="store-header">
    <div class="store-header-inner">
        <div class="store-header-top">
            <a href="{{ route('index.show') }}" class="store-home-link" aria-label="صفحه اصلی فروشگاه">
                <div class="store-avatar">
                    @if(!empty($shop->logo))
                        <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name ?? 'فروشگاه' }}">
                    @else
                        {{ mb_substr($shop->name ?? 'ف', 0, 1) }}
                    @endif
                </div>
                <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
            </a>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
                <span id="cart-val" class="store-cart-badge">{{ $orderCount ?? 0 }}</span>
            </a>
        </div>
        @if($showStats ?? false)
        <div class="store-stats">
            <span><strong>{{ $postCount }}</strong> پست</span>
            <span><strong>{{ $productCount }}</strong> محصول</span>
        </div>
        @endif
        <p class="store-description">{!! nl2br(e($shop->description ?? 'محصولات و مطالب این فروشگاه را اینجا ببینید.')) !!}</p>
    </div>
</header>
