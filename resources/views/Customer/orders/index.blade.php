@extends('Customer.layouts.Master')

@section('content')

<div class="row">

    <div class="col-lg-12">

        <div class="panel panel-default">

            <div class="panel-heading">
                لیست سفارش‌ها
            </div>

            <div class="panel-body">

                <div class="table-responsive">

                    <table class="table table-striped">

                        <thead>
                        <tr>
                            <th>شماره سفارش</th>
                            <th>خریدار</th>
                            <th>گیرنده</th>
                            <th>موبایل</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ پرداخت</th>
                            <th width="50px">جزئیات</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($orders as $order)

                            <tr>

                                {{-- شماره سفارش --}}
                                <td>
                                    #{{ $order->id }}
                                </td>

                                {{-- خریدار --}}
                                <td>
                                    {{ optional($order->buyer)->name ?? '-' }}
                                </td>

                                {{-- گیرنده --}}
                                <td>
                                    {{ $order->receiver_name ?? '-' }}
                                </td>

                                {{-- شماره گیرنده --}}
                                <td>
                                    {{ $order->receiver_phone ?? '-' }}
                                </td>

                                {{-- مبلغ --}}
                                <td>
                                    {{ number_format($order->total ?? optional($order->payment)->amount ?? 0) }}
                                </td>

                                {{-- وضعیت پرداخت --}}
                                <td>
                                    @switch($order->status)

                                        @case('paid')
                                            <span class="label label-success">
                                                پرداخت شده
                                            </span>
                                            @break

                                        @case('pending')
                                            <span class="label label-warning">
                                                در انتظار پرداخت
                                            </span>
                                            @break

                                        @default
                                            <span class="label label-default">
                                                {{ $order->status ?: 'نامشخص' }}
                                            </span>

                                    @endswitch
                                </td>

                                {{-- تاریخ پرداخت --}}
                                <td>
                                    @if($order->paid_at)
                                        {{ $order->paid_at->format('Y/m/d H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- جزئیات --}}
                                <td>
                                    <a href="{{ route('shop.orders.show', $order->id) }}">
                                        <i
                                            class="fa fa-2x fa-pencil-square-o"
                                            aria-hidden="true"
                                        ></i>
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    سفارشی وجود ندارد.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="panel-footer">
                تعداد سفارش‌ها: {{ $orders->count() }}
            </div>

        </div>

    </div>

</div>

@endsection