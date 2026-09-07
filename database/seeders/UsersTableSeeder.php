<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // کاربر اصلی
        $adminId = DB::table('users')->insertGetId([
            'uuid' => '654a7ba2-4868-4247-a01d-dfa252f38a89',
            'name' => 'Masoud',
            'email' => 'masoud2525@gmail.com',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);

        DB::table('role_user')->insert([
            'user_id' => $adminId,
            'role_id' => 1,
            'user_type' => 'App\\Models\\User',
        ]);

        // کاربر صاحب فروشگاه
        $userId = DB::table('users')->insertGetId([
            'uuid' => 'f420fbd9-05ea-4cb2-bcc7-8c4c589aca66',
            'path' => 'azq11azq',
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);

        DB::table('role_user')->insert([
            'user_id' => $userId,
            'role_id' => 2,
            'user_type' => 'App\\Models\\User',
        ]);

        // فروشگاه تستی
        $shopId = DB::table('shops')->insertGetId([
            'uuid' => 'bc5b48d0-ac04-46c3-abf1-d128c15ade2b',
            'user_id' => $userId,
            'name' => 'فروشگاه تستی من',
            'domain' => 'localhost',
            'slug' => 'localhost',
        ]);

        // خریدار تستی
        $buyerId = DB::table('buyers')->insertGetId([
            'uuid' => '163d08bd-1d0e-4a0b-8f63-9314ae43616e',
            'name' => 'masoud',
            'email' => 'masoud2525@gmail.com',
            'phone' => '09120136329',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
        ]);

        DB::table('role_user')->insert([
            'user_id' => $buyerId,
            'role_id' => 3,
            'user_type' => 'App\\Models\\Buyer',
        ]);

        DB::table('buyer_shop')->insert([
            'buyer_id' => $buyerId,
            'shop_id' => $shopId,
            'email' => 'masoud2525@gmail.com',
            'phone' => '09120136329',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);

        // -------------------------
        // محصولات تستی
        // -------------------------
        $products = [
            [
                'title' => 'هدفون بی‌سیم تستی',
                'slug' => 'test-wireless-headphone',
                'body' => '<p>یک محصول تستی برای نمایش فروشگاه.</p>',
                'price' => '2490000',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=900',
            ],
            [
                'title' => 'ساعت هوشمند تستی',
                'slug' => 'test-smart-watch',
                'body' => '<p>ساعت هوشمند نمونه برای تست رابط کاربری فروشگاه.</p>',
                'price' => '3890000',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900',
            ],
            [
                'title' => 'کفش روزمره تستی',
                'slug' => 'test-everyday-shoes',
                'body' => '<p>کفش روزمره سبک و مناسب استفاده روزانه.</p>',
                'price' => '3190000',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=900',
            ],
            [
                'title' => 'دوربین عکاسی تستی',
                'slug' => 'test-camera',
                'body' => '<p>یک دوربین نمونه برای تست گرید محصولات.</p>',
                'price' => '18900000',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=900',
            ],
            [
                'title' => 'کیف چرمی تستی',
                'slug' => 'test-leather-bag',
                'body' => '<p>کیف چرمی نمونه با تصویر خارجی.</p>',
                'price' => '2790000',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900',
            ],
            [
                'title' => 'عینک آفتابی تستی',
                'slug' => 'test-sunglasses',
                'body' => '<p>عینک آفتابی نمونه برای تست فروشگاه.</p>',
                'price' => '1590000',
                'image' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=900',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'shop_id' => $shopId,
                'user_id' => $userId,
                'title' => $product['title'],
                'body' => $product['body'],
                'slug' => $product['slug'],
                'images' => json_encode([
                    'images' => [
                        'original' => $product['image'],
                        '300' => $product['image'],
                        '600' => $product['image'],
                        '800' => $product['image'],
                    ],
                    'thum' => $product['image'],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'price-type' => 'cash',
                'price' => $product['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------
        // مقالات تستی فروشگاه
        // -------------------------
        $articles = [
            [
                'title' => 'راهنمای انتخاب محصول مناسب',
                'slug' => 'guide-to-choosing-the-right-product',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200',
                'body' => '<p>انتخاب محصول مناسب همیشه ساده نیست. در این مقاله چند نکته کاربردی برای مقایسه و انتخاب بهتر را بررسی می‌کنیم.</p><p>قبل از خرید، نیاز واقعی، کیفیت، قیمت و تجربه استفاده را در نظر بگیرید.</p>',
            ],
            [
                'title' => 'چطور قبل از خرید محصول را بررسی کنیم؟',
                'slug' => 'how-to-check-a-product-before-buying',
                'image' => 'https://images.unsplash.com/photo-1556740749-887f6717d7e4?w=1200',
                'body' => '<p>مشخصات فنی، تصاویر واقعی، نظرات کاربران و شرایط خدمات پس از فروش از مهم‌ترین مواردی هستند که قبل از خرید باید بررسی شوند.</p>',
            ],
            [
                'title' => 'چرا کیفیت تصویر محصول مهم است؟',
                'slug' => 'why-product-image-quality-matters',
                'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=1200',
                'body' => '<p>تصویر محصول یکی از اولین چیزهایی است که مخاطب در فروشگاه می‌بیند. تصاویر باکیفیت می‌توانند اطلاعات بیشتری درباره محصول منتقل کنند.</p>',
            ],
            [
                'title' => 'یک فروشگاه خوب چه محتوایی باید داشته باشد؟',
                'slug' => 'what-content-should-a-good-store-have',
                'image' => 'https://images.unsplash.com/photo-1556742111-a301076d9d18?w=1200',
                'body' => '<p>یک فروشگاه فقط مجموعه‌ای از محصولات نیست. معرفی محصولات، آموزش، راهنمای خرید و محتوای کاربردی می‌تواند تجربه بهتری برای مشتری ایجاد کند.</p>',
            ],
        ];

        foreach ($articles as $article) {
            DB::table('articles')->insert([
                'shop_id' => $shopId,
                'user_id' => $userId,
                'title' => $article['title'],
                'body' => $article['body'],
                'slug' => $article['slug'],
                'images' => json_encode([
                    'original' => $article['image'],
                    'thum' => $article['image'],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'comentCount' => 0,
                'veiwCount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------
        // سفارش‌های تستی
        // -------------------------
        $productId = DB::table('products')->where('shop_id', $shopId)->value('id');

        $order1 = DB::table('orders')->insertGetId([
            'buyer_id' => $buyerId,
            'shop_id' => $shopId,
            'status' => 'paid',
            'tracking_code' => null,
            'paid_at' => now(),
            'total' => '4980000',
            'receiver_name' => 'علی رضایی',
            'receiver_phone' => '09121234567',
            'receiver_province' => 'لرستان',
            'receiver_city' => 'بروجرد',
            'receiver_address' => 'خیابان سیاوش',
            'receiver_postal_code' => '1234567891',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order2 = DB::table('orders')->insertGetId([
            'buyer_id' => $buyerId,
            'shop_id' => $shopId,
            'status' => 'pending',
            'tracking_code' => null,
            'paid_at' => null,
            'total' => null,
            'receiver_name' => null,
            'receiver_phone' => null,
            'receiver_province' => null,
            'receiver_city' => null,
            'receiver_address' => null,
            'receiver_postal_code' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_product')->insert([
            [
                'order_id' => $order1,
                'product_id' => $productId,
                'quantity' => 2,
                'price' => '2490000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $order2,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => '2490000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
