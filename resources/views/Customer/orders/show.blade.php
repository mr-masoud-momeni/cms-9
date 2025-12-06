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

                <a href="{{ route('shop.orders.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
            </div>
            <div class="panel-footer">Panel Footer</div>


        </div>

    </div>
</div>
@endsection
