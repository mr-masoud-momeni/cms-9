@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header-minimal')
@endsection

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/store-payment.css') }}">
@endsection

@section('Main')
<div class="store-payment-page">
    @include('Frontend.layouts.errors')
    @include('Frontend.layouts.message')

    <div class="store-payment-card">
        <div class="store-payment-heading">
            <div class="store-payment-icon">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <h1>پرداخت کارت به کارت</h1>
                <p>اطلاعات و رسید پرداخت سفارش خود را ثبت کنید.</p>
            </div>
        </div>

        <div class="store-payment-amount">
            @php
                $itemsTotal = $checkoutOrder->products->sum(function ($product) {
                    return $product->pivot->price * $product->pivot->quantity;
                });
                $shippingAmount = (int) ($checkoutOrder->shipping_amount ?? 0);
            @endphp

            <div>جمع کالاها: {{ number_format($itemsTotal) }} تومان</div>
            <div>هزینه ارسال: {{ $shippingAmount > 0 ? number_format($shippingAmount) . ' تومان' : 'رایگان' }}</div>

            <span>مبلغ قابل پرداخت</span>
            <strong>
                {{ number_format($totalAmount) }}
                <small>تومان</small>
            </strong>
        </div>

        <div class="store-payment-card-body">
            <div class="alert alert-light border mb-4">
                <div class="mb-2"><strong>صاحب حساب:</strong> {{ $bankAccount->account_holder }}</div>
                <div class="mb-2"><strong>شماره کارت:</strong> {{ $bankAccount->card_number }}</div>
                <div><strong>شماره شبا:</strong> {{ $bankAccount->sheba }}</div>
            </div>

            <form method="POST" action="{{ route('payment.card_to_card.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="tracking_code" class="form-label">کد پیگیری (اختیاری)</label>
                    <input type="text" id="tracking_code" name="tracking_code" class="form-control" value="{{ old('tracking_code') }}">
                </div>

                <div class="mb-3">
                    <label for="receipt" class="form-label">تصویر رسید پرداخت</label>
                    <input type="file" id="receipt" name="receipt" class="form-control" accept="image/*" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">توضیحات (اختیاری)</label>
                    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">ثبت رسید و ارسال برای تأیید</button>
            </form>
        </div>
    </div>
</div>
@endsection
