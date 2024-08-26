<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable();
            $table->integer('subcategory_id')->nullable();
            $table->string('name',40)->nullable();
            $table->string('slug')->nullable();
            $table->string('product_id')->nullable();
            $table->decimal('price')->default(0.00000000);
            $table->integer('quantity')->default(0);
            $table->integer('club_point')->nullable();
            $table->decimal('discount')->nullable();
            $table->tinyInteger('min_order')->nullable();
            $table->string('unit')->nullable();
            $table->tinyInteger('hot_deals')->default(0);
            $table->tinyInteger('featured_product')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->string('image')->nullable();
            $table->text('files')->nullable();
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
        Schema::dropIfExists('products');
    }
}
