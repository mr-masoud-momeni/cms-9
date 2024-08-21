<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('body');
            $table->string('slug');
            $table->string('link',50)->nullable();
            $table->text('product-body')->nullable();
            $table->text('images');
            $table->text('price-type');
            $table->string('price',50)->nullable();
            $table->integer('comentCount')->default(0);
            $table->integer('veiwCount')->default(0);
            $table->timestamps();

            //Define foreign keys
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
