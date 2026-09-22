@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header-minimal')
@endsection

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/store-show.css') }}">
@endsection

@section('Main')
    <main class="store-detail store-product-detail" aria-label="جزئیات محصول">
        <div class="store-detail-grid">
            <div class="store-detail-media">
                <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" class="store-detail-image">
            </div>

            <div class="store-detail-content">
                <h1>{{ $product->title }}</h1>

                <div class="store-body">
                    {!! nl2br(e($product->body)) !!}
                </div>

                <div class="store-price">
                    {{ $product->price }}
                </div>

                <div class="mb-3">
                    @if($product->stock > 0)
                        <small class="text-muted">موجودی: موجود</small>
                    @else
                        <small class="text-danger">این محصول ناموجود است.</small>
                    @endif
                </div>

                <form method="post" action="{{ route('buyer.order.store') }}" class="AddProduct store-detail-form">
                    {!! csrf_field() !!}

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="store-quantity">
                        <label for="count_product">{{ $product->unit }}</label>

                        <input
                            id="count_product"
                            type="number"
                            name="count_product"
                            value="1"
                            min="1"
                            max="{{ $product->stock }}"
                            data-unit="{{ e($product->unit) }}"
                            data-stock="{{ $product->stock }}"
                            {{ $product->stock <= 0 ? "disabled" : "" }}
                        >

                        <small id="quantity-message" class="text-danger" style="display:none; margin-top:6px;"></small>
                    </div>

                    <button type="submit" class="store-button" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        افزودن به سبد خرید
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script>
    jQuery(function ($) {
        const $quantity = $('#count_product');
        const $quantityMessage = $('#quantity-message');
        const stock = Number($quantity.data('stock') || 0);
        const unit = $quantity.data('unit') || '';

        function validateQuantity() {
            let quantity = Number($quantity.val() || 0);

            if (quantity < 1) {
                quantity = 1;
                $quantity.val(quantity);
            }

            if (quantity > stock) {
                $quantityMessage
                    .text('حداکثر ' + stock + ' ' + unit + ' قابل سفارش است.')
                    .show();

                return false;
            }

            $quantityMessage.hide().text('');
            return true;
        }

        $quantity.on('input change', validateQuantity);

        $('.AddProduct').on('submit', function (event) {
            event.preventDefault();

            if (!validateQuantity()) {
                return;
            }

            const $form = $(this);
            const $button = $form.find('.store-button');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $form.serialize(),

                beforeSend: function () {
                    $button.prop('disabled', true);
                },

                success: function (data) {
                    if (data.error && data.error.length) {
                        showToast(data.error[0], 'danger');
                        return;
                    }

                    const $cart = $('#cart-val');
                    const current = Number($cart.text() || 0);
                    const added = Number(data.success || 0);

                    $cart.text(current + added);

                    showToast(
                        data.message || 'محصول با موفقیت به سبد خرید اضافه شد.',
                        'success'
                    );
                },

                error: function () {
                    showToast('خطا در افزودن محصول به سبد خرید.', 'danger');
                },

                complete: function () {
                    $button.prop('disabled', stock <= 0);
                }
            });
        });

        function showToast(message, type = 'success') {
            const container = document.querySelector('#toastContainer');

            if (!container) {
                console.warn('toastContainer پیدا نشد.');
                return;
            }

            const toastElement = document.createElement('div');

            toastElement.className = 'toast text-bg-' + type + ' border-0';
            toastElement.setAttribute('role', 'alert');
            toastElement.setAttribute('aria-live', 'assertive');
            toastElement.setAttribute('aria-atomic', 'true');

            toastElement.innerHTML = '<div class="d-flex align-items-center">' +
                '<div class="toast-body">' + message + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="بستن"></button>' +
                '</div>';

            container.appendChild(toastElement);

            const toast = bootstrap.Toast.getOrCreateInstance(
                toastElement,
                { delay: 3000 }
            );

            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });

            toast.show();
        }
    });
</script>
@endsection
