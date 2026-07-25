<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * productPrice() -> showDiscountPrice() reads $product->discount_type to decide
 * whether a discount is a fixed amount (1) or a percentage (else). The column
 * was never defined on `products`, so every discount was silently treated as a
 * percentage. Default to 1 (fixed) to match how discounts are entered.
 */
class AddDiscountTypeToProducts extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'discount_type')) {
                $table->tinyInteger('discount_type')->default(1)->after('discount');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });
    }
}
