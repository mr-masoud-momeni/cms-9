<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('shop_id', 'products_shop_id_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('shop_id', 'orders_shop_id_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('shop_id', 'categories_shop_id_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('shop_id', 'payments_shop_id_index');
        });

        Schema::table('gateways', function (Blueprint $table) {
            $table->index('shop_id', 'gateways_shop_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_shop_id_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_shop_id_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_shop_id_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_shop_id_index');
        });

        Schema::table('gateways', function (Blueprint $table) {
            $table->dropIndex('gateways_shop_id_index');
        });
    }
};
