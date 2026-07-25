<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns the application queries on `products` that the original
 * create_products_table migration never defined.
 */
class AddMissingProductColumns extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->integer('brand_id')->nullable()->after('subcategory_id');
            }
            if (!Schema::hasColumn('products', 'today_deals')) {
                $table->tinyInteger('today_deals')->default(0)->after('hot_deals');
            }
            if (!Schema::hasColumn('products', 'sale_count')) {
                $table->integer('sale_count')->default(0)->after('today_deals');
            }
            if (!Schema::hasColumn('products', 'avg_rate')) {
                $table->decimal('avg_rate', 8, 2)->default(0)->after('sale_count');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['brand_id', 'today_deals', 'sale_count', 'avg_rate'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
