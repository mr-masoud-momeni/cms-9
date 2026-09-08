<header class="shop-profile-header">
    <div class="shop-profile-header__inner">
        <a href="{{ url('/') }}" class="shop-profile-header__brand">
            <div class="shop-profile-header__avatar">
                @if(!empty($shop->logo))
                    <img src="{{ $shop->logo }}" alt="{{ $shop->name }}">
                @else
                    <span>{{ mb_substr($shop->name ?? 'S', 0, 1) }}</span>
                @endif
            </div>
            <div class="shop-profile-header__info">
                <strong>{{ $shop->name ?? '' }}</strong>
                @if(!empty($shop->domain))
                    <small>{{ $shop->domain }}</small>
                @endif
            </div>
        </a>
        <a href="{{ route('order.index') }}" class="shop-profile-header__cart" aria-label="سبد خرید">
            <span class="shop-profile-header__cart-icon">🛒</span>
            <span>سبد خرید</span>
        </a>
    </div>
</header>

<style>
.shop-profile-header{position:sticky;top:0;z-index:1000;background:#fff;border-bottom:1px solid #eee}
.shop-profile-header__inner{max-width:960px;margin:auto;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.shop-profile-header__brand{display:flex;align-items:center;gap:10px;color:inherit;text-decoration:none;min-width:0}
.shop-profile-header__avatar{width:46px;height:46px;border-radius:50%;overflow:hidden;border:2px solid #111;display:flex;align-items:center;justify-content:center;flex:none;background:#f3f3f3}
.shop-profile-header__avatar img{width:100%;height:100%;object-fit:cover}
.shop-profile-header__avatar span{font-size:20px;font-weight:700}
.shop-profile-header__info{display:flex;flex-direction:column;min-width:0}
.shop-profile-header__info strong{font-size:15px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shop-profile-header__info small{font-size:11px;color:#777;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shop-profile-header__cart{display:flex;align-items:center;gap:6px;text-decoration:none;color:#111;font-size:13px;font-weight:600;white-space:nowrap}
.shop-profile-header__cart-icon{font-size:18px}
@media(max-width:576px){.shop-profile-header__inner{padding:10px 12px}.shop-profile-header__avatar{width:42px;height:42px}.shop-profile-header__cart span:last-child{display:none}}
</style>