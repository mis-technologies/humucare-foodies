<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricePerMillilitersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_per_milliliters', function (Blueprint $table) {
            $table->id();
            $table->decimal('price')->default(0.00000000);
            $table->string('product_id')->nullable();
            $table->integer('milliliter')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_price_per_milliliters');
    }
}
