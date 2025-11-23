@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- داشبورد یوزر -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- داشبورد یوزر -->
            <div class="col-md-12">
                @include('Frontend.layouts.errors')
                @include('Frontend.layouts.message')
                @auth('buyer')
                @isset($order)
                    <div class="main-box clearfix">
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
                                @php $totalPrice = 0; @endphp
                                @foreach($order->products as $product)
                                    @php
                                        $productPrice = $product->pivot->price * $product->pivot->quantity;
                                    @endphp
                                    <tr>
                                        <td>
                                            <img width="100" src="{{ asset($product->images['thum'])}}" alt="">
                                            <a href="#" class="user-link">{{ $product->title }}</a>
                                        </td>
                                        <td class="text-center">{{ $product->price }}</td>
                                        <td class="text-center">{{ $product->pivot->quantity }}</td>
                                        <td class="text-center">{{ $product->pivot->price }}</td>
                                        <td style="width: 10%;" class="text-center">
                                            <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>جمع کل</td>
                                    <td class="text-center">{{ $totalAmount }}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <form method="post" action="{{ route('buyer.payment') }}">
                            {{ csrf_field() }}
                            <button type="submit">تکمیل خرید</button>
                        </form>
                    </div>
                @endisset
                @endauth

                {{-- اگر کاربر لاگین نیست و داده‌ها از سشن می‌آیند --}}
                @guest('buyer')
                @if(is_array($order) && count($order))
                    <ul>
                        @foreach($order as $productId => $qty)
                            <li>محصول {{ $productId }} – تعداد: {{ $qty }}</li>
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
