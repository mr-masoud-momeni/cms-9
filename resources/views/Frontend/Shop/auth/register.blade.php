@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="content container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-md-6 mx-auto text-center">
                <form method="POST" action="{{ route('buyer.register.submit') }}">
                    @csrf
                    <input name="name" class="form-control mb-3" placeholder="نام (اختیاری)">
                    <input name="email" class="form-control mb-3" placeholder="ایمیل (اختیاری)">
                    <input name="password" class="form-control mb-3" type="password" placeholder="رمز عبور">
                    <button>ثبت‌نام</button>
                </form>
            </div>
        </div>
    </div>
@endsection
