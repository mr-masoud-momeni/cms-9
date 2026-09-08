@extends('Frontend.Store.Layouts.Master')

@section('Main')
<div class="instagram-shop">
    @include('Frontend.Store.Partials.shop-profile')

    <div class="shop-products container">
        @forelse($products as $product)
            @include('Frontend.Store.Partials.product-card', ['product' => $product])
        @empty
            <div class="shop-empty">
                <i class="bi bi-bag"></i>
                <h2>هنوز محصولی اضافه نشده</h2>
                <p>محصولات این فروشگاه به‌زودی اینجا نمایش داده می‌شوند.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="shop-pagination container">{{ $products->links('vendor.pagination.bootstrap-4') }}</div>
    @endif
</div>
@endsection

@section('scripts')
<style>
.instagram-shop{min-height:100vh;background:#fff;padding:28px 0 90px}.shop-profile{display:flex;align-items:center;gap:28px;max-width:900px;padding:12px 20px 36px}.shop-avatar{flex:0 0 110px;width:110px;height:110px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#f1f1f1;border:1px solid #ddd;font-size:42px;font-weight:600;color:#222}.shop-profile-info h1{margin:0 0 8px;font-size:25px;font-weight:600;color:#111}.shop-profile-info p{margin:0 0 12px;color:#666;font-size:14px}.shop-profile-meta{color:#222;font-size:14px}.shop-products{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:3px;max-width:1100px;padding:0}.shop-product{display:block;color:inherit;text-decoration:none;overflow:hidden}.shop-product-image{width:100%;aspect-ratio:1/1;background:#f5f5f5;overflow:hidden}.shop-product-image img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .25s ease}.shop-product:hover .shop-product-image img{transform:scale(1.025)}.shop-product-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:36px}.shop-product-info{padding:10px 4px 18px}.shop-product-info h2{margin:0 0 5px;font-size:14px;font-weight:500;color:#222;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.shop-product-info span{font-size:13px;color:#666}.shop-empty{grid-column:1/-1;text-align:center;padding:90px 20px;color:#777}.shop-empty i{font-size:42px;display:block;margin-bottom:15px}.shop-empty h2{font-size:18px;color:#333;margin-bottom:8px}.shop-empty p{font-size:14px}.shop-pagination{padding-top:28px;display:flex;justify-content:center}@media(max-width:767px){.instagram-shop{padding-top:16px}.shop-profile{gap:18px;padding:12px 16px 25px}.shop-avatar{flex-basis:82px;width:82px;height:82px;font-size:31px}.shop-profile-info h1{font-size:20px}.shop-profile-info p{font-size:13px;line-height:1.7}.shop-products{grid-template-columns:repeat(2,minmax(0,1fr))}.shop-product-info{padding:8px 5px 14px}}
</style>
@endsection
