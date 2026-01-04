@extends('Frontend.layouts.Master')

@section('Main')
    <div class="auth-box">

        <h2>تعیین رمز عبور جدید</h2>

        {{-- خطاهای کلی --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('buyer.reset.submit') }}">
            @csrf

            <div class="form-group">
                <label>رمز عبور جدید</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                    minlength="6"
                    placeholder="رمز عبور جدید"
                >
            </div>

            <div class="form-group">
                <label>تکرار رمز عبور</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                    minlength="6"
                    placeholder="تکرار رمز عبور"
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                تغییر رمز عبور
            </button>
        </form>

    </div>
@endsection
