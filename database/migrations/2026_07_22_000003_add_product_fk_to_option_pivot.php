<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * The pivot had a foreign key on option_group_id but not on product_id, so
 * deleting a dish left orphaned modifier attachments behind (re-running the
 * seeder reproduced this immediately).
 */
class AddProductFkToOptionPivot extends Migration
{
    public function up()
    {
        // clear any orphans first, or the FK cannot be created
        DB::statement('DELETE pog FROM product_option_group pog
                       LEFT JOIN products p ON p.id = pog.product_id
                       WHERE p.id IS NULL');

        Schema::table('product_option_group', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('product_option_group', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
    }
}
