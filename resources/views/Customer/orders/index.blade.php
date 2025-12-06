@extends('Customer.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">لیست محصولات</div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>نام خریدار</th>
                            <th>مبلغ</th>
                            <th>وضعیت پرداخت</th>
                            <th>تاریخ پرداخت</th>
                            <th width="50px">جزئیات</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($orders as $order)

                            <tr class="item{{$order->id}}">
                                <td>{{$order->buyer->name}}</td>
                                <td>{{ optional($order->payment)->amount ?? 0 }}</td>
                                <td>{{$order->status ? 'پرداخت شده' : 'پرداخت نشده'}}</td>
                                <td>{{ $order->paid_at ? $order->paid_at : '-' }}</td>
                                <td>
                                    <a href="{{ route('shop.orders.show', $order->id) }}"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">Panel Footer</div>


            </div>

        </div>
    </div>

@endsection
