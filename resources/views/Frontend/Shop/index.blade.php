@extends('Frontend.Shop.layouts.Master')
@section('Main')
<!-- بخش محصولات -->
<section class="container my-4">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12 mb-3">
            <div class="card">
                <img src="product1.jpg" class="card-img-top" alt="محصول 1">
                <div class="card-body">
                    <h5 class="card-title text-center">محصول 1</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 mb-3">
            <div class="card">
                <img src="product2.jpg" class="card-img-top" alt="محصول 2">
                <div class="card-body">
                    <h5 class="card-title text-center">محصول 2</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12 mb-3">
            <div class="card">
                <img src="product3.jpg" class="card-img-top" alt="محصول 3">
                <div class="card-body">
                    <h5 class="card-title text-center">محصول 3</h5>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')

@endsection
