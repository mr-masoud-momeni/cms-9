@component('mail::message')
    ایمیل فعال سازی
    @component('mail::button',['url'=>route('activation.account',$activationCode)])
        فعال سازی اکانت
    @endcomponent

@endcomponent