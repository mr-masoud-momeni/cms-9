@include('Backend.layouts.header')
@yield('HeaderLinks')
</head>
<body>

<div id="wrapper"  >
    <div class="top-bar">
        <div class="nav " style="float: left; margin-top: 5px; ">
            <form action="{{route('logout')}}" method="post">
                {!! csrf_field() !!}
                <button class="  btn-xs btn">خروج از حساب کاربری</button>
            </form>
        </div>
        <div class="logo-cms">
            <a href="#">
            Start Bootstrap
        </a>
        </div>
        <div style="float: right;">
            <a href="#menu-toggle" id="menu-toggle" style="color: #fff;">
                <i class="fa fa-bars fa-2x" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <!-- Sidebar -->
    @include('Backend.layouts.sidebar')
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div style="width: 100%;">
            <div class="container-fluid margin-top-50">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

@include('Backend.layouts.footer')
@yield('scripts')
</body>

</html>
