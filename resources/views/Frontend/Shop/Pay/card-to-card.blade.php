@extends('Frontend.Shop.layouts.Master')

@section('Main')
<div class="content container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('Frontend.layouts.errors')

            <div class="main-box clearfix p-4">
                <h5 class="mb-4">پرداخت کارت به کارت</h5>

                <div class="alert alert-light border mb-4">
                    <div class="mb-2"><strong>مبلغ سفارش:</strong> {{ number_format($totalAmount) }} ریال</div>
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
</div>
@endsection
