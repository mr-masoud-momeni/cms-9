@extends('Frontend.Store.Layouts.MasterMinimal')

@section('Main')
    <main class="store-detail" aria-label="جزئیات محصول">
        <div class="store-detail-grid">
            <div>
                <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" class="store-detail-image">
            </div>
            <div class="store-detail-content">
                <h1>{{ $product->title }}</h1>
                <div class="store-body">{!! $product->body !!}</div>
                <div class="store-price">{{ $product->price }}</div>
                <form method="post" action="{{ route('buyer.order.store') }}" class="AddProduct store-detail-form">
                    {!! csrf_field() !!}
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-4">
                        <label for="count_product">تعداد</label>
                        <div><input id="count_product" type="number" name="count_product" value="1" min="1"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-buy">افزودن به سبد خرید</button>
                </form>
            </div>
        </div>
    </main>
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
                const toastHTML = `<div class="toast align-items-center bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
                const container = document.querySelector('#toastContainer');
                container.innerHTML = toastHTML;
                const toast = new bootstrap.Toast(container.querySelector('.toast'), { delay: 3000 });
                toast.show();
            }
        });
    </script>
@endsection
