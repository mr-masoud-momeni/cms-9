<div class="shop-profile container">
    <div class="shop-avatar">{{ mb_substr($shop->name ?? 'ش', 0, 1) }}</div>
    <div class="shop-profile-info">
        <h1>{{ $shop->name ?? 'فروشگاه' }}</h1>
        <p>محصولات این فروشگاه را ببینید و آنلاین سفارش دهید.</p>
        <div class="shop-profile-meta"><strong>{{ $products->total() }}</strong> محصول</div>
    </div>
</div>
