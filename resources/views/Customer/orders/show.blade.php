@extends('Customer.layouts.Master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">جزئیات سفارش</div>
            <div class="panel-body">
                <div class="mb-3">
                    <strong>خریدار:</strong> {{ optional($order->buyer)->name }}<br>
                    <strong>تاریخ پرداخت:</strong> {{ optional($order)->paid_at ?? '-' }}<br>
                    <strong>مبلغ نهایی:</strong> {{ number_format(optional($order->payment)->amount ?? 0) }} تومان<br>
                    <strong>وضعیت:</strong> {{ $order->status }}
                </div>

                <h5>اقلام سفارش:</h5>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                        <th>قیمت واحد</th>
                        <th>قیمت کل</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->products as $product)
                        <tr>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>{{ number_format($product->pivot->price) }}</td>
                            <td>{{ number_format($product->pivot->price * $product->pivot->quantity) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <form method="POST" action="{{ route('shop.orders.update', $order) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label>وضعیت سفارش</label>
                        <select name="status" id="order-status" class="form-select">
                            <option value="pending" @if($order->status == 'pending') selected @endif>
                                در انتظار پرداخت
                            </option>

                            <option value="paid" @if($order->status == 'paid') selected @endif>
                                پرداخت شده
                            </option>

                            <option value="shipped" @if($order->status == 'shipped') selected @endif>
                                ارسال شده
                            </option>

                            <option value="completed" @if($order->status == 'completed') selected @endif>
                                انجام شده
                            </option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="tracking-wrapper">
                        <label>کد رهگیری پستی</label>
                        <input
                            type="text"
                            name="tracking_code"
                            value="{{ $order->tracking_code }}"
                            class="form-control"
                        >
                    </div>

                    <button class="btn btn-primary">ثبت تغییرات</button>
                </form>


                <a href="{{ route('shop.orders.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
            </div>
            <div class="panel-footer">Panel Footer</div>


        </div>

    </div>
</div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('order-status');
            const trackingBox  = document.getElementById('tracking-wrapper');

            function toggleTracking() {
                if (statusSelect.value === 'shipped') {
                    trackingBox.style.display = 'block';
                } else {
                    trackingBox.style.display = 'none';
                }
            }

            toggleTracking(); // نمایش/مخفی اولیه
            statusSelect.addEventListener('change', toggleTracking);
        });
    </script>
@endsection
