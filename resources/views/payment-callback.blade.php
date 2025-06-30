@if ($status === 'OK')
    <form method="POST" action="/verify-payment">
        @csrf
        <input type="hidden" name="authority" value="{{ $authority }}">
        <button type="submit">تایید پرداخت</button>
    </form>
@else
    <p style="color:red;">پرداخت ناموفق بود</p>
@endif
