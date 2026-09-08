@extends('Frontend.Store.Layouts.Master')

@section('Main')
<div class="instagram-store">
    <div class="store-tabs" role="tablist">
        <button class="store-tab active" type="button" data-tab="products" role="tab" aria-selected="true">
            <i class="bi bi-grid-3x3"></i>
            محصولات
        </button>
        <button class="store-tab" type="button" data-tab="posts" role="tab" aria-selected="false">
            <i class="bi bi-file-text"></i>
            پست‌ها
        </button>
    </div>

    <section class="store-tab-content active" data-content="products">
        @if($products->count())
            <div class="shop-products">
                @foreach($products as $product)
                    @include('Frontend.Store.Partials.product-card', ['product' => $product])
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="shop-pagination">{{ $products->links('vendor.pagination.bootstrap-4') }}</div>
            @endif
        @else
            <div class="shop-empty">
                <i class="bi bi-bag"></i>
                <h2>هنوز محصولی اضافه نشده</h2>
                <p>محصولات این فروشگاه به‌زودی اینجا نمایش داده می‌شوند.</p>
            </div>
        @endif
    </section>

    <section class="store-tab-content" data-content="posts">
        @if($articles->count())
            <div class="shop-posts">
                @foreach($articles as $article)
                    @include('Frontend.Store.Partials.article-card', ['article' => $article])
                @endforeach
            </div>
        @else
            <div class="shop-empty">
                <i class="bi bi-file-text"></i>
                <h2>هنوز پستی منتشر نشده</h2>
                <p>پست‌های این فروشگاه به‌زودی اینجا نمایش داده می‌شوند.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.store-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var target = this.dataset.tab;

            document.querySelectorAll('.store-tab').forEach(function (item) {
                item.classList.toggle('active', item === tab);
                item.setAttribute('aria-selected', item === tab ? 'true' : 'false');
            });

            document.querySelectorAll('.store-tab-content').forEach(function (content) {
                content.classList.toggle('active', content.dataset.content === target);
            });
        });
    });
});
</script>
@endsection
