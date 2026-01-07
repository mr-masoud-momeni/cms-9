@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="content container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-md-6 mx-auto text-center">
                <form method="POST" action="{{ route('buyer.reset.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label>رمز عبور جدید</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="رمز عبور جدید">
                    </div>

                    <div class="form-group">
                        <label>تکرار رمز عبور</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6" placeholder="تکرار رمز عبور">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        تغییر رمز عبور
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
