<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('pages', function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('user_id')->unsingend();
//            $table->string('title');
//            $table->string('url');
//            $table->string('slug');
//            $table->boolean('status');
//            $table->text('html')->nullable();
//            $table->text('styles')->nullable();
//            $table->text('css')->nullable();
//            $table->text('assets')->nullable();
//            $table->text('components')->nullable();
//            $table->timestamps();
//        });

        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsingend();
            $table->string('title');
            $table->string('url');
            $table->string('slug');
            $table->boolean('status');
            $table->text('gjs_data')->nullable();
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
        Schema::dropIfExists('pages');
    }
}
