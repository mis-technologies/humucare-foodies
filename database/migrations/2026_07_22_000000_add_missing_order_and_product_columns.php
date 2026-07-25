<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns the application reads/writes that the shipped migrations never
 * defined (they only existed in the vendor's install SQL dump).
 *
 * Without these, placing an order throws a SQL error and admin product
 * creation fails outright.
 */
class AddMissingOrderAndProductColumns extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // CheckoutController stores a json_encode()'d address blob
            if (!Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable()->after('shipping_id');
            }
            // 1 = online payment, 2 = cash on delivery
            if (!Schema::hasColumn('orders', 'payment_type')) {
                $table->tinyInteger('payment_type')->default(2)->after('address');
            }
            // 0 = pending, 1 = success, 9 = cancelled (see Order::getPaymentTextAttribute)
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->tinyInteger('payment_status')->default(0)->after('payment_type');
            }
            // gateway redirect target (Coingate and friends)
            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->text('payment_url')->nullable()->after('payment_status');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'price')) {
                $table->decimal('price', 28, 8)->default(0)->after('quantity');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_sku')) {
                $table->string('product_sku', 40)->nullable()->unique()->after('slug');
            }
            if (!Schema::hasColumn('products', 'summary')) {
                $table->text('summary')->nullable()->after('description');
            }
            // digital-item fields: unused by a restaurant, but the admin form
            // posts them and store() assigns them, so they must exist.
            if (!Schema::hasColumn('products', 'digital_item')) {
                $table->tinyInteger('digital_item')->default(0)->after('status');
            }
            if (!Schema::hasColumn('products', 'file_type')) {
                $table->tinyInteger('file_type')->nullable()->after('digital_item');
            }
            if (!Schema::hasColumn('products', 'digi_file')) {
                $table->string('digi_file')->nullable()->after('file_type');
            }
            if (!Schema::hasColumn('products', 'digi_link')) {
                $table->text('digi_link')->nullable()->after('digi_file');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['payment_url', 'payment_status', 'payment_type', 'address'] as $c) {
                if (Schema::hasColumn('orders', $c)) { $table->dropColumn($c); }
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'price')) { $table->dropColumn('price'); }
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['digi_link', 'digi_file', 'file_type', 'digital_item', 'summary'] as $c) {
                if (Schema::hasColumn('products', $c)) { $table->dropColumn($c); }
            }
            if (Schema::hasColumn('products', 'product_sku')) {
                $table->dropUnique(['product_sku']);
                $table->dropColumn('product_sku');
            }
        });
    }
}
