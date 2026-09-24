@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header', ['showStats' => false])
@endsection

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/store-cart.css') }}">
@endsection

@section('Main')
<div class="store-cart-page">

    @include('Frontend.layouts.errors')
    @include('Frontend.layouts.message')

    @if($products->count())

        <div class="store-cart-layout">

            {{-- محصولات سبد --}}
            <div class="store-cart-products">

                <div class="store-cart-heading">
                    <h1>سبد خرید</h1>
                    <span>{{ $products->count() }} محصول</span>
                </div>

                <div class="store-cart-list">

                    @foreach($products as $product)

                        @php
                            $productPrice = $product->cart_price * $product->cart_quantity;
                        @endphp

                        <div class="store-cart-item">

                            <div class="store-cart-item-image">
                                @if(!empty($product->images['thum']))
                                    <img
                                        src="{{ asset($product->images['thum']) }}"
                                        alt="{{ $product->title }}"
                                    >
                                @else
                                    <div class="store-cart-image-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="store-cart-item-content">

                                <div class="store-cart-item-top">
                                    <h2>{{ $product->title }}</h2>

                                    <form
                                        action="{{ route('buyer.order.destroy', $product->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="store-cart-remove"
                                            aria-label="حذف محصول"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="store-cart-item-meta">

                                    <div>
                                        <span>قیمت واحد</span>
                                        <strong>
                                            {{ number_format($product->cart_price) }}
                                            تومان
                                        </strong>
                                    </div>

                                    <div>
                                        <span>تعداد</span>
                                        <strong>
                                            {{ $product->cart_quantity }}
                                        </strong>
                                    </div>

                                    <div class="store-cart-item-total">
                                        <span>جمع</span>
                                        <strong>
                                            {{ number_format($productPrice) }}
                                            تومان
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- خلاصه سفارش --}}
            <aside class="store-cart-summary">

                @php
                    $grandTotal = $totalAmount + $shippingAmount;
                @endphp

                <h2>خلاصه سفارش</h2>

                <div class="store-cart-summary-row">
                    <span>تعداد محصولات</span>
                    <strong>{{ $products->count() }}</strong>
                </div>

                <div class="store-cart-summary-row">
                    <span>جمع کالاها</span>
                    <strong>{{ number_format($totalAmount) }} تومان</strong>
                </div>

                <div class="store-cart-summary-row">
                    <span>هزینه ارسال</span>
                    <strong>{{ $shippingAmount > 0 ? number_format($shippingAmount) . ' تومان' : 'رایگان' }}</strong>
                </div>

                <div class="store-cart-summary-divider"></div>

                <div class="store-cart-summary-total">
                    <span>مبلغ قابل پرداخت</span>
                    <strong>
                        {{ number_format($grandTotal) }}
                        <small>تومان</small>
                    </strong>
                </div>

            </aside>

        </div>


        {{-- اطلاعات گیرنده --}}
        <section class="store-checkout">

            <div class="store-checkout-heading">
                <span class="store-checkout-icon">
                    <i class="bi bi-person"></i>
                </span>

                <div>
                    <h2>اطلاعات گیرنده</h2>
                    <p>اطلاعات ارسال سفارش را وارد کنید.</p>
                </div>
            </div>


            <form method="POST" action="{{ route('checkout') }}">
                @csrf

                <div class="store-checkout-fields">

                    <div class="store-field">
                        <label for="receiver_name">
                            نام و نام خانوادگی
                        </label>

                        <input
                            type="text"
                            id="receiver_name"
                            name="receiver_name"
                            value="{{ old('receiver_name') }}"
                            required
                        >
                    </div>


                    <div class="store-field">
                        <label for="receiver_phone">
                            شماره موبایل
                        </label>

                        <input
                            type="text"
                            id="receiver_phone"
                            name="receiver_phone"
                            value="{{ old('receiver_phone') }}"
                            required
                        >
                    </div>


                    <div class="store-field">
                        <label for="receiver_province">
                            استان
                        </label>

                        <input
                            type="text"
                            id="receiver_province"
                            name="receiver_province"
                            value="{{ old('receiver_province') }}"
                            required
                        >
                    </div>


                    <div class="store-field">
                        <label for="receiver_city">
                            شهر
                        </label>

                        <input
                            type="text"
                            id="receiver_city"
                            name="receiver_city"
                            value="{{ old('receiver_city') }}"
                            required
                        >
                    </div>


                    <div class="store-field">
                        <label for="receiver_postal_code">
                            کد پستی
                        </label>

                        <input
                            type="text"
                            id="receiver_postal_code"
                            name="receiver_postal_code"
                            value="{{ old('receiver_postal_code') }}"
                            required
                        >
                    </div>


                    <div class="store-field store-field-full">
                        <label for="receiver_address">
                            آدرس کامل
                        </label>

                        <textarea
                            id="receiver_address"
                            name="receiver_address"
                            rows="4"
                            required
                        >{{ old('receiver_address') }}</textarea>
                    </div>

                </div>


                <div class="store-checkout-footer">

                    <div class="store-checkout-final-price">
                        <span>مبلغ نهایی</span>

                        <strong>
                            {{ number_format($grandTotal) }}
                            <small>تومان</small>
                        </strong>
                    </div>

                    <button type="submit" class="store-button">
                        ادامه و انتخاب روش پرداخت
                        <i class="bi bi-arrow-left"></i>
                    </button>

                </div>

            </form>

        </section>

    @else

        <div class="store-cart-empty">

            <div class="store-cart-empty-icon">
                <i class="bi bi-bag"></i>
            </div>

            <h1>سبد خرید خالی است</h1>

            <p>
                هنوز محصولی به سبد خرید اضافه نکرده‌اید.
            </p>

            <a href="{{ route('index.show') }}">
                مشاهده محصولات
                <i class="bi bi-arrow-left"></i>
            </a>

        </div>

    @endif

</div>
@endsection
