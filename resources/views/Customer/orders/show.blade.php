@extends('Customer.layouts.Master')

@section('content')

<style>
    .order-detail-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .order-detail-section:last-of-type {
        border-bottom: 0;
    }

    .order-detail-section h4 {
        margin: 0 0 15px;
        font-weight: 600;
    }

    .order-summary {
        margin: 0;
    }

    .order-summary-item {
        margin-bottom: 10px;
    }

    .order-summary-item:last-child {
        margin-bottom: 0;
    }

    .order-actions .form-group {
        margin-bottom: 12px;
    }

    .order-actions .btn {
        min-width: 150px;
    }

    .order-receipt img {
        max-width: 420px;
        width: 100%;
        height: auto;
        border: 1px solid #ddd;
        padding: 4px;
    }

    .order-items-table th,
    .order-items-table td {
        vertical-align: middle !important;
    }

    .order-sms textarea {
        resize: vertical;
    }

    .order-back {
        margin-top: 5px;
    }

    @media (max-width: 767px) {
        .order-detail-section {
            margin-bottom: 18px;
            padding-bottom: 18px;
        }

        .order-detail-section h4 {
            font-size: 16px;
            margin-bottom: 13px;
        }

        .order-summary-item {
            margin-bottom: 8px;
        }

        .order-actions .form-group {
            margin-bottom: 10px;
        }

        .order-actions .btn,
        .order-sms .btn,
        .order-back {
            width: 100%;
        }

        .order-payment-actions form {
            display: block !important;
            margin: 0 0 8px !important;
        }

        .order-payment-actions .btn {
            width: 100%;
        }

        .order-items-table {
            font-size: 12px;
        }

        .order-items-table th,
        .order-items-table td {
            white-space: nowrap;
        }

        .order-receiver-row > div {
            margin-bottom: 10px;
        }

        .order-receiver-row > div:last-child {
            margin-bottom: 0;
        }

        .panel-body {
            padding: 15px;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">

            <div class="panel-heading">
                <strong>جزئیات سفارش #{{ $order->id }}</strong>
            </div>

            <div class="panel-body">

                {{-- خلاصه سفارش --}}
                <div class="order-detail-section">
                    <h4>خلاصه سفارش</h4>

                    <div class="order-summary">
                        <div class="order-summary-item">
                            <strong>شماره سفارش:</strong>
                            #{{ $order->id }}
                        </div>

                        <div class="order-summary-item">
                            <strong>خریدار:</strong>
                            {{ optional($order->buyer)->name ?? '-' }}
                        </div>

                        <div class="order-summary-item">
                            <strong>مبلغ کالاها:</strong>
                            {{ number_format($order->products->sum(function ($product) {
                                return $product->pivot->price * $product->pivot->quantity;
                            })) }}
                            تومان
                        </div>

                        <div class="order-summary-item">
                            <strong>هزینه ارسال:</strong>
                            {{ ($order->shipping_amount ?? 0) > 0 ? number_format($order->shipping_amount) . ' تومان' : 'رایگان' }}
                        </div>

                        <div class="order-summary-item">
                            <strong>مبلغ نهایی:</strong>
                            {{ number_format($order->total ?? 0) }}
                            تومان
                        </div>

                        <div class="order-summary-item">
                            <strong>وضعیت سفارش:</strong>

                            @if($order->status === 'paid')
                                <span class="label label-success">پرداخت شده</span>
                            @elseif($order->status === 'pending')
                                <span class="label label-warning">در انتظار پرداخت</span>
                            @elseif($order->status === 'shipped')
                                <span class="label label-info">ارسال شده</span>
                            @elseif($order->status === 'completed')
                                <span class="label label-success">انجام شده</span>
                            @elseif($order->status === 'reserved')
                                <span class="label label-warning">رزرو شده</span>
                            @elseif($order->status === 'cancelled')
                                <span class="label label-danger">لغو شده</span>
                            @else
                                <span class="label label-default">{{ $order->status ?? 'نامشخص' }}</span>
                            @endif
                        </div>

                        <div class="order-summary-item">
                            <strong>تاریخ پرداخت:</strong>
                            @if($order->paid_at)
                                {{ $order->paid_at->format('Y/m/d H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                {{-- پرداخت --}}
                @if($order->payment)
                    <div class="order-detail-section">
                        <h4>اطلاعات پرداخت</h4>

                        <div class="order-summary">
                            <div class="order-summary-item">
                                <strong>روش پرداخت:</strong>
                                {{ $order->payment->isCardToCard() ? 'کارت‌به‌کارت' : 'آنلاین' }}
                            </div>

                            <div class="order-summary-item">
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
                            </div>

                            <div class="order-summary-item">
                                <strong>مبلغ پرداخت:</strong>
                                {{ number_format($order->payment->amount ?? 0) }} تومان
                            </div>
                        </div>

                        @if($order->payment->isCardToCard() && $order->payment->receipt)
                            <div class="order-receipt" style="margin-top: 18px;">
                                <strong>رسید پرداخت:</strong>

                                <div style="margin-top: 10px;">
                                    <a href="{{ asset($order->payment->receipt->image) }}" target="_blank">
                                        <img
                                            src="{{ asset($order->payment->receipt->image) }}"
                                            alt="رسید پرداخت"
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
                            <div class="order-payment-actions" style="margin-top: 18px;">
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
                <div class="order-detail-section">
                    <h4>اطلاعات گیرنده</h4>

                    <div class="row order-receiver-row">
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
                <div class="order-detail-section">
                    <h4>اقلام سفارش</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered order-items-table">
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
                                    $itemTotal = $product->pivot->price * $product->pivot->quantity;
                                    $itemsTotal += $itemTotal;
                                @endphp

                                <tr>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>{{ number_format($product->pivot->price) }} تومان</td>
                                    <td>{{ number_format($itemTotal) }} تومان</td>
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
                                    <th colspan="3">جمع اقلام</th>
                                    <th>{{ number_format($itemsTotal) }} تومان</th>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- مدیریت سفارش --}}
                <div class="order-detail-section order-actions">
                    <h4>مدیریت سفارش</h4>

                    <form method="POST" action="{{ route('shop.orders.update', $order) }}" style="margin-bottom: 20px;">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label for="order-status">وضعیت سفارش</label>
                            <select name="status" id="order-status" class="form-control">
                                <option value="pending" @if($order->status === 'pending') selected @endif>در انتظار پرداخت</option>
                                <option value="reserved" @if($order->status === 'reserved') selected @endif>رزرو شده</option>
                                <option value="paid" @if($order->status === 'paid') selected @endif>پرداخت شده</option>
                                <option value="shipped" @if($order->status === 'shipped') selected @endif>ارسال شده</option>
                                <option value="completed" @if($order->status === 'completed') selected @endif>انجام شده</option>
                                <option value="cancelled" @if($order->status === 'cancelled') selected @endif>لغو شده</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            ثبت وضعیت سفارش
                        </button>
                    </form>

                    <form method="POST" action="{{ route('shop.orders.tracking.update', $order) }}">
                        @csrf

                        <div class="form-group">
                            <label for="tracking-code">کد رهگیری مرسوله</label>
                            <input
                                type="text"
                                id="tracking-code"
                                name="tracking_code"
                                value="{{ old('tracking_code', $order->tracking_code) }}"
                                class="form-control"
                                placeholder="کد رهگیری مرسوله"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            ثبت کد رهگیری
                        </button>
                    </form>
                </div>

                {{-- پیامک --}}
                @php
                    $smsPhone = $order->receiver_phone ?? optional($order->buyer)->phone;
                    $smsText = "مشتری گرامی، سفارش شما با شماره #{$order->id}";
                    if ($order->tracking_code) {
                        $smsText .= " با کد رهگیری {$order->tracking_code}";
                    }
                    $smsText .= " ثبت شده است.";
                @endphp

                <div class="order-detail-section order-sms">
                    <h4>پیامک به مشتری</h4>

                    <div class="form-group">
                        <label for="sms-phone">شماره موبایل</label>
                        <input
                            type="text"
                            id="sms-phone"
                            class="form-control"
                            value="{{ $smsPhone ?? '' }}"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label for="sms-text">متن پیام</label>
                        <textarea id="sms-text" class="form-control" rows="4">{{ $smsText }}</textarea>
                    </div>

                    <a
                        href="{{ $smsPhone ? 'sms:' . $smsPhone . '?body=' . rawurlencode($smsText) : '#' }}"
                        id="send-sms-button"
                        class="btn btn-primary"
                    >
                        ارسال پیامک
                    </a>

                    @if(!$smsPhone)
                        <p class="text-muted" style="margin-top: 8px;">
                            شماره موبایل مشتری ثبت نشده است.
                        </p>
                    @endif
                </div>

                <a
                    href="{{ route('shop.orders.index') }}"
                    class="btn btn-default order-back"
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
    const smsText = document.getElementById('sms-text');
    const sendSmsButton = document.getElementById('send-sms-button');

    if (!smsText || !sendSmsButton) {
        return;
    }

    const phone = @json($smsPhone);

    sendSmsButton.addEventListener('click', function (event) {
        if (!phone) {
            event.preventDefault();
            alert('شماره موبایل مشتری ثبت نشده است.');
            return;
        }

        sendSmsButton.href = 'sms:' + phone + '?body=' + encodeURIComponent(smsText.value);
    });
});
</script>
@endsection
