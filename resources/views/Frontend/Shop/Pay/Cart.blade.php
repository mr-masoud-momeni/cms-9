@extends('Frontend.Shop.layouts.Master')

@section('Main')

    <!-- داشبورد یوزر -->
    <div class="content container my-5">
        <div class="row align-items-center">

            <div class="col-md-12">

                @include('Frontend.layouts.errors')
                @include('Frontend.layouts.message')

                @auth('buyer')

                    @isset($order)

                        <div class="main-box clearfix">

                            <!-- سبد خرید -->
                            <div class="table-responsive">
                                <table class="table user-list">

                                    <thead>
                                    <tr>
                                        <th><span>محصول</span></th>
                                        <th class="text-center"><span>قیمت</span></th>
                                        <th class="text-center"><span>تعداد</span></th>
                                        <th class="text-center"><span>قیمت تعداد</span></th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    @foreach($order->products as $product)

                                        @php
                                            $productPrice = $product->pivot->price * $product->pivot->quantity;
                                        @endphp

                                        <tr>

                                            <td>
                                                <img
                                                    width="100"
                                                    src="{{ asset($product->images['thum']) }}"
                                                    alt="{{ $product->title }}"
                                                >

                                                <a href="#" class="user-link">
                                                    {{ $product->title }}
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($product->pivot->price) }}
                                            </td>

                                            <td class="text-center">
                                                {{ $product->pivot->quantity }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($productPrice) }}
                                            </td>

                                            <td style="width: 10%;" class="text-center">

                                                <form
                                                    action="{{ route('order.destroy', $product->id) }}"
                                                    method="POST"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-link text-danger p-0"
                                                    >
                                                        <i
                                                            class="bi bi-trash"
                                                            style="font-size: 1.5rem;"
                                                        ></i>
                                                    </button>

                                                </form>

                                            </td>

                                        </tr>

                                    @endforeach

                                    <tr>
                                        <td><strong>جمع کل</strong></td>

                                        <td
                                            colspan="3"
                                            class="text-center"
                                        >
                                            <strong>
                                                {{ number_format($totalAmount) }}
                                            </strong>
                                        </td>

                                        <td></td>
                                    </tr>

                                    </tbody>

                                </table>
                            </div>


                            <!-- اطلاعات گیرنده -->
                            <div class="mt-5">

                                <h5 class="mb-4">
                                    اطلاعات گیرنده
                                </h5>

                                <form
                                    method="POST"
                                    action="{{ route('buyer.payment') }}"
                                >

                                    @csrf

                                    <div class="row">

                                        <!-- نام گیرنده -->
                                        <div class="col-md-6 mb-3">

                                            <label for="receiver_name" class="form-label">
                                                نام و نام خانوادگی
                                            </label>

                                            <input
                                                type="text"
                                                id="receiver_name"
                                                name="receiver_name"
                                                class="form-control"
                                                value="{{ old('receiver_name') }}"
                                                required
                                            >

                                        </div>


                                        <!-- شماره تماس -->
                                        <div class="col-md-6 mb-3">

                                            <label for="receiver_phone" class="form-label">
                                                شماره موبایل
                                            </label>

                                            <input
                                                type="text"
                                                id="receiver_phone"
                                                name="receiver_phone"
                                                class="form-control"
                                                value="{{ old('receiver_phone') }}"
                                                required
                                            >

                                        </div>


                                        <!-- استان -->
                                        <div class="col-md-6 mb-3">

                                            <label for="receiver_province" class="form-label">
                                                استان
                                            </label>

                                            <input
                                                type="text"
                                                id="receiver_province"
                                                name="receiver_province"
                                                class="form-control"
                                                value="{{ old('receiver_province') }}"
                                                required
                                            >

                                        </div>


                                        <!-- شهر -->
                                        <div class="col-md-6 mb-3">

                                            <label for="receiver_city" class="form-label">
                                                شهر
                                            </label>

                                            <input
                                                type="text"
                                                id="receiver_city"
                                                name="receiver_city"
                                                class="form-control"
                                                value="{{ old('receiver_city') }}"
                                                required
                                            >

                                        </div>


                                        <!-- کد پستی -->
                                        <div class="col-md-6 mb-3">

                                            <label for="receiver_postal_code" class="form-label">
                                                کد پستی
                                            </label>

                                            <input
                                                type="text"
                                                id="receiver_postal_code"
                                                name="receiver_postal_code"
                                                class="form-control"
                                                value="{{ old('receiver_postal_code') }}"
                                                required
                                            >

                                        </div>


                                        <!-- آدرس -->
                                        <div class="col-md-12 mb-4">

                                            <label for="receiver_address" class="form-label">
                                                آدرس کامل
                                            </label>

                                            <textarea
                                                id="receiver_address"
                                                name="receiver_address"
                                                class="form-control"
                                                rows="4"
                                                required
                                            >{{ old('receiver_address') }}</textarea>

                                        </div>

                                    </div>


                                    <!-- دکمه پرداخت -->
                                    <div class="text-end">

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            ادامه و پرداخت
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    @endisset

                @endauth


                {{-- اگر کاربر لاگین نیست و داده‌ها از سشن می‌آیند --}}
                @guest('buyer')

                    @if(is_array($order) && count($order))

                        <ul>

                            @foreach($order as $productId => $qty)

                                <li>
                                    محصول {{ $productId }}
                                    – تعداد: {{ $qty }}
                                </li>

                            @endforeach

                        </ul>

                    @else

                        <p>سبد خرید خالی است.</p>

                    @endif

                @endguest

            </div>

        </div>
    </div>

@endsection
