@extends('Customer.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش درگاه پرداخت</h3></div>
                <div class="panel-body">


                    <div class="row">

                        <div class="col-md-4 col-sm-12">
                        <form action="{{ route('gateways.store') }}" method="POST">
                            @csrf

                                <div class="form-group">
                                    <label for="title">عنوان درگاه</label>
                                    <input type="text" class="form-control" name="title"
                                           value="{{ old('title') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">Terminal ID</label>
                                    <input type="text" class="form-control" name="terminal_id"
                                           value="{{ old('terminal_id') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">Username</label>
                                    <input type="text" class="form-control" name="username"
                                           value="{{ old('username') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">Password</label>
                                    <input type="password" class="form-control" name="password"
                                           value="{{ old('password') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">WSDL URL</label>
                                    <input type="url" class="form-control" name="wsdl_url"
                                           value="{{ old('wsdl_url') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">Gateway URL</label>
                                    <input type="url" class="form-control" name="gateway_url"
                                           value="{{ old('gateway_url') }}">
                                </div>

                            <button type="submit">ذخیره درگاه</button>
                        </form>
                        </div>

                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>

        </div>
    </div>

@endsection
