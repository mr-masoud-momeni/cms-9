@extends('Frontend.Shop.layouts.Master')

@section('Main')
<div class="container my-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="fs-1 mb-2">✅</div>
                        <h2 class="fw-bold mb-2">سفارش شما تأیید شد</h2>
                        <p class="text-muted mb-0">{{ $shop->name }}</p>
                    </div>

                    <div class="alert alert-success rounded-3">
                        پرداخت سفارش <strong>#{{ $order->id }}</strong> با موفقیت تأیید شده است.
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">جزئیات سفارش</h5>
                        @foreach($order->products as $product)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $product->name }} × {{ $product->pivot->quantity }}</span>
                                <span>{{ number_format($product->pivot->price * $product->pivot->quantity) }} تومان</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-3">
                        <span>مبلغ کل</span>
                        <span>{{ number_format((float) $order->total) }} تومان</span>
                    </div>

                    @if($order->receiver_name || $order->receiver_address)
                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">اطلاعات تحویل</h5>
                            @if($order->receiver_name)
                                <p class="mb-1">گیرنده: {{ $order->receiver_name }}</p>
                            @endif
                            @if($order->receiver_phone)
                                <p class="mb-1">موبایل: {{ $order->receiver_phone }}</p>
                            @endif
                            @if($order->receiver_province || $order->receiver_city)
                                <p class="mb-1">{{ $order->receiver_province }}{{ $order->receiver_province && $order->receiver_city ? '، ' : '' }}{{ $order->receiver_city }}</p>
                            @endif
                            @if($order->receiver_address)
                                <p class="mb-0">آدرس: {{ $order->receiver_address }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="text-center text-muted small mt-4">
                        سفارش شما در حال پردازش است.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
