@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header', ['showStats' => false])
@endsection

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/store-payment.css') }}?v=20260924">
@endsection

@section('Main')
<div class="store-payment-page">
    <div class="store-payment-card">
        <div class="store-payment-success">
            <div class="store-payment-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <h1>رسید با موفقیت ثبت شد</h1>

            <p>
                رسید پرداخت شما برای فروشگاه ارسال شد و پس از تأیید، سفارش پردازش می‌شود.
            </p>

            <div class="store-payment-order">
                شماره سفارش:
                <strong>{{ $payment->order_id }}</strong>
            </div>

            <a href="{{ route('index.show') }}" class="btn btn-primary">
                بازگشت به فروشگاه
            </a>
        </div>
    </div>
</div>
@endsection
