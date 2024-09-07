<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_shop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained();
            $table->foreignId('shop_id')->constrained();
            $table->string('email');
            $table->string('phone');
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'email']);  // ترکیب ایمیل و فروشگاه باید یونیک باشد
            $table->unique(['store_id', 'phone']);  // ترکیب شماره تلفن و فروشگاه باید یونیک باشد
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyer_shop');
    }
}
