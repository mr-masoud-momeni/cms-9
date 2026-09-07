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
        .profile-action { background: #efefef; border-radius: 8px; padding: 8px 16px; font-size: 14px; font-weight: 600; }
        .stats { display: flex; gap: 34px; margin-bottom: 20px; font-size: 15px; }
        .stats strong { font-weight: 700; }
        .bio { line-height: 1.9; font-size: 14px; max-width: 520px; }
        .username { color: #737373; font-size: 13px; direction: ltr; text-align: right; margin-top: 2px; }
        .feed-title { padding: 17px 0 12px; font-size: 12px; font-weight: 700; letter-spacing: .4px; }
        .feed-title span { border-top: 1px solid #111; padding-top: 17px; }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; }
        .post { position: relative; aspect-ratio: 1 / 1; background: #efefef; overflow: hidden; }
        .post img { width: 100%; height: 100%; display: block; object-fit: cover; transition: transform .25s ease; }
        .post:hover img { transform: scale(1.025); }
        .post-info { position: absolute; inset: 0; display: flex; align-items: flex-end; padding: 12px; color: #fff; opacity: 0; transition: opacity .2s ease; background: linear-gradient(transparent 45%, rgba(0,0,0,.65)); }
        .post:hover .post-info { opacity: 1; }
        .no-image { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #888; font-size: 13px; }
        .empty { text-align: center; padding: 70px 20px; color: #737373; }
        .pagination { display: flex; justify-content: center; gap: 8px; padding-top: 30px; }
        .pagination > * { padding: 7px 11px; border: 1px solid #ddd; border-radius: 7px; font-size: 13px; background: #fff; }
        @media (max-width: 650px) {
            .profile-page { padding: 16px 10px 35px; }
            .profile-header { grid-template-columns: 88px 1fr; gap: 18px; padding: 14px 6px 25px; }
            .avatar { width: 82px; height: 82px; }
            .avatar-inner { font-size: 25px; border-width: 3px; }
            .profile-title { gap: 8px; margin-bottom: 14px; }
            .profile-title h1 { font-size: 20px; width: 100%; }
            .profile-actions { display: none; }
            .stats { gap: 18px; font-size: 13px; margin-bottom: 10px; }
            .bio { font-size: 12px; line-height: 1.8; }
            .product-grid { gap: 2px; }
            .feed-title { padding-right: 4px; }
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
                        <div class="username">{{ $shop->domain }}</div>
                    @endif
                </div>
                <div class="profile-actions">
                    <a class="profile-action" href="#products">مشاهده محصولات</a>
                </div>
            </div>

            <div class="stats">
                <span><strong>{{ $products->total() }}</strong> پست</span>
                <span><strong>{{ $products->total() }}</strong> محصول</span>
            </div>

            <div class="bio">
                محصولات و خدمات {{ $shop?->name ?? 'این فروشگاه' }}<br>
                برای مشاهده جزئیات، روی هر محصول کلیک کنید.
            </div>
        </div>
    </header>

    <div id="products" class="feed-title"><span>محصولات</span></div>

    @if($products->count())
        <section class="product-grid">
            @foreach($products as $product)
                <a class="post" href="{{ route('front.product.show', $product) }}" aria-label="{{ $product->title }}">
                    @if(!empty($product->images['thum']))
                        <img src="{{ asset($product->images['thum']) }}" alt="{{ $product->title }}" loading="lazy">
                    @else
                        <div class="no-image">تصویری موجود نیست</div>
                    @endif
                    <div class="post-info">
                        <strong>{{ $product->title }}</strong>
                    </div>
                </a>
            @endforeach
        </section>

        <div class="pagination">
            {{ $products->links('vendor.pagination.bootstrap-4') }}
        </div>
    @else
        <div class="empty">
            هنوز محصولی برای نمایش وجود ندارد.
        </div>
    @endif
</main>
</body>
</html>
