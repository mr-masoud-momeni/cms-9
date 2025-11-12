@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- داشبورد یوزر -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- داشبورد یوزر -->
            <div class="col-md-12">
                @include('Frontend.layouts.errors')
                @include('Frontend.layouts.message')
                {{-- اگر کاربر لاگین است و $order یک مدل Order است --}}
                @if($orders->isEmpty())
                    <p>هیچ سفارش تکمیل شده‌ای وجود ندارد.</p>
                @else
                    @foreach($orders as $order)
                        <div class="main-box my-4 p-3 border rounded">
                            <h5>سفارش شماره: {{ $order->id }} – تاریخ: {{ $order->created_at->format('Y/m/d') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>محصول</th>
                                        <th class="text-center">قیمت واحد</th>
                                        <th class="text-center">تعداد</th>
                                        <th class="text-center">قیمت کل</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $totalPrice = 0; @endphp
                                    @foreach($order->products as $product)
                                        @php
                                            $productPrice = $product->pivot->price * $product->pivot->quantity;
                                            $totalPrice += $productPrice;
                                        @endphp
                                        <tr>
                                            <td>
                                                <img width="60" src="{{ asset($product->images['thum']) }}" alt="">
                                                {{ $product->title }}
                                            </td>
                                            <td class="text-center">{{ number_format($product->pivot->price) }} تومان</td>
                                            <td class="text-center">{{ $product->pivot->quantity }}</td>
                                            <td class="text-center">{{ number_format($productPrice) }} تومان</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">جمع کل</td>
                                        <td class="text-center fw-bold">{{ number_format($totalPrice) }} تومان</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

        </div>
    </div>
@endsection
