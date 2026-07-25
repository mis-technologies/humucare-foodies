<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * sendEmail() writes $emailLog->email_from / ->email_to, but the shipped
 * migration created columns named `from` / `to`. Result: every customer
 * notification email threw "Unknown column 'email_from'" and never sent.
 *
 * Uses raw CHANGE COLUMN (renameColumn needs doctrine/dbal, which isn't installed).
 */
class FixEmailLogsColumns extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('email_logs', 'from') && !Schema::hasColumn('email_logs', 'email_from')) {
            DB::statement('ALTER TABLE `email_logs` CHANGE `from` `email_from` VARCHAR(255) NULL');
        }
        if (Schema::hasColumn('email_logs', 'to') && !Schema::hasColumn('email_logs', 'email_to')) {
            DB::statement('ALTER TABLE `email_logs` CHANGE `to` `email_to` VARCHAR(255) NULL');
        }
    }

    public function down()
    {
        if (Schema::hasColumn('email_logs', 'email_from')) {
            DB::statement('ALTER TABLE `email_logs` CHANGE `email_from` `from` VARCHAR(255) NULL');
        }
        if (Schema::hasColumn('email_logs', 'email_to')) {
            DB::statement('ALTER TABLE `email_logs` CHANGE `email_to` `to` VARCHAR(255) NULL');
        }
    }
}
