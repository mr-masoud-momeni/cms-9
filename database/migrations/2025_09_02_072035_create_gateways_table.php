<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id'); // شناسه فروشگاه
            $table->string('title'); // نام درگاه (مثلا Mellat)
            $table->string('terminal_id');
            $table->string('username');
            $table->string('password');
            $table->string('wsdl_url');
            $table->string('gateway_url');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
