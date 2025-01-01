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
                            @if(session('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection

