<a href="{{ route('front.product.show', $product) }}" class="shop-product">
    <div class="shop-product-image">
        @if(!empty($product->images['thum']))
            <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" loading="lazy">
        @else
            <div class="shop-product-placeholder"><i class="bi bi-image"></i></div>
        @endif
    </div>
    <div class="shop-product-info">
        <h2>{{ $product->title }}</h2>
        <span>{{ number_format((float) $product->price) }} تومان</span>
    </div>
</a>
