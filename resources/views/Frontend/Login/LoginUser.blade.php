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
                                <!-- فرم لاگین -->
                                <form method="POST" action="{{ route('buyer.login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" id="email" class="form-control" placeholder="ایمیل" name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="password" id="password" class="form-control" placeholder="گذرواژه" name="password" value="{{ old('password') }}">
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

