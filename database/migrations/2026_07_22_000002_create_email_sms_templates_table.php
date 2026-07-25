<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Both EmailTemplate and SmsTemplate point at ONE shared table named
 * `email_sms_templates`. An earlier migration created `email_templates` /
 * `sms_templates` instead, so every notify() call threw
 * "Base table not found" — which crashed checkout for logged-in customers.
 */
class CreateEmailSmsTemplatesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('email_sms_templates')) {
            Schema::create('email_sms_templates', function (Blueprint $table) {
                $table->id();
                $table->string('act', 191)->nullable()->index();
                $table->string('name', 191)->nullable();
                $table->string('subj', 191)->nullable();
                $table->text('email_body')->nullable();
                $table->text('sms_body')->nullable();
                $table->text('shortcodes')->nullable();
                $table->tinyInteger('email_status')->default(1);
                $table->tinyInteger('sms_status')->default(1);
                $table->timestamps();
            });
        }

        // the mistakenly-named tables were never written to; drop them so they
        // can't be confused with the real one
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('sms_templates');
    }

    public function down()
    {
        Schema::dropIfExists('email_sms_templates');
    }
}
