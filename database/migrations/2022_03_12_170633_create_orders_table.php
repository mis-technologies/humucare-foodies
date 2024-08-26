<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('order_no')->nullable();
            $table->decimal('subtotal')->default(0.00000000);
            $table->decimal('discount')->default(0.00000000);
            $table->decimal('shipping_charge')->default(0.00000000);
            $table->decimal('total')->default(0.00000000);
            $table->integer('coupon_id')->default(0);
            $table->integer('shipping_id')->default(0);
            $table->tinyInteger('order_status')->default(0);
            $table->tinyInteger('notification')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('orders');
    }
}
