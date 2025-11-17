<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'admin',
             'display_name' => 'admin',
             'description' => 'دسترسی کامل به بخش ادمین سایت',
             ],
            ['name' => 'shop_owner',
             'display_name' => 'shop-owner',
             'description' => 'مدیریت افزودن و ویرایش محصول و دیدن سفارش ها و...',
            ],
            ['name' => 'buyer',
             'display_name' => 'buyer',
             'description' => 'مدیریت پنل خرید و دیدن سفارش در حال انجام...',
            ],
        ]);

    }
}
