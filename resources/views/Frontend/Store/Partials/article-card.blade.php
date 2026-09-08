<a href="{{ route('article.show', $article) }}" class="store-post">
    <div class="store-post-image">
        @if(!empty($article->images['thum']))
            <img src="{{ asset($article->images['thum']) }}" alt="{{ $article->title }}" loading="lazy">
        @elseif(!empty($article->images['thumbnail']))
            <img src="{{ asset($article->images['thumbnail']) }}" alt="{{ $article->title }}" loading="lazy">
        @else
            <div class="store-post-placeholder"><i class="bi bi-image"></i></div>
        @endif
    </div>
    <div class="store-post-info">
        <h2>{{ $article->title }}</h2>
    </div>
</a>
