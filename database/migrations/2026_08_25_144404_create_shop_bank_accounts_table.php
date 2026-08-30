<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')
                ->unique()
                ->constrained('shops')
                ->cascadeOnDelete();

            $table->string('card_number', 16);
            $table->string('sheba', 26);
            $table->string('account_holder');

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
        Schema::dropIfExists('shop_bank_accounts');
    }
}
