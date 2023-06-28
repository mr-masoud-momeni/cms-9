<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsingend();
            $table->integer('parent_id')->unsingend();
            $table->string('type');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
//        Schema::create('article_category', function (Blueprint $table) {
//            $table->integer('category_id')->unsigned();
//            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
//            $table->integer('article_id')->unsigned();
//            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
//            $table->primary(['article_id' , 'category_id']);
//        });
//        Schema::create('categorizables', function (Blueprint $table) {
//            $table->integer('category_id')->unsigned();
////            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
//            $table->integer('categorizable_id')->unsigned();
//            $table->string('categorizable_type');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_category');
        Schema::dropIfExists('categories');
    }
}
