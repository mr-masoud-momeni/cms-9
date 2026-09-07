<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('shop_id');
            $table->string('status')->nullable();
            $table->string('tracking_code')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('total', 50)->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_province')->nullable();
            $table->string('receiver_city')->nullable();
            $table->text('receiver_address')->nullable();
            $table->string('receiver_postal_code', 20)->nullable();
            $table->timestamps();
            $table->foreign('buyer_id')->references('id')->on('buyers')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
