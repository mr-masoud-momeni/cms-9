<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = DB::table('users')->insertGetId([
            'uuid' => '654a7ba2-4868-4247-a01d-dfa252f38a89',
            'name' => 'Masoud',
            'email' => 'masoud2525@gmail.com',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);

        // تخصیص نقش‌ها به یوزر
        DB::table('role_user')->insert([
            'user_id' => $userId,
            'role_id' => 1, // نقش admin
            'user_type' => 'App\Models\User',
        ]);
        $userId = DB::table('users')->insertGetId([
            'uuid' => 'f420fbd9-05ea-4cb2-bcc7-8c4c589aca66',
            'path' => 'azq11azq',
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);
        // تخصیص نقش‌ها به یوزر
        DB::table('role_user')->insert([
            'user_id' => $userId,
            'role_id' => 2, // نقش shop-owner
            'user_type' => 'App\Models\User',
        ]);
        DB::table('shops')->insert([
            'uuid' => 'bc5b48d0-ac04-46c3-abf1-d128c15ade2b',
            'user_id' => $userId,
            'name' => 'localhost',
            'domain' => 'localhost',
            'slug' => 'localhost',
        ]);
        $buyerId = DB::table('buyers')->insertGetId([
            'uuid' => '163d08bd-1d0e-4a0b-8f63-9314ae43616e',
            'name' => 'masoud',
            'email' => 'masoud2525@gmail.com',
            'phone' => '09120136329',
            'password' => '$2a$12$8QEYX7BUv/Ts02oPWQLtgeuyx1RIOFiC0WuwmqAp1LmZUtyy117SO',
        ]);
        // تخصیص نقش‌ها به یوزر
        DB::table('role_user')->insert([
            'user_id' =>$buyerId,
            'role_id' => 3, // نقش buyer
            'user_type' => 'App\Models\buyer',
        ]);
        // تخصیص نقش‌ها به یوزر
        DB::table('buyer_shop')->insert([
            'buyer_id' =>$buyerId,
            'shop_id' => 1, // نقش buyer
            'email' => 'masoud2525@gmail.com',
            'phone' => '09120136329',
            'email_verified_at' => '2024-08-30 13:52:31'
        ]);
    }
}
