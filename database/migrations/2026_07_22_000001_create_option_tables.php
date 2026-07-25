<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Item modifiers ("Choose your size", "Add extras", "Spice level").
 *
 * Groups are reusable and attached to dishes through a pivot, so a single
 * "Choose your size" group can serve the whole menu instead of being
 * duplicated per dish.
 */
class CreateOptionTables extends Migration
{
    public function up()
    {
        Schema::create('option_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            // single = radio (pick one), multi = checkbox (pick several)
            $table->enum('type', ['single', 'multi'])->default('single');
            $table->tinyInteger('is_required')->default(0);
            $table->unsignedTinyInteger('min_select')->default(0);
            $table->unsignedTinyInteger('max_select')->default(1);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_group_id')->index();
            $table->string('name', 120);
            // price delta: may be 0, or negative for a discount
            $table->decimal('price', 28, 8)->default(0);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('option_group_id')->references('id')->on('option_groups')->onDelete('cascade');
        });

        Schema::create('product_option_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('option_group_id')->index();
            $table->integer('sort_order')->default(0);

            $table->foreign('option_group_id')->references('id')->on('option_groups')->onDelete('cascade');
            $table->unique(['product_id', 'option_group_id']);
        });

        // chosen modifiers travel with the basket line...
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'options')) {
                $table->text('options')->nullable();
            }
            if (!Schema::hasColumn('carts', 'options_price')) {
                $table->decimal('options_price', 28, 8)->default(0);
            }
        });

        // ...and are snapshotted onto the order so the kitchen ticket is
        // accurate even if the menu changes later.
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'options')) {
                $table->text('options')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'options')) { $table->dropColumn('options'); }
        });
        Schema::table('carts', function (Blueprint $table) {
            foreach (['options', 'options_price'] as $c) {
                if (Schema::hasColumn('carts', $c)) { $table->dropColumn($c); }
            }
        });
        Schema::dropIfExists('product_option_group');
        Schema::dropIfExists('options');
        Schema::dropIfExists('option_groups');
    }
}
