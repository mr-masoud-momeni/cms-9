<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li>
            <a href="{{URL::to('/')}}" target="_blank">نمایش سایت</a>
        </li>
        <li class="dropdown {{Request::is('admin/Permission')
                              || Request::is('admin/Role')
                              || Request::is('admin/register')? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">کاربران<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li><a class="{{Request::is('admin/Permission') ? 'active-sidebar' : ''}}" href="{{route('Permission.index')}}">سطح دسترسی کاربران</a></li>
                <li><a class="{{Request::is('admin/Role') ? 'active-sidebar' : ''}}" href="{{route('role.index')}}">نقش کاربر</a></li>
                <li><a class="{{Request::is('admin/register') ? 'active-sidebar' : ''}}" href="{{route('register.index')}}">ایجاد کاربر</a></li>
            </ul>
        </li>
        <li class="dropdown {{Request::is('admin/menu') ? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">منو<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <a class="{{Request::is('admin/menu') ? 'active-sidebar' : ''}}" href="{{route('menu.index')}}">ایجاد منو</a>
            </ul>
        </li>
        <li class="dropdown {{Request::is('admin/article/create') || Request::is('admin/article') || Request::is('admin/category/create/article') ? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">پست ها<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <a class="{{Request::is('admin/article/create') ? 'active-sidebar' : ''}}" href="{{route('article.create')}}">ارسال پست</a>
                <li><a class="{{Request::is('admin/article') ? 'active-sidebar' : ''}}"  href="{{route('article.index')}}">لیست پست ها</a></li>
                <li><a class="{{Request::is('admin/category/create/article') ? 'active-sidebar' : ''}}" href="{{route('catArticle.create')}}">ایجاد دسته بندی</a></li>
            </ul>
        </li>
        <li class="dropdown {{Request::is('admin/email') || Request::is('admin/email-group/create') || Request::is('admin/category/x') ? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">اطلاع رسانی<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <a class="{{Request::is('admin/email') ? 'active-sidebar' : ''}}" href="{{route('email.index')}}">ایمیل</a>
                <li><a class="{{Request::is('admin/email-group/create') ? 'active-sidebar' : ''}}"  href="{{route('email-group.create')}}">دسته بندی ایمیل</a></li>
            </ul>
        </li>
        <li class="dropdown {{Request::is('admin/page') ? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">صفحه ها<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <a class="{{Request::is('admin/page') ? 'active-sidebar' : ''}}" href="{{route('page.index')}}">ایجاد صفحه</a>
            </ul>
        </li>
        <li class="dropdown {{Request::is('admin/product/create') || Request::is('admin/product') || Request::is('admin/category/create/product') ? 'open' : ''}} ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">محصولات<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <a class="{{Request::is('admin/product/create') ? 'active-sidebar' : ''}}" href="{{route('product.create')}}">ایجاد محصول</a>
                <li><a class="{{Request::is('admin/product') ? 'active-sidebar' : ''}}"  href="{{route('product.index')}}">لیست محصولات</a></li>
                <li><a class="{{Request::is('admin/category/create/product') ? 'active-sidebar' : ''}}" href="{{route('catProduct.create')}}">ایجاد دسته بندی</a></li>
            </ul>
        </li>
    </ul>
</div>
