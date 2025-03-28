@extends('Frontend.Shop.layouts.Master')
@section('Main')
<!-- بخش محصولات -->
<section class="content container my-4">
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-6 col-lg-4 col-6 mb-3">
            <a href="{{ route('front.product.show', $product) }}">
            <div class="card">
                <img src="{{ asset($product->images['thum'])}}" class="card-img-top" alt="محصول 1">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $product->title}}</h5>
                    <h6 class="text-primary mb-1 pb-3">{{$product->price}}</h6>
                </div>
            </div>
            </a>
        </div>
        @endforeach
    </div>
</section>
@endsection
@section('scripts')

@endsection
