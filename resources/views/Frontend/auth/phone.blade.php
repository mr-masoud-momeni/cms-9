<form method="POST" action="{{ url('/buyer/auth/phone') }}">
    @csrf

    <input
        type="text"
        name="phone"
        placeholder="شماره موبایل"
        required
    >

    @error('phone')
    <div>{{ $message }}</div>
    @enderror

    <button type="submit">
        ادامه
    </button>
</form>
