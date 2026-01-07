<form method="POST" action="{{route('buyer.login.submit')}}">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">
    <input name="password" type="password" placeholder="رمز عبور">
    <button>ورود</button>
</form>

<form method="POST" action="{{route('buyer.forgot.form')}}">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">
    <button>فراموشی رمز</button>
</form>
