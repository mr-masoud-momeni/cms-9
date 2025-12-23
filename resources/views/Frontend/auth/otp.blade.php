<h2>{{ ucfirst($purpose) }} OTP</h2>

@if(!session('otp_sent'))

    <form method="POST" action="{{ route('buyer.otp.send', $purpose) }}">
        @csrf
        <input name="mobile" placeholder="Mobile">
        <button>Send Code</button>
    </form>

@else

    <form method="POST" action="{{ route('buyer.otp.verify', $purpose) }}">
        @csrf
        <input name="code" placeholder="OTP Code">
        <button>Verify</button>
    </form>

    <p>
        ارسال مجدد تا
        {{ max(0, 60 - (now()->timestamp - session('otp_sent_at'))) }}
        ثانیه دیگر
    </p>

@endif
