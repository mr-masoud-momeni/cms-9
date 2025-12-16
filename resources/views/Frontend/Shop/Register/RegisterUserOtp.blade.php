@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- صفحه محصول -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- ثبت نام یوزر -->
            <div class="col-md-6">
                <!-- فرم ثبت‌نام -->
                <form action="{{ route('buyer.register') }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <input type="text" id="phone" class="form-control" placeholder="تلفن همراه" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="text-center mt-3"><button type="submit" id="sendOtp" class="btn btn-success">{{ __('ui.register')}}</button></div>
                </form>
            </div>
            <!-- خالی -->
            <div class="col-md-6 text-center">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    let seconds = 120;
    const btn = document.getElementById('sendOtp');

    const timer = setInterval(() => {
        seconds--;
        btn.innerText = `ارسال کد (${seconds})`;

        if (seconds <= 0) {
            clearInterval(timer);
            btn.innerText = 'ارسال مجدد';
            btn.disabled = false;
        }
    }, 1000);
</script>
@endsection
