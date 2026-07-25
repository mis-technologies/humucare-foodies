<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GatewayController@update / ManualGatewayController set $gateway->image, but
 * the gateways table (rebuilt from the vendor's partial migrations) had no
 * image column, so saving a gateway threw "Unknown column 'image'".
 */
class AddImageToGateways extends Migration
{
    public function up()
    {
        Schema::table('gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('gateways', 'image')) {
                $table->string('image')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('gateways', function (Blueprint $table) {
            if (Schema::hasColumn('gateways', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
}
