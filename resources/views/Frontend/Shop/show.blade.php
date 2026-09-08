<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->title }}</title>
    <style>
        *{box-sizing:border-box} body{margin:0;background:#fafafa;color:#171717;font-family:Tahoma,Arial,sans-serif} a{color:inherit;text-decoration:none}.product-page{max-width:935px;margin:0 auto;padding:28px 18px 50px}.product-card{background:#fff;border:1px solid #dbdbdb;border-radius:12px;overflow:hidden;margin-top:18px}.product-layout{display:grid;grid-template-columns:1fr 1fr}.product-media{background:#f7f7f7;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;overflow:hidden}.product-media img{width:100%;height:100%;object-fit:cover}.product-placeholder{color:#888}.product-info{display:flex;flex-direction:column}.product-content{padding:28px 24px}.product-title{font-size:25px;line-height:1.5;margin:0 0 16px}.product-description{color:#444;line-height:2;margin-bottom:24px}.product-price{font-size:22px;font-weight:800;margin-bottom:20px}.product-actions{display:flex;gap:10px}.quantity{width:82px;border:1px solid #dbdbdb;border-radius:8px;text-align:center;font-weight:600}.btn-buy{flex:1;border:0;border-radius:8px;background:#171717;color:#fff;font-weight:700;padding:12px 18px;cursor:pointer;font-family:inherit}.product-meta{border-top:1px solid #efefef;padding:16px 24px;color:#737373;font-size:13px}@media(max-width:767px){.product-page{padding:16px 10px 35px}.product-card{margin-top:12px;border-left:0;border-right:0;border-radius:0}.product-layout{grid-template-columns:1fr}.product-content{padding:22px 18px}.product-title{font-size:22px}.product-meta{padding:14px 18px}}
    </style>
</head>
<body>
<main class="product-page">
    @include('Frontend.layouts.shop-profile-header')
    <div class="product-card">
        <div class="product-layout">
            <div class="product-media">
                @if(!empty($product->images['thum']))
                    <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}">
                @else
                    <span class="product-placeholder">تصویری برای این محصول وجود ندارد</span>
                @endif
            </div>
            <div class="product-info">
                <div class="product-content">
                    <h1 class="product-title">{{ $product->title }}</h1>
                    <div class="product-description">{!! $product->body !!}</div>
                    <div class="product-price">{{ $product->price }}</div>
                    <form method="post" action="{{ route('order.store') }}" class="AddProduct">
                        {!! csrf_field() !!}
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="product-actions">
                            <input class="quantity" type="number" name="count_product" value="1" min="1">
                            <button type="submit" class="btn-buy">افزودن به سبد خرید</button>
                        </div>
                    </form>
                </div>
                <div class="product-meta">اشتراک‌گذاری این محصول با دوستانت ✨</div>
            </div>
        </div>
    </div>
</main>
<script>
jQuery(function($){$('.AddProduct').on('submit',function(e){e.preventDefault();var form=$(this);$.ajax({url:form.attr('action'),type:'POST',dataType:'JSON',data:form.serialize(),success:function(data){if($.isEmptyObject(data.error)){var order=Number($('#cart-val').attr('value'))||0;$('#cart-val').attr('value',order+(data.success||0));if(typeof showToast==='function')showToast(data.message,'success');else alert(data.message)}else{if(typeof showToast==='function')showToast(data.message,'danger');else alert(data.message)}},error:function(xhr){var message=xhr.responseJSON?.message||'افزودن محصول به سبد خرید انجام نشد.';if(typeof showToast==='function')showToast(message,'danger');else alert(message)}})})});
</script>
</body>
</html>
