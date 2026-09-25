@extends('Customer.layouts.Master')

@section('content')
<div class="shop-dashboard">
    <div class="shop-dashboard__header">
        <h1>داشبورد فروشگاه</h1>
        <p>دسترسی سریع به بخش‌های اصلی فروشگاه</p>
    </div>

    <div class="shop-dashboard__grid">
        <a class="shop-dashboard__card" href="{{ route('shop.orders.index') }}">
            <span class="shop-dashboard__icon"><i class="fa fa-shopping-bag" aria-hidden="true"></i></span>
            <span class="shop-dashboard__title">سفارش‌ها</span>
            <span class="shop-dashboard__description">مدیریت و بررسی سفارش‌های مشتریان</span>
        </a>

        <a class="shop-dashboard__card" href="{{ route('shop.product.index') }}">
            <span class="shop-dashboard__icon"><i class="fa fa-cube" aria-hidden="true"></i></span>
            <span class="shop-dashboard__title">محصولات</span>
            <span class="shop-dashboard__description">افزودن و مدیریت محصولات فروشگاه</span>
        </a>

        <a class="shop-dashboard__card" href="{{ route('shop.article.index') }}">
            <span class="shop-dashboard__icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span class="shop-dashboard__title">مقالات</span>
            <span class="shop-dashboard__description">مدیریت مقالات و محتوای فروشگاه</span>
        </a>

        <a class="shop-dashboard__card" href="{{ route('shop.settings.edit') }}">
            <span class="shop-dashboard__icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
            <span class="shop-dashboard__title">فروشگاه</span>
            <span class="shop-dashboard__description">مدیریت مشخصات و تنظیمات فروشگاه</span>
        </a>
    </div>
</div>

<style>
    .shop-dashboard {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 0 30px;
    }

    .shop-dashboard__header {
        margin-bottom: 24px;
    }

    .shop-dashboard__header h1 {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 700;
    }

    .shop-dashboard__header p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .shop-dashboard__grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .shop-dashboard__card {
        min-height: 170px;
        padding: 24px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        background: #fff;
        color: #333;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .shop-dashboard__card:hover,
    .shop-dashboard__card:focus {
        color: #333;
        border-color: #26739f;
        box-shadow: 0 5px 18px rgba(0,0,0,.07);
        transform: translateY(-2px);
    }

    .shop-dashboard__icon {
        width: 48px;
        height: 48px;
        margin-bottom: 16px;
        border-radius: 10px;
        background: #eef6fa;
        color: #26739f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .shop-dashboard__title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .shop-dashboard__description {
        color: #777;
        font-size: 13px;
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .shop-dashboard__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .shop-dashboard {
            padding: 8px 0 24px;
        }

        .shop-dashboard__header {
            margin-bottom: 18px;
        }

        .shop-dashboard__header h1 {
            font-size: 21px;
        }

        .shop-dashboard__grid {
            gap: 12px;
        }

        .shop-dashboard__card {
            min-height: 150px;
            padding: 18px;
        }
    }
</style>
@endsection
