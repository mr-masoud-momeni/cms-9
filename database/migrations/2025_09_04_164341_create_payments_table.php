<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('gateway_id');
            $table->unsignedBigInteger('order_id')->nullable(); // اگر سفارش داری
            $table->string('ref_id')->nullable();    // کد ارجاع بانک (token)
            $table->string('sale_reference_id')->nullable(); // شماره پیگیری
            $table->string('sale_order_id')->nullable();     // شماره سفارش بانک
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['pending','redirected','paid','failed'])->default('pending');
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('gateway_id')->references('id')->on('gateways')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
