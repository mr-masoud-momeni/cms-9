@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <div class="content container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-md-6 mx-auto text-center">
                <form method="POST" action="{{ route('buyer.submit.phone') }}">
                    @csrf

                    <input class="form-control mb-3" type="text" name="phone" placeholder="شماره موبایل" required>
                    @error('phone')
                    <div>{{ $message }}</div>
                    @enderror
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button class="btn btn-primary w-100" type="submit">ادامه</button>
                </form>
            </div>
        </div>
    </div>
@endsection
