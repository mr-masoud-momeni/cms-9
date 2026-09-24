@extends('Customer.layouts.Master')

@php
    use App\Models\Order;
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">لیست سفارش‌ها</div>

                <div class="panel-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
                        <li class="{{ !$paymentMethod ? 'active' : '' }}">
                            <a href="{{ route('shop.orders.index') }}">همه</a>
                        </li>
                        <li class="{{ $paymentMethod === 'online' ? 'active' : '' }}">
                            <a href="{{ route('shop.orders.index', ['payment_method' => 'online']) }}">آنلاین</a>
                        </li>
                        <li class="{{ $paymentMethod === 'card_to_card' ? 'active' : '' }}">
                            <a href="{{ route('shop.orders.index', ['payment_method' => 'card_to_card']) }}">کارت‌به‌کارت</a>
                        </li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>شماره سفارش</th>
                                <th>خریدار</th>
                                <th>روش پرداخت</th>
                                <th>وضعیت پرداخت</th>
                                <th>وضعیت سفارش</th>
                                <th>مبلغ</th>
                                <th>تاریخ ثبت</th>
                                <th width="70">جزئیات</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ optional($order->buyer)->name ?? '-' }}</td>

                                    <td>
                                        @if(optional($order->payment)->method === 'card_to_card')
                                            <span class="label label-warning">کارت‌به‌کارت</span>
                                        @elseif(optional($order->payment)->method === 'online')
                                            <span class="label label-info">آنلاین</span>
                                        @else
                                            <span class="label label-default">نامشخص</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if(optional($order->payment)->method === 'card_to_card')
                                            @if(optional($order->payment)->status === 'waiting_confirmation')
                                                <span class="label label-warning">در انتظار تأیید</span>
                                            @elseif(optional($order->payment)->status === 'paid')
                                                <span class="label label-success">تأیید شده</span>
                                            @elseif(optional($order->payment)->status === 'rejected')
                                                <span class="label label-danger">رد شده</span>
                                            @else
                                                <span class="label label-default">
                                                    {{ optional($order->payment)->status ?? 'نامشخص' }}
                                                </span>
                                            @endif
                                        @elseif(optional($order->payment)->status === 'paid')
                                            <span class="label label-success">پرداخت شده</span>
                                        @elseif($order->payment)
                                            <span class="label label-default">{{ $order->payment->status }}</span>
                                        @else
                                            <span class="label label-default">بدون پرداخت</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($order->status === Order::STATUS_PAID)
                                            <span class="label label-success">پرداخت شده</span>
                                        @elseif($order->status === Order::STATUS_PENDING)
                                            <span class="label label-warning">در انتظار پرداخت</span>
                                        @elseif($order->status === Order::STATUS_RESERVED)
                                            <span class="label label-warning">رزرو شده</span>
                                        @elseif($order->status === Order::STATUS_SHIPPED)
                                            <span class="label label-info">ارسال شده</span>
                                        @elseif($order->status === Order::STATUS_COMPLETED)
                                            <span class="label label-success">انجام شده</span>
                                        @elseif($order->status === Order::STATUS_CANCELLED)
                                            <span class="label label-danger">لغو شده</span>
                                        @else
                                            <span class="label label-default">{{ $order->status ?? 'نامشخص' }}</span>
                                        @endif
                                    </td>

                                    <td>{{ number_format($order->total ?? 0) }} تومان</td>
                                    <td>{{ $order->created_at }}</td>

                                    <td class="text-center">
                                        <a href="{{ route('shop.orders.show', $order) }}" title="مشاهده سفارش">
                                            <i class="fa fa-eye fa-lg" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        سفارشی برای این فروشگاه ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
