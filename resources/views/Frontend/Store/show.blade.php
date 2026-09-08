@extends('Frontend.Store.Layouts.MasterMinimal')

@section('Main')
    <main class="store-detail store-product-detail" aria-label="جزئیات محصول">
        <div class="store-detail-grid">
            <div class="store-detail-media">
                <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" class="store-detail-image">
            </div>

            <div class="store-detail-content">
                <h1>{{ $product->title }}</h1>
                <div class="store-body">{!! $product->body !!}</div>
                <div class="store-price">{{ $product->price }}</div>

                <form method="post" action="{{ route('buyer.order.store') }}" class="AddProduct store-detail-form">
                    {!! csrf_field() !!}
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="store-quantity">
                        <label for="count_product">تعداد</label>
                        <input id="count_product" type="number" name="count_product" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary btn-buy">افزودن به سبد خرید</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script>
    jQuery(function ($) {
        $('.AddProduct').on('submit', function (event) {
            event.preventDefault();

            const $form = $(this);
            const $button = $form.find('.btn-buy');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                dataType: 'JSON',
                data: $form.serialize(),
                beforeSend: function () {
                    $button.prop('disabled', true);
                },
                success: function (data) {
                    if ($.isEmptyObject(data.error)) {
                        const $cart = $('#cart-val');
                        const current = Number($cart.text() || 0);
                        const added = Number(data.success || 0);
                        $cart.text(current + added);
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'danger');
                    }
                },
                error: function () {
                    showToast('خطا در افزودن محصول به سبد خرید.', 'danger');
                },
                complete: function () {
                    $button.prop('disabled', false);
                }
            });
        });

        function showToast(message, type) {
            const container = document.querySelector('#toastContainer');
            if (!container) return;

            container.innerHTML = `<div class="toast align-items-center bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;

            const toast = new bootstrap.Toast(container.querySelector('.toast'), { delay: 3000 });
            toast.show();
        }
    });
</script>
@endsection
