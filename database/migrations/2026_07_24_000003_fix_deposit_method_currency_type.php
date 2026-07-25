<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * deposits.method_currency holds a currency CODE ('GBP'), but core_tables
 * created it as decimal(28,8) — so 'GBP' was stored as 0.00000000 and the
 * gateway-currency lookup (method_code + currency) never matched.
 */
class FixDepositMethodCurrencyType extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('deposits', 'method_currency')) {
            DB::statement("ALTER TABLE `deposits` MODIFY `method_currency` VARCHAR(40) NULL");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('deposits', 'method_currency')) {
            DB::statement("ALTER TABLE `deposits` MODIFY `method_currency` DECIMAL(28,8) NOT NULL DEFAULT 0");
        }
    }
}
