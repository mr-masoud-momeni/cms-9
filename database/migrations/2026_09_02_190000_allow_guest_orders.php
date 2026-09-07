<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AllowGuestOrders extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
        });

        DB::statement('ALTER TABLE orders MODIFY buyer_id BIGINT UNSIGNED NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
        });

        DB::statement('ALTER TABLE orders MODIFY buyer_id BIGINT UNSIGNED NOT NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');
        });
    }
}
