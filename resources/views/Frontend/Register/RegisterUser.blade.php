@extends('Frontend.layouts.Master')
@section('Main')

    <main id="main">

        <!-- ======= Breadcrumbs ======= -->
        <section id="breadcrumbs" class="breadcrumbs">
            <div class="container">

                <ol>
                    <li><a href="index.html">Home</a></li>
                    <li>Inner Page</li>
                </ol>
                <h2>ثبت نام</h2>

            </div>
        </section><!-- End Breadcrumbs -->
        @include('Frontend.layouts.errors')
        @include('Frontend.layouts.message')
        <section class="inner-page  py-5">
            <div class="container">
                <div class="row ">
                    <div class="col-lg-4">
                        <div class="main-box clearfix">
                            <div class="from">
                                <!-- فرم ثبت‌نام -->
                                <form action="{{ route('buyer.register') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" id="name" class="form-control" placeholder="نام" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="email" id="email" class="form-control" placeholder="ایمیل" name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="text" id="phone" class="form-control" placeholder="تلفن همراه" name="phone" value="{{ old('phone') }}">
                                    </div>
                                    <div class="text-center mt-3"><button type="submit" class="btn btn-success">ثبت نام</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection

