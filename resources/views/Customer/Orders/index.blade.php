@extends('Customer.layouts.Master')

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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>شماره سفارش</th>
                                <th>خریدار</th>
                                <th>مبلغ</th>
                                <th>وضعیت</th>
                                <th>تاریخ ثبت</th>
                                <th width="70">جزئیات</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ optional($order->buyer)->name ?? '-' }}</td>
                                    <td>{{ number_format($order->total ?? 0) }} تومان</td>
                                    <td>
                                        @if($order->status === 'paid')
                                            <span class="label label-success">پرداخت شده</span>
                                        @elseif($order->status === 'pending')
                                            <span class="label label-warning">در انتظار پرداخت</span>
                                        @elseif($order->status === 'shipped')
                                            <span class="label label-info">ارسال شده</span>
                                        @elseif($order->status === 'completed')
                                            <span class="label label-success">انجام شده</span>
                                        @else
                                            <span class="label label-default">{{ $order->status ?? 'نامشخص' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('shop.orders.show', $order) }}" title="مشاهده سفارش">
                                            <i class="fa fa-eye fa-lg" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
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
