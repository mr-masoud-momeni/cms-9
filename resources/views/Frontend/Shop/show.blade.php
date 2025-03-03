@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- صفحه محصول -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- تصویر محصول -->
            <div class="col-md-6 text-center">
                <img src="{{ asset($product->images['thum'])}}" alt="محصول" class="product-image">
            </div>

            <!-- جزئیات محصول -->
            <div class="col-md-6">
                <h2 class="fw-bold">{{ $product->title}}</h2>
                <p class="text-muted">توضیح کوتاه درباره محصول. این بخش شامل مشخصات و ویژگی‌های محصول می‌شود.</p>
                <h4 class="text-danger">{{ $product->price}}</h4>
                <button class="btn btn-primary btn-buy">افزودن به سبد خرید 🛒</button>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
