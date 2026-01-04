<form method="POST" action="{{route('buyer.otp.login')}}">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">
    <input name="password" type="password" placeholder="رمز عبور">
    <button>ورود</button>
</form>

<form method="POST" action="/buyer/auth/forgot">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">
    <button>فراموشی رمز</button>
</form>
