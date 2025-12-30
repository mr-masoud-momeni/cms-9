<h2>کد تأیید ارسال‌شده به {{ $phone }}</h2>

<form method="POST" action="{{ route('buyer.otp.verify') }}">
    @csrf

    <input type="hidden" name="phone" value="{{ $phone }}">
    <input type="hidden" name="purpose" value="{{ $purpose }}">

    <input name="code" placeholder="کد تایید">
    <button>تایید</button>
</form>

@if(session('otp_expires_at'))
    <p>
        ارسال مجدد تا
        {{ max(0, session('otp_expires_at')->diffInSeconds(now())) }}
        ثانیه دیگر
    </p>
@endif
