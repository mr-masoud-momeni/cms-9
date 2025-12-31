<form method="POST" action="{{ route('buyer.register.submit') }}">
    @csrf

    <input name="name" placeholder="نام (اختیاری)">
    <input name="email" placeholder="ایمیل (اختیاری)">
    <input name="password" type="password" placeholder="رمز عبور">

    <button>ثبت‌نام</button>
</form>
