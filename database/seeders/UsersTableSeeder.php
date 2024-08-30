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
    }
}
