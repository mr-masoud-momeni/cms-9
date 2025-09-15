@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="container mt-5">
        <div class="alert alert-danger rounded-3 shadow-sm p-4">
            <h3 class="mb-3">❌ پرداخت ناموفق بود</h3>
            <p>شماره سفارش: {{ $payment->order_id }}</p>
            <p>کد خطا: {{ $payment->error_code ?? 'نامشخص' }}</p>

            <a href="{{ url('/checkout') }}" class="btn btn-warning mt-3">تلاش دوباره</a>
        </div>
    </div>
@endsection
