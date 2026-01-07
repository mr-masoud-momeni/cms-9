@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="content container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-md-6 mx-auto text-center">
                <form method="POST" action="{{route('buyer.login.submit')}}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <input name="password" class="form-control mb-3" type="password" placeholder="رمز عبور">
                    <button class="btn btn-primary w-100" >ورود</button>
                </form>
                <br>
                <form method="POST" action="{{route('buyer.forgot.form')}}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <button class="btn btn-default w-100" >فراموشی رمز</button>
                </form>
            </div>
        </div>
    </div>
@endsection
