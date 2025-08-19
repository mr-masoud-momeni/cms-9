<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li>
            <a href="{{URL::to('/')}}" target="_blank">نمایش سایت</a>
        </li>

        @role('shop_owner')
        <li class="dropdown ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">فروشگاه<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li><a class="{{Route::currentRouteName() == 'product.create' ? 'active-sidebar' : ''}}" href="{{route('product.create')}}">ایجاد محصول</a></li>
                <li><a class="{{Route::currentRouteName() == 'product.index' ? 'active-sidebar' : ''}}"  href="{{route('product.index')}}">لیست محصولات</a></li>
                <li><a class="{{Route::currentRouteName() == 'catProduct.create' ? 'active-sidebar' : ''}}" href="{{route('catProduct.create')}}">ایجاد دسته بندی</a></li>
                <li><a class="{{Route::currentRouteName() == 'gateways.edit' ? 'active-sidebar' : ''}}" href="{{route('gateways.edit')}}">مدیریت درگاه پرداخت</a></li>
            </ul>
        </li>
        @endrole
    </ul>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.dropdown-menu .active-sidebar').forEach(function (activeLink) {
            let dropdownParent = activeLink.closest('.dropdown');
            if (dropdownParent) {
                dropdownParent.classList.add('open');
            }
        });
    });
</script>
