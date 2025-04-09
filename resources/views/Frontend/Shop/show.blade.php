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
                <p class="text-muted">{!! $product->body !!}</p>
                <h4 class="text-danger">{{ $product->price}}</h4>
                <form method="post" action="{{route('order.store')}}" class="AddProduct" >
                    {!! csrf_field() !!}
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="text-center">
                        <div class="d-flex flex-column mb-4">
                            <input type="number" name="count_product" value="1">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-buy">افزودن به سبد خرید</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function($){
            $('.AddProduct').submit(function (event) {
                event.preventDefault();
                var $this = $(this);
                var url = $this.attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    datatype: 'JSON',
                    data: $this.serialize(),
                    success: function(data) {

                        if($.isEmptyObject(data.error)){
                            var order = $("#cart-val").attr('value');
                            order = Number(order);
                            order = order+data.success;
                            $("#cart-val").attr('value', order);
                            showToast(data.message, "success");

                        }else{

                            showToast(data.message, "danger");

                        }

                    }
                });
            });
            function showToast(message, type) {
                const toastHTML = `
                                    <div class="toast align-items-center bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                      <div class="d-flex">
                                        <div class="toast-body">${message}</div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                      </div>
                                    </div>
                                  `;

                const container = document.querySelector('#toastContainer');
                container.innerHTML = toastHTML;
                const toast = new bootstrap.Toast(container.querySelector('.toast'), { delay: 3000 });
                toast.show();
            }
        });

    </script>
@endsection
