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
            $table->integer('user_id')->unsingend();
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
