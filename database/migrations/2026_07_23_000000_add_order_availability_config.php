<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Restaurant ordering controls: master "accepting orders" switch, opening
 * hours per weekday, and which fulfilment methods (delivery / collection)
 * are offered. Stored as one JSON blob on general_settings, matching the
 * existing mail_config / sms_config pattern.
 */
class AddOrderAvailabilityConfig extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'order_config')) {
                $table->text('order_config')->nullable();
            }
        });

        // how the order is fulfilled: 'delivery' or 'collection'
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'fulfilment_type')) {
                $table->string('fulfilment_type', 20)->default('delivery')->after('payment_type');
            }
        });

        // sensible defaults so the store is usable immediately
        $default = [
            'accepting_orders'   => 1,
            'delivery_enabled'   => 1,
            'collection_enabled' => 1,
            'timezone_note'      => 'Times are the restaurant local time.',
            'hours'              => collect(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'])
                ->mapWithKeys(fn ($d) => [$d => ['closed' => 0, 'open' => '09:00', 'close' => '22:00']])
                ->all(),
        ];

        DB::table('general_settings')->whereNull('order_config')
            ->update(['order_config' => json_encode($default)]);
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'order_config')) {
                $table->dropColumn('order_config');
            }
        });
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'fulfilment_type')) {
                $table->dropColumn('fulfilment_type');
            }
        });
    }
}
