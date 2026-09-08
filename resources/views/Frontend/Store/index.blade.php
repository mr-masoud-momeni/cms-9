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
<style>
.instagram-store{width:100%;background:#fff;min-height:60vh;padding-bottom:60px}
.store-tabs{max-width:980px;margin:0 auto;border-top:1px solid #eee;border-bottom:1px solid #eee;display:flex;justify-content:center;gap:55px}
.store-tab{position:relative;border:0;background:transparent;padding:15px 8px 13px;color:#777;font-size:13px;cursor:pointer}
.store-tab i{margin-left:5px}
.store-tab.active{color:#171717;font-weight:700}
.store-tab.active:after{content:"";position:absolute;right:0;left:0;bottom:-1px;height:2px;background:#171717}
.store-tab-content{display:none}
.store-tab-content.active{display:block}
.shop-products,.shop-posts{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:3px;padding-top:3px}
.shop-product,.store-post{display:block;color:inherit;text-decoration:none;overflow:hidden}
.shop-product-image,.store-post-image{width:100%;aspect-ratio:1/1;background:#f5f5f5;overflow:hidden}
.shop-product-image img,.store-post-image img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .25s ease}
.shop-product:hover .shop-product-image img,.store-post:hover .store-post-image img{transform:scale(1.025)}
.shop-product-info,.store-post-info{padding:9px 5px 15px}
.shop-product-info h2,.store-post-info h2{margin:0;font-size:13px;font-weight:500;color:#222;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shop-product-info span{display:block;margin-top:4px;font-size:12px;color:#666}
.shop-product-placeholder,.store-post-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:34px}
.shop-empty{max-width:900px;margin:0 auto;text-align:center;padding:90px 20px;color:#777}
.shop-empty i{font-size:40px;display:block;margin-bottom:14px}.shop-empty h2{font-size:18px;color:#333;margin:0 0 8px}.shop-empty p{font-size:13px;margin:0}
.shop-pagination{padding:28px 15px;display:flex;justify-content:center}
@media(max-width:767px){.store-tabs{gap:38px}.shop-products,.shop-posts{grid-template-columns:repeat(2,minmax(0,1fr));gap:2px}.shop-product-info,.store-post-info{padding:7px 4px 12px}.shop-product-info h2,.store-post-info h2{font-size:12px}.shop-product-info span{font-size:11px}}
</style>
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
