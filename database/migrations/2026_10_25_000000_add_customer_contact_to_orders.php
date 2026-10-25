<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot the customer's contact details on the order.
 *
 * Guest orders have user_id = 0, and the name, phone and email typed at
 * checkout were never stored anywhere — admin saw "Anonymous User" with no
 * way to reach the customer. Signed-in orders get the same snapshot, so the
 * order keeps the details it was placed with even if the profile changes.
 */
class AddCustomerContactToOrders extends Migration {
    public function up() {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_mobile')) {
                $table->string('customer_mobile', 40)->nullable()->after('customer_email');
            }
        });
    }

    public function down() {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['customer_mobile', 'customer_email', 'customer_name'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
