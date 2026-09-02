@extends('Frontend.Shop.layouts.Master')

@section('Main')
<div class="content container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('Frontend.layouts.errors')
            @include('Frontend.layouts.message')

            <div class="main-box clearfix p-4">
                <h5 class="mb-4">انتخاب روش پرداخت</h5>

                <div class="mb-4">
                    <strong>مبلغ قابل پرداخت:</strong>
                    {{ number_format($totalAmount) }}
                </div>

                @if($gateway)
                    <form method="POST" action="{{ route('payment.online') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            پرداخت آنلاین
                        </button>
                    </form>
                @endif

                @if($bankAccount)
                    <a href="{{ route('payment.card_to_card') }}" class="btn btn-outline-primary w-100">
                        پرداخت کارت به کارت
                    </a>
                @endif

                @if(!$gateway && !$bankAccount)
                    <div class="alert alert-warning mb-0">
                        در حال حاضر هیچ روش پرداختی برای این فروشگاه فعال نیست.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
