@extends('Frontend.Shop.layouts.Master')

@section('Main')
<div class="content container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="main-box clearfix p-4 text-center">
                <h5 class="mb-3">رسید با موفقیت ثبت شد</h5>
                <p class="mb-2">رسید پرداخت شما برای فروشگاه ارسال شد و پس از تأیید، سفارش پردازش می‌شود.</p>
                <p class="mb-4">شماره سفارش: <strong>{{ $order->id }}</strong></p>
                <a href="{{ route('index.show') }}" class="btn btn-primary">بازگشت به فروشگاه</a>
            </div>
        </div>
    </div>
</div>
@endsection
