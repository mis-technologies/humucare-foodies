<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gateway schema gaps (the core_tables migration reconstructed these from the
 * vendor's incomplete migrations):
 *   - gateway_currencies.gateway_parameters -> gateway_parameter (24 files read
 *     the singular name; processors would get null keys otherwise)
 *   - gateways.description  (manual-gateway payment instructions)
 *   - gateways.input_form   (manual-gateway customer field definitions)
 */
class FixGatewayColumns extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('gateway_currencies', 'gateway_parameters')
            && !Schema::hasColumn('gateway_currencies', 'gateway_parameter')) {
            DB::statement('ALTER TABLE `gateway_currencies` CHANGE `gateway_parameters` `gateway_parameter` TEXT NULL');
        }

        Schema::table('gateways', function ($table) {
            if (!Schema::hasColumn('gateways', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('gateways', 'input_form')) {
                $table->text('input_form')->nullable();
            }
        });
    }

    public function down()
    {
        if (Schema::hasColumn('gateway_currencies', 'gateway_parameter')) {
            DB::statement('ALTER TABLE `gateway_currencies` CHANGE `gateway_parameter` `gateway_parameters` TEXT NULL');
        }
        Schema::table('gateways', function ($table) {
            foreach (['description', 'input_form'] as $c) {
                if (Schema::hasColumn('gateways', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
}
