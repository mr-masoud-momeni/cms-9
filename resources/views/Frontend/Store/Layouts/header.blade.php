<header class="store-header">
    <div class="store-header-inner">
        <div class="store-header-top">
            <div class="store-avatar">
                @if(!empty($shop->logo))
                    <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name ?? 'فروشگاه' }}">
                @else
                    {{ mb_substr($shop->name ?? 'ف', 0, 1) }}
                @endif
            </div>
            <h1 class="store-title">{{ $shop->name ?? 'فروشگاه' }}</h1>
            <a href="{{ route('buyer.order.index') }}" class="store-cart" aria-label="سبد خرید">
                <i class="bi bi-bag"></i>
            </a>
        </div>
        <div class="store-stats">
            <span><strong>{{ $postCount }}</strong> پست</span>
            <span><strong>{{ $productCount }}</strong> محصول</span>
        </div>
        <p class="store-description">{!! nl2br(e($shop->description ?? 'محصولات و مطالب این فروشگاه را اینجا ببینید.')) !!}</p>
    </div>
</header>
