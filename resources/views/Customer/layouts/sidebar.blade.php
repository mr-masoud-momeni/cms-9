<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li>
            <a href="{{ URL::to('/') }}" target="_blank">نمایش سایت</a>
        </li>

        @role('shop_owner')
        @php
            $shopMenuOpen = Route::is(
                'shop.product.*',
                'shop.gateways.*',
                'shop.orders.*',
                'shop.settings.*',
                'shop.article.*'
            );
        @endphp

        <li class="dropdown {{ $shopMenuOpen ? 'open' : '' }}">
            <a href="#" class="dropdown-toggle {{ $shopMenuOpen ? 'active-sidebar' : '' }}" data-toggle="dropdown">
                فروشگاه<span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a class="{{ Route::currentRouteName() == 'shop.settings.edit' ? 'active-sidebar' : '' }}" href="{{ route('shop.settings.edit') }}">مشخصات فروشگاه</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.gateways.edit' ? 'active-sidebar' : '' }}" href="{{ route('shop.gateways.edit') }}">مدیریت درگاه پرداخت</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.product.create' ? 'active-sidebar' : '' }}" href="{{ route('shop.product.create') }}">ایجاد محصول</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.product.index' ? 'active-sidebar' : '' }}" href="{{ route('shop.product.index') }}">لیست محصولات</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.orders.index' ? 'active-sidebar' : '' }}" href="{{ route('shop.orders.index') }}">سفارش ها</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.article.create' ? 'active-sidebar' : '' }}" href="{{ route('shop.article.create') }}">ایجاد مقاله</a></li>
                <li><a class="{{ Route::currentRouteName() == 'shop.article.index' ? 'active-sidebar' : '' }}" href="{{ route('shop.article.index') }}">لیست مقالات</a></li>
            </ul>
        </li>
        @endrole
    </ul>
</div>
