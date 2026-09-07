@extends('Frontend.layouts.Master')
@section('Main')

<section id="SinglePost">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-6">
                <div id="content">
                    <h2>{{ $article->title }}</h2>
                    {!! $article->body !!}

                    @if($article->categories->count())
                        <ul>
                            @foreach($article->categories as $cat)
                                <li>{{ $cat->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="comment">
                    @include('Frontend.layouts.comment')
                </div>
            </div>

            <div class="col-lg-5 col-md-6">
                <div class="SinglePost-img">
                    @if(!empty($article->images['thum']))
                        <img src="{{ asset($article->images['thum']) }}" alt="{{ $article->title }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
