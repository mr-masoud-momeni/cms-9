<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowGuestOrders extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->unsignedBigInteger('buyer_id')->nullable()->change();
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
            $table->unsignedBigInteger('buyer_id')->nullable(false)->change();
            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');
        });
    }
}
