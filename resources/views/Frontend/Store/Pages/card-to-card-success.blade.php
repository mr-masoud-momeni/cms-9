@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header-minimal')
@endsection

@section('Main')
<div class="store-payment-page">
    <div class="store-payment-card">
        <div class="store-payment-heading">
            <div class="store-payment-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <h1>رسید با موفقیت ثبت شد</h1>
                <p>رسید پرداخت شما برای فروشگاه ارسال شد و پس از تأیید، سفارش پردازش می‌شود.</p>
            </div>
        </div>

        <div class="text-center p-4">
            <p class="mb-2">شماره سفارش: <strong>{{ $payment->order_id }}</strong></p>
            <p class="mb-4">{{ $reservationMessage }}</p>
            <a href="{{ route('index.show') }}" class="btn btn-primary">بازگشت به فروشگاه</a>
        </div>
    </div>
</div>
@endsection
