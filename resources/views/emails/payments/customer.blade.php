@component('mail::message')
خرید شما با موفقیت انجام شد

@foreach($order->products as $product)
    - {{ $product->name }} × {{ $product->pivot->quantity }} ({{ number_format($product->pivot->price) }} تومان)
@endforeach

**جمع کل:** {{ number_format($order->total) }} تومان

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
