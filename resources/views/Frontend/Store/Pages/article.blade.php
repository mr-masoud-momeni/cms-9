@extends('Frontend.Store.Layouts.MasterMinimal')

@section('Main')
    <main class="store-detail store-blog-detail" aria-label="مقاله">
        <div class="store-detail-grid">
            <article class="store-detail-content">
                <h1>{{ $article->title }}</h1>

                <div class="store-blog-content">
                    {!! $article->body !!}
                </div>

                @if(isset($article->category))
                    <div class="store-blog-meta">
                        @foreach($article->category()->get() as $cat)
                            <span>{{ $cat->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="store-blog-comments">
                    @include('Frontend.layouts.comment')
                </div>
            </article>

            <div class="store-detail-media">
                @if(!empty($article->images['thum']))
                    <img src="{{ asset($article->images['thum']) }}" alt="{{ $article->title }}" class="store-blog-image">
                @endif
            </div>
        </div>
    </main>
@endsection
