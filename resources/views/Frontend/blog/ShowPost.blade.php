@extends('Frontend.layouts.Master')
@section('Main')

    <!--==========================
      About Us Section
    ============================-->
    <section id="SinglePost" >

        <div class="container">
            <div class="row">

                <div class="col-lg-7 col-md-6">
                    <div id="content">
                        <h2>{{$article->title}}</h2>
                        {!! $article->body !!}
                        <ul>
                            @if(isset($article->category))
                                @foreach($article->category()->get() as $cat)
                                    <li>{{$cat->name}}</li>
                                @endforeach
                            @endif

                        </ul>
                    </div>
                    <div class="comment">
                        @include('Frontend.layouts.comment')
                    </div>
                </div>

                <div class="col-lg-5 col-md-6">
                    <div class="SinglePost-img">
                        <?php $place=$article->images['thum']; ?>
                        <img src="{{ asset($place)}}" alt="">
                    </div>
                </div>


            </div>
        </div>

    </section><!-- #about -->


@endsection

