@extends('Frontend.Store.Layouts.MasterMain')

@section('title', $product->title)

@section('header')
    @include('Frontend.Store.Layouts.header', ['showStats' => false])
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

                @if($product->available_stock <= 0)
                    <div class="mb-3">
                        <small class="text-danger">این محصول ناموجود است.</small>
                    </div>
                @endif

                <form method="post" action="{{ route('buyer.order.store') }}" class="AddProduct store-detail-form">
                    {!! csrf_field() !!}

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="store-quantity">
                        <label for="count_product">{{ $product->unit }}</label>

                        <div class="quantity-control" role="group" aria-label="تعداد">
                            <button
                                type="button"
                                class="quantity-button quantity-decrease"
                                aria-label="کاهش تعداد"
                                {{ $product->available_stock <= 0 ? 'disabled' : '' }}
                            >−</button>

                            <input
                                id="count_product"
                                type="number"
                                name="count_product"
                                value="1"
                                min="1"
                                max="{{ $product->available_stock }}"
                                inputmode="numeric"
                                data-unit="{{ e($product->unit) }}"
                                data-stock="{{ $product->available_stock }}"
                                {{ $product->available_stock <= 0 ? 'disabled' : '' }}
                            >

                            <button
                                type="button"
                                class="quantity-button quantity-increase"
                                aria-label="افزایش تعداد"
                                {{ $product->available_stock <= 0 ? 'disabled' : '' }}
                            >+</button>
                        </div>

                        <small id="quantity-message" class="text-danger" style="display:none; margin-top:6px;"></small>
                    </div>

                    <button type="submit" class="store-button" {{ $product->available_stock <= 0 ? 'disabled' : '' }}>
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

        $('.quantity-decrease').on('click', function () {
            const current = Number($quantity.val() || 1);

            if (current > 1) {
                $quantity.val(current - 1).trigger('change');
            }
        });

        $('.quantity-increase').on('click', function () {
            const current = Number($quantity.val() || 1);
            const next = current + 1;

            if (next <= stock) {
                $quantity.val(next).trigger('change');
            } else {
                validateQuantity();
            }
        });

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
