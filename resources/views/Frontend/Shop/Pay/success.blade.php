@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="container mt-5">
        <div class="alert alert-success rounded-3 shadow-sm p-4">
            <h3 class="mb-3">✅ پرداخت با موفقیت انجام شد</h3>
            <p>مبلغ: {{ number_format($payment->amount) }} تومان</p>
            <p>شماره سفارش: {{ $payment->order_id }}</p>
            <p>کد پیگیری بانک: {{ $payment->bank_ref }}</p>
            <p>RefId: {{ $payment->ref_id }}</p>

            <a href="{{ url('/') }}" class="btn btn-primary mt-3">بازگشت به صفحه اصلی</a>
        </div>
    </div>
@endsection
