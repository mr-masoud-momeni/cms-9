@extends('Frontend.Shop.layouts.Master')

@section('Main')
<section class="content container my-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">محصولات</h1>
            <p class="text-muted mb-0">جدیدترین محصولات فروشگاه</p>
        </div>
        <div class="text-muted small">
            {{ $products->total() }} محصول
        </div>
    </div>

    @if($products->count())
        <div class="row g-3 g-md-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('front.product.show', $product) }}" class="text-decoration-none text-dark d-block h-100">
                        <article class="card h-100 border-0 shadow-sm overflow-hidden">
                            <div class="ratio ratio-1x1 bg-light">
                                @if(!empty($product->images['thum']))
                                    <img src="{{ asset($product->images['thum']) }}" class="card-img-top object-fit-cover" alt="{{ $product->title }}" loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center text-muted small">تصویری موجود نیست</div>
                                @endif
                            </div>
                            <div class="card-body p-3 d-flex flex-column">
                                <h2 class="h6 card-title mb-3">{{ $product->title }}</h2>
                                <div class="mt-auto fw-bold text-primary">
                                    {{ number_format((float) $product->price) }}
                                </div>
                            </div>
                        </article>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $products->links('vendor.pagination.bootstrap-4') }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3 fs-1">🛍️</div>
            <h2 class="h5">هنوز محصولی ثبت نشده است</h2>
            <p class="text-muted mb-0">به‌زودی محصولات این فروشگاه نمایش داده می‌شوند.</p>
        </div>
    @endif
</section>
@endsection

@section('scripts')
@endsection
