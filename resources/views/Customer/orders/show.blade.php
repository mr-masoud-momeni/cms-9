@extends('Customer.layouts.Master')

@section('content')

<div class="row">

    <div class="col-lg-12">

        <div class="panel panel-default">

            <div class="panel-heading">
                جزئیات سفارش #{{ $order->id }}
            </div>

            <div class="panel-body">

                {{-- اطلاعات سفارش --}}
                <div class="mb-4">

                    <h4>اطلاعات سفارش</h4>

                    <p>
                        <strong>شماره سفارش:</strong>
                        #{{ $order->id }}
                    </p>

                    <p>
                        <strong>خریدار:</strong>
                        {{ optional($order->buyer)->name ?? '-' }}
                    </p>

                    <p>
                        <strong>مبلغ کالاها:</strong>
                        {{ number_format($order->products->sum(function ($product) {
                            return $product->pivot->price * $product->pivot->quantity;
                        })) }}
                        تومان
                    </p>

                    <p>
                        <strong>هزینه ارسال:</strong>
                        {{ ($order->shipping_amount ?? 0) > 0 ? number_format($order->shipping_amount) . ' تومان' : 'رایگان' }}
                    </p>

                    <p>
                        <strong>مبلغ نهایی:</strong>
                        {{ number_format($order->total ?? 0) }}
                        تومان
                    </p>

                    <p>
                        <strong>وضعیت:</strong>

                        @if($order->status === 'paid')

                            <span class="label label-success">
                                پرداخت شده
                            </span>

                        @elseif($order->status === 'pending')

                            <span class="label label-warning">
                                در انتظار پرداخت
                            </span>

                        @elseif($order->status === 'shipped')

                            <span class="label label-info">
                                ارسال شده
                            </span>

                        @elseif($order->status === 'completed')

                            <span class="label label-success">
                                انجام شده
                            </span>

                        @elseif($order->status === 'reserved')

                            <span class="label label-warning">
                                رزرو شده
                            </span>

                        @elseif($order->status === 'cancelled')

                            <span class="label label-danger">
                                لغو شده
                            </span>

                        @else

                            <span class="label label-default">
                                {{ $order->status ?? 'نامشخص' }}
                            </span>

                        @endif

                    </p>

                    <p>
                        <strong>تاریخ پرداخت:</strong>

                        @if($order->paid_at)
                            {{ $order->paid_at->format('Y/m/d H:i') }}
                        @else
                            -
                        @endif

                    </p>

                </div>


                {{-- پرداخت --}}
                @if($order->payment)
                    <div class="mb-4">
                        <h4>اطلاعات پرداخت</h4>

                        <p>
                            <strong>روش پرداخت:</strong>
                            {{ $order->payment->isCardToCard() ? 'کارت‌به‌کارت' : 'آنلاین' }}
                        </p>

                        <p>
                            <strong>وضعیت پرداخت:</strong>
                            @if($order->payment->status === 'waiting_confirmation')
                                <span class="label label-warning">در انتظار تأیید</span>
                            @elseif($order->payment->status === 'paid')
                                <span class="label label-success">تأیید شده</span>
                            @elseif($order->payment->status === 'rejected')
                                <span class="label label-danger">رد شده</span>
                            @else
                                <span class="label label-default">{{ $order->payment->status }}</span>
                            @endif
                        </p>

                        <p>
                            <strong>مبلغ پرداخت:</strong>
                            {{ number_format($order->payment->amount ?? 0) }} تومان
                        </p>

                        @if($order->payment->isCardToCard() && $order->payment->receipt)
                            <div style="margin-top: 20px;">
                                <strong>رسید پرداخت:</strong>

                                <div style="margin-top: 10px;">
                                    <a href="{{ asset($order->payment->receipt->image) }}" target="_blank">
                                        <img
                                            src="{{ asset($order->payment->receipt->image) }}"
                                            alt="رسید پرداخت"
                                            style="max-width: 420px; width: 100%; height: auto; border: 1px solid #ddd; padding: 4px;"
                                        >
                                    </a>
                                </div>

                                @if($order->payment->receipt->tracking_code)
                                    <p style="margin-top: 10px;">
                                        <strong>کد پیگیری واریز:</strong>
                                        {{ $order->payment->receipt->tracking_code }}
                                    </p>
                                @endif

                                @if($order->payment->receipt->description)
                                    <p>
                                        <strong>توضیحات مشتری:</strong>
                                        {{ $order->payment->receipt->description }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if($order->payment->isCardToCard() && $order->payment->status === 'waiting_confirmation')
                            <div style="margin-top: 20px;">
                                <form method="POST" action="{{ route('shop.orders.payment.approve', $order) }}" style="display:inline-block; margin-left:8px;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        تأیید پرداخت
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('shop.orders.payment.reject', $order) }}" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        رد پرداخت
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- اطلاعات گیرنده --}}
                <div class="mb-4">

                    <h4>اطلاعات گیرنده</h4>

                    <div class="row">

                        <div class="col-md-6">
                            <strong>نام گیرنده:</strong>
                            {{ $order->receiver_name ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>شماره موبایل:</strong>
                            {{ $order->receiver_phone ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>استان:</strong>
                            {{ $order->receiver_province ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>شهر:</strong>
                            {{ $order->receiver_city ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>کد پستی:</strong>
                            {{ $order->receiver_postal_code ?? '-' }}
                        </div>

                        <div class="col-md-12">
                            <strong>آدرس:</strong>
                            {{ $order->receiver_address ?? '-' }}
                        </div>

                    </div>

                </div>


                {{-- اقلام سفارش --}}
                <div class="mb-4">

                    <h4>اقلام سفارش</h4>

                    <div class="table-responsive">

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

                            @php
                                $itemsTotal = 0;
                            @endphp

                            @forelse($order->products as $product)

                                @php
                                    $itemTotal =
                                        $product->pivot->price *
                                        $product->pivot->quantity;

                                    $itemsTotal += $itemTotal;
                                @endphp

                                <tr>

                                    <td>
                                        {{ $product->title }}
                                    </td>

                                    <td>
                                        {{ $product->pivot->quantity }}
                                    </td>

                                    <td>
                                        {{ number_format($product->pivot->price) }}
                                        تومان
                                    </td>

                                    <td>
                                        {{ number_format($itemTotal) }}
                                        تومان
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center">
                                        محصولی در این سفارش وجود ندارد.
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                            @if($order->products->isNotEmpty())

                                <tfoot>

                                <tr>
                                    <th colspan="3">
                                        جمع اقلام
                                    </th>

                                    <th>
                                        {{ number_format($itemsTotal) }}
                                        تومان
                                    </th>
                                </tr>

                                </tfoot>

                            @endif

                        </table>

                    </div>

                </div>


                {{-- تغییر وضعیت سفارش --}}
                <div class="mb-4">

                    <h4>مدیریت سفارش</h4>

                    <form
                        method="POST"
                        action="{{ route('shop.orders.update', $order) }}"
                    >

                        @csrf
                        @method('PATCH')


                    <div class="mb-3">

                        <label for="order-status">
                            وضعیت سفارش
                        </label>
                        @php
                            $status = $order->status;
                        @endphp
                        <select name="status" id="order-status">

                            <option value="pending" @if($status == 'pending') selected @endif>
                                در انتظار پرداخت
                            </option>

                            <option value="paid" @if($status == 'paid') selected @endif>
                                پرداخت شده
                            </option>

                            <option value="shipped" @if($status == 'shipped') selected @endif>
                                ارسال شده
                            </option>

                            <option value="completed" @if($status == 'completed') selected @endif>
                                انجام شده
                            </option>

                        </select>

                    </div>


                        {{-- کد رهگیری --}}
                        <div
                            class="mb-3"
                            id="tracking-wrapper"
                            style="{{ $order->status === 'shipped' ? '' : 'display:none;' }}"
                        >

                            <label for="tracking_code">
                                کد رهگیری پستی
                            </label>

                            <input
                                type="text"
                                id="tracking_code"
                                name="tracking_code"
                                value="{{ old('tracking_code', $order->tracking_code) }}"
                                class="form-control"
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            ثبت تغییرات
                        </button>

                    </form>

                </div>


                {{-- بازگشت --}}
                <a
                    href="{{ route('shop.orders.index') }}"
                    class="btn btn-secondary"
                >
                    بازگشت به لیست سفارش‌ها
                </a>

            </div>

            <div class="panel-footer">
                سفارش #{{ $order->id }}
            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const statusSelect = document.getElementById('order-status');
    const trackingBox = document.getElementById('tracking-wrapper');

    function toggleTracking() {

        if (statusSelect.value === 'shipped') {
            trackingBox.style.display = 'block';
        } else {
            trackingBox.style.display = 'none';
        }

    }

    toggleTracking();

    statusSelect.addEventListener('change', toggleTracking);

});

</script>

@endsection