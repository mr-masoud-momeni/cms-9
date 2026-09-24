<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        'products' => 'products_shop_id_index',
        'orders' => 'orders_shop_id_index',
        'categories' => 'categories_shop_id_index',
        'payments' => 'payments_shop_id_index',
        'gateways' => 'gateways_shop_id_index',
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexName) {
            if ($this->hasLeadingShopIdIndex($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->index('shop_id', $indexName);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexName) {
            if ($this->hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        }
    }

    private function hasLeadingShopIdIndex(string $table): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('column_name', 'shop_id')
            ->where('seq_in_index', 1)
            ->exists();
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
