@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- داشبورد یوزر -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- داشبورد یوزر -->
            <div class="col-md-12">
                @include('Frontend.layouts.errors')
                @include('Frontend.layouts.message')
                @isset($orders)
                    <div class="main-box clearfix">
                        <div class="table-responsive">

                            <table class="table user-list">
                                <thead>
                                <tr>
                                    <th><span>محصول</span></th>
                                    <th class="text-center"><span>قیمت</span></th>
                                    <th class="text-center"><span>تعداد</span></th>
                                    <th class="text-center"><span>قیمت تعداد</span></th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $totalPrice = 0;
                                @endphp
                                @foreach($orders as $order)
                                    <tr>
                                        @foreach($order->products as $product)
                                            @php
                                                $productPrice = $product->pivot->price*$product->pivot->quantity;
                                                $totalPrice = $productPrice+$totalPrice;
                                            @endphp
                                            <td>
                                                <img width="100" src="{{ asset($product->images['thum'])}}" alt="">
                                                <a href="#" class="user-link">{{$product->title}}</a>
                                            </td>
                                            <td class="text-center">
                                                {{$product->price}}
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-default">{{$order->total}}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-default">{{$order->amount}}</span>
                                            </td>
                                            <td style="width: 10%; " class="text-center">
                                                <a href="#" class="table-link danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                    </tr>
                                @endforeach

                                    @if($loop->last)
                                        <tr>
                                            <td>
                                                جمع کل
                                            </td>
                                            <td class="text-center">
                                                {{$totalPrice}}
                                            </td>
                                            <td class="text-center">
                                            </td>
                                            <td class="text-center">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form method="post" action="/start-payment">
                            {{csrf_field()}}
                            <input type="hidden" name="amount" value="{{$totalPrice}}">
                            <button type="submit">تکمیل خرید</button>
                        </form>
                    </div>
                @endisset
            </div>

        </div>
    </div>
@endsection
