<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 50)->default('عدد')->after('price');
            $table->unsignedInteger('stock')->default(0)->after('unit');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('reserved_at')->nullable()->after('paid_at');
            $table->timestamp('reservation_expires_at')->nullable()->after('reserved_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'stock']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'reserved_at',
                'reservation_expires_at',
            ]);
        });
    }
};
