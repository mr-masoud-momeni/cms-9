@extends('Frontend.layouts.Master')
@section('Main')

    <main id="main">

        <!-- ======= Breadcrumbs ======= -->
        <section id="breadcrumbs" class="breadcrumbs">
            <div class="container">

                <ol>
                    <li><a href="index.html">Home</a></li>
                    <li>Inner Page</li>
                </ol>
                <h2>سفارشات</h2>

            </div>
        </section><!-- End Breadcrumbs -->

        <section class="inner-page pt-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <div class="table-responsive">
                                <table class="table user-list">
                                    <thead>
                                    <tr>
                                        <th><span>محصول</span></th>
                                        <th class="text-center"><span>قیمت</span></th>
                                        <th class="text-center"><span>تعداد</span></th>
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
                                                    $productPrice =  $product->price * $product->pivot->amount;
                                                    $totalPrice = $productPrice+$totalPrice;
                                                @endphp
                                                <td>
                                                    <img src="{{ asset($product->images['thum'])}}" alt="">
                                                    <a href="#" class="user-link">{{$product->title}}</a>
                                                </td>
                                                <td class="text-center">
                                                    {{$product->price}}
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-default">{{$product->pivot->amount}}</span>
                                                </td>
                                                <td style="width: 10%; " class="text-center">
                                                    <a href="#" class="table-link danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                        </svg>
                                                    </a>
                                                </td>
                                            @endforeach
                                        </tr>
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
                            <form method="post" action="{{route('buy.add')}}">
                                {{csrf_field()}}
                                <input type="hidden" name="price" value="{{$totalPrice}}">
                                <button type="submit">تکمیل خرید</button>
                            </form>
                            <ul class="pagination pull-right">
                                <li><a href="#"><i class="fa fa-chevron-right"></i></a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li><a href="#">5</a></li>
                                <li><a href="#"><i class="fa fa-chevron-left"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection

