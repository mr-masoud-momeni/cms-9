@extends('Frontend.Shop.layouts.Master')

@section('Main')
    <style>
        .instagram-product {
            max-width: 980px;
            margin: 40px auto 70px;
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-media {
            background: #f7f7f7;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid #efefef;
        }

        .shop-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
            color: #fff;
            font-weight: 700;
            font-size: 18px;
        }

        .shop-name {
            font-weight: 700;
            margin: 0;
        }

        .shop-domain {
            color: #737373;
            font-size: 13px;
            margin: 2px 0 0;
        }

        .product-content {
            padding: 24px 22px;
            flex: 1;
        }

        .product-title {
            font-size: 25px;
            font-weight: 700;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .product-description {
            color: #444;
            line-height: 2;
            margin-bottom: 25px;
        }

        .product-price {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            align-items: stretch;
        }

        .quantity {
            width: 82px;
            border: 1px solid #dbdbdb;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .btn-buy {
            flex: 1;
            border: 0;
            border-radius: 8px;
            font-weight: 700;
            padding: 11px 18px;
        }

        .product-meta {
            border-top: 1px solid #efefef;
            padding: 16px 22px;
            color: #737373;
            font-size: 13px;
        }

        @media (max-width: 767px) {
            .instagram-product {
                margin: 15px 0 40px;
                border-left: 0;
                border-right: 0;
                border-radius: 0;
            }

            .product-title {
                font-size: 22px;
            }
        }
    </style>

    <div class="container">
        <div class="instagram-product">
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="product-media">
                        @if(!empty($product->images['thum']))
                            <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}">
                        @else
                            <span class="text-muted">تصویری برای این محصول وجود ندارد</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="product-info">
                        <div class="product-header">
                            <div class="shop-avatar">
                                {{ mb_substr($product->shop->name ?? 'ف', 0, 1) }}
                            </div>
                            <div>
                                <p class="shop-name">{{ $product->shop->name ?? '' }}</p>
                                <p class="shop-domain">{{ $product->shop->domain ?? '' }}</p>
                            </div>
                        </div>

                        <div class="product-content">
                            <h1 class="product-title">{{ $product->title }}</h1>

                            <div class="product-description">
                                {!! $product->body !!}
                            </div>

                            <div class="product-price">
                                {{ $product->price }}
                            </div>

                            <form method="post" action="{{ route('order.store') }}" class="AddProduct">
                                {!! csrf_field() !!}
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div class="product-actions">
                                    <input class="quantity" type="number" name="count_product" value="1" min="1">
                                    <button type="submit" class="btn btn-primary btn-buy">
                                        افزودن به سبد خرید
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="product-meta">
                            اشتراک‌گذاری این محصول با دوستانت ✨
                        </div>
                    </div>
                </div>
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
                    dataType: 'JSON',
                    data: $this.serialize(),
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            var order = Number($("#cart-val").attr('value')) || 0;
                            $("#cart-val").attr('value', order + data.success);
                            showToast(data.message, "success");
                        } else {
                            showToast(data.message, "danger");
                        }
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message || 'افزودن محصول به سبد خرید انجام نشد.';
                        showToast(message, "danger");
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
                if (!container) return;

                container.innerHTML = toastHTML;
                const toast = new bootstrap.Toast(container.querySelector('.toast'), { delay: 3000 });
                toast.show();
            }
        });
    </script>
@endsection
