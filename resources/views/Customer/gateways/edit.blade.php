@extends('Customer.layouts.Master')

@section('content')

    <div class="row">
        <div class="col-lg-12">

            @include('Customer.layouts.errors')

            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3>تنظیمات پرداخت</h3>
                </div>

                <div class="panel-body">

                    {{-- پرداخت آنلاین --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>پرداخت آنلاین</h4>
                        </div>

                        <div class="panel-body">

                            <form action="{{ route('shop.gateways.store') }}" method="POST">
                                @csrf

                                <div class="row">

                                    <div class="col-md-4 col-sm-12">

                                        <div class="form-group">
                                            <label for="title">عنوان درگاه</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="title"
                                                   value="{{ old('title') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="terminal_id">Terminal ID</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="terminal_id"
                                                   value="{{ old('terminal_id') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="username"
                                                   value="{{ old('username') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password"
                                                   class="form-control"
                                                   name="password"
                                                   value="{{ old('password') }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4 col-sm-12">

                                        <div class="form-group">
                                            <label for="wsdl_url">WSDL URL</label>
                                            <input type="url"
                                                   class="form-control"
                                                   name="wsdl_url"
                                                   value="{{ old('wsdl_url') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="gateway_url">Gateway URL</label>
                                            <input type="url"
                                                   class="form-control"
                                                   name="gateway_url"
                                                   value="{{ old('gateway_url') }}">
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            ذخیره درگاه
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>


                    {{-- کارت به کارت --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>پرداخت کارت به کارت</h4>
                        </div>

                        <div class="panel-body">

                            <form action="{{ route('shop.card-to-card.store') }}" method="POST">
                                @csrf

                                <div class="row">

                                    <div class="col-md-4 col-sm-12">

                                        <div class="form-group">
                                            <label for="card_number">شماره کارت</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="card_number"
                                                   value="{{ old('card_number') }}"
                                                   placeholder="مثلاً 6037...">
                                        </div>

                                    </div>

                                    <div class="col-md-4 col-sm-12">

                                        <div class="form-group">
                                            <label for="owner_name">نام صاحب کارت</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="owner_name"
                                                   value="{{ old('owner_name') }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4 col-sm-12">

                                        <div class="form-group">
                                            <label for="iban">شماره شبا</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="iban"
                                                   value="{{ old('iban') }}"
                                                   placeholder="IR...">
                                        </div>

                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary">
                                    ذخیره اطلاعات کارت
                                </button>

                            </form>

                        </div>
                    </div>


                    {{-- اتصال به بله --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>اعلان پرداخت‌ها</h4>
                        </div>

                        <div class="panel-body">

                            <p>
                                برای دریافت اعلان پرداخت‌های کارت به کارت در پیام‌رسان بله،
                                حساب بله خود را به فروشگاه متصل کنید.
                            </p>

                            @if($baleConnection)

                                <p>
                                    <strong>وضعیت:</strong>
                                    <span class="text-success">
                                        متصل است
                                    </span>
                                </p>

                                <form action="{{ route('shop.bale.disconnect') }}" method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="btn btn-danger">
                                        قطع اتصال
                                    </button>
                                </form>

                            @elseif(session('bale_connection_token'))

                                <p>
                                    <strong>وضعیت:</strong>
                                    <span class="text-warning">
                                        در انتظار اتصال
                                    </span>
                                </p>

                                <p>
                                    کد زیر را در بات Shop Maker در بله ارسال کنید:
                                </p>

                                <div style="font-size: 24px; font-weight: bold; margin: 15px 0;">
                                    {{ session('bale_connection_token') }}
                                </div>

                                <p>
                                    این کد تا ۱۰ دقیقه معتبر است.
                                </p>

                            @else

                                <p>
                                    <strong>وضعیت:</strong>
                                    <span class="text-danger">
                                        متصل نیست
                                    </span>
                                </p>

                                <form action="{{ route('shop.bale.connect') }}" method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="btn btn-primary">
                                        اتصال به بله
                                    </button>
                                </form>

                            @endif

                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    تنظیمات پرداخت فروشگاه
                </div>

            </div>

        </div>
    </div>

@endsection