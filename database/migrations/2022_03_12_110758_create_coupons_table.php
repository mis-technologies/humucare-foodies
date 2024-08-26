<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('discount')->default(0);
            $table->tinyInteger('discount_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('min_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('coupons');
    }
}
