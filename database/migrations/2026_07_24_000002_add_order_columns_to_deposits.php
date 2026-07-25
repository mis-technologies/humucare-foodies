<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The online-payment flow links a Deposit (payment intent) to its Order and
 * routes by method_code, but the deposits table (rebuilt from the vendor's
 * partial migrations) had neither column — so $deposit->order_id was always
 * null and manual approval could never find the order.
 */
class AddOrderColumnsToDeposits extends Migration
{
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'order_id')) {
                $table->integer('order_id')->nullable()->index()->after('user_id');
            }
            if (!Schema::hasColumn('deposits', 'method_code')) {
                $table->integer('method_code')->nullable()->index()->after('order_id');
            }
        });
    }

    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            foreach (['order_id', 'method_code'] as $c) {
                if (Schema::hasColumn('deposits', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
}
