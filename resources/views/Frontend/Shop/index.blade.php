<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $shop?->name ?? 'فروشگاه' }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #fafafa;
            color: #171717;
            font-family: Tahoma, Arial, sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        .profile-page { max-width: 935px; margin: 0 auto; padding: 28px 18px 50px; }
        .profile-header {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 38px;
            padding: 18px 0 38px;
            border-bottom: 1px solid #dbdbdb;
        }
        .avatar-wrap { display: flex; justify-content: center; align-items: flex-start; }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5);
        }
        .avatar-inner {
            width: 100%; height: 100%; border-radius: 50%;
            background: #fff; display: flex; align-items: center; justify-content: center;
            overflow: hidden; border: 4px solid #fff;
            font-size: 42px; font-weight: 700; color: #444;
        }
        .profile-main { min-width: 0; }
        .profile-title { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-bottom: 22px; }
        .profile-title h1 { font-size: 26px; font-weight: 400; margin: 0; }
        .profile-actions { display: flex; gap: 8px; }
        .cart-action {
            position: relative;
            background: #171717;
            color: #fff;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
        }
        .cart-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            margin-right: 5px;
            padding: 0 5px;
            border-radius: 10px;
            background: #fff;
            color: #171717;
            font-size: 11px;
            line-height: 18px;
        }
        .stats { display: flex; gap: 34px; margin-bottom: 20px; font-size: 15px; }
        .stats strong { font-weight: 700; }
        .bio { line-height: 1.9; font-size: 14px; max-width: 520px; }
        .domain { color: #737373; font-size: 13px; direction: ltr; text-align: right; margin-top: 2px; }
        .tabs {
            display: flex;
            justify-content: center;
            gap: 0;
            border-bottom: 1px solid #dbdbdb;
            margin-top: 4px;
        }
        .tab {
            min-width: 150px;
            padding: 17px 24px 15px;
            border: 0;
            border-top: 1px solid transparent;
            background: transparent;
            color: #737373;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
        }
        .tab.active {
            color: #171717;
            border-top-color: #171717;
        }
        .tab-panel { padding-top: 12px; }
        .product-grid, .article-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }
        .post {
            position: relative;
            aspect-ratio: 1 / 1;
            background: #efefef;
            overflow: hidden;
        }
        .post img {
            width: 100%; height: 100%; display: block; object-fit: cover;
            transition: transform .25s ease;
        }
        .post:hover img { transform: scale(1.025); }
        .post-info {
            position: absolute; inset: 0;
            display: flex; align-items: flex-end;
            padding: 12px; color: #fff; opacity: 0;
            transition: opacity .2s ease;
            background: linear-gradient(transparent 45%, rgba(0,0,0,.68));
        }
        .post:hover .post-info { opacity: 1; }
        .article-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 260px;
            background: #fff;
            border: 1px solid #e4e4e4;
            overflow: hidden;
        }
        .article-image { width: 100%; aspect-ratio: 1.45 / 1; background: #efefef; overflow: hidden; }
        .article-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .article-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #888; font-size: 28px; }
        .article-content { padding: 13px 14px 15px; }
        .article-title { margin: 0 0 8px; font-size: 15px; line-height: 1.7; }
        .article-excerpt { margin: 0; color: #737373; font-size: 12px; line-height: 1.8; }
        .empty { text-align: center; padding: 70px 20px; color: #737373; grid-column: 1 / -1; }
        .pagination { display: flex; justify-content: center; gap: 8px; padding-top: 30px; grid-column: 1 / -1; }
        .pagination > * { padding: 7px 11px; border: 1px solid #ddd; border-radius: 7px; font-size: 13px; background: #fff; }
        .pagination-wrap { grid-column: 1 / -1; }
        @media (max-width: 650px) {
            .profile-page { padding: 16px 10px 35px; }
            .profile-header { grid-template-columns: 88px 1fr; gap: 18px; padding: 14px 6px 25px; }
            .avatar { width: 82px; height: 82px; }
            .avatar-inner { font-size: 25px; border-width: 3px; }
            .profile-title { gap: 8px; margin-bottom: 14px; }
            .profile-title h1 { font-size: 20px; }
            .profile-actions { margin-top: 4px; }
            .cart-action { padding: 7px 12px; font-size: 12px; }
            .stats { gap: 18px; font-size: 13px; margin-bottom: 10px; }
            .bio { font-size: 12px; line-height: 1.8; }
            .tabs { margin-top: 0; }
            .tab { min-width: 0; flex: 1; padding: 15px 8px 13px; }
            .product-grid { gap: 2px; }
            .article-grid { grid-template-columns: 1fr; gap: 8px; }
            .article-card { min-height: 0; }
        }
    </style>
</head>
<body>
<main class="profile-page">
    <header class="profile-header">
        <div class="avatar-wrap">
            <div class="avatar">
                <div class="avatar-inner">
                    {{ mb_substr($shop?->name ?? 'ف', 0, 1) }}
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-title">
                <div>
                    <h1>{{ $shop?->name ?? 'فروشگاه' }}</h1>
                    @if($shop?->domain)
                        <div class="domain">{{ $shop->domain }}</div>
                    @endif
                </div>
                <div class="profile-actions">
                    <a class="cart-action" href="{{ route('order.index') }}">
                        🛒 سبد خرید
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <div class="stats">
                <span><strong>{{ $products->total() }}</strong> محصول</span>
                <span><strong>{{ $articles->total() }}</strong> مقاله</span>
            </div>

            <div class="bio">
                محصولات و خدمات {{ $shop?->name ?? 'این فروشگاه' }}<br>
                برای مشاهده جزئیات، روی محصول یا مقاله کلیک کنید.
            </div>
        </div>
    </header>

    <nav class="tabs" aria-label="محتوای فروشگاه">
        <button class="tab active" type="button" data-tab="products">محصولات</button>
        <button class="tab" type="button" data-tab="articles">مقالات</button>
    </nav>

    <section id="products" class="tab-panel">
        @if($products->count())
            <div class="product-grid">
                @foreach($products as $product)
                    <a class="post" href="{{ route('front.product.show', $product) }}" aria-label="{{ $product->title }}">
                        @if(!empty($product->images['thum']))
                            <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" loading="lazy">
                        @else
                            <div class="article-placeholder">🛍️</div>
                        @endif
                        <div class="post-info">
                            <strong>{{ $product->title }}</strong>
                        </div>
                    </a>
                @endforeach
                <div class="pagination-wrap pagination">
                    {{ $products->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="empty">هنوز محصولی برای نمایش وجود ندارد.</div>
        @endif
    </section>

    <section id="articles" class="tab-panel" hidden>
        @if($articles->count())
            <div class="article-grid">
                @foreach($articles as $article)
                    <a class="article-card" href="{{ route('article.show', $article) }}">
                        <div class="article-image">
                            @if(!empty($article->images['thum']))
                                <img src="{{ asset($article->images['thum']) }}" alt="{{ $article->title }}" loading="lazy">
                            @else
                                <div class="article-placeholder">📝</div>
                            @endif
                        </div>
                        <div class="article-content">
                            <h2 class="article-title">{{ $article->title }}</h2>
                            <p class="article-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($article->body), 110) }}</p>
                        </div>
                    </a>
                @endforeach
                <div class="pagination-wrap pagination">
                    {{ $articles->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="empty">هنوز مقاله‌ای برای این فروشگاه منتشر نشده است.</div>
        @endif
    </section>
</main>

<script>
    document.querySelectorAll('.tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.tab').forEach(function (item) {
                item.classList.toggle('active', item === tab);
            });

            document.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.hidden = panel.id !== tab.dataset.tab;
            });
        });
    });
</script>
</body>
</html>
