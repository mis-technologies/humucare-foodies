<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Core tables that the application queries but that shipped with no migration
 * (they originally came from the vendor's install SQL dump).
 *
 * Dated early on purpose: `carts` must exist before the 2024 ALTER migrations
 * that add price_per_liter / price_per_milliliter to it.
 */
class CreateCoreTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('sitename', 191)->nullable();
                $table->string('cur_text', 191)->default('GBP');
                $table->string('cur_sym', 191)->default('£');
                $table->string('email_from', 191)->nullable();
                $table->text('email_template')->nullable();
                $table->text('sms_api')->nullable();
                $table->text('sms_body')->nullable();
                $table->string('base_color', 20)->default('f5a623');
                $table->text('seo_description')->nullable();
                $table->text('mail_config')->nullable();
                $table->text('sms_config')->nullable();
                $table->tinyInteger('ev')->default(0);   // email verification
                $table->tinyInteger('en')->default(0);   // email notification
                $table->tinyInteger('sv')->default(0);   // sms verification
                $table->tinyInteger('sn')->default(0);   // sms notification
                $table->tinyInteger('force_ssl')->default(0);
                $table->tinyInteger('secure_password')->default(0);
                $table->tinyInteger('agree')->default(0);
                $table->tinyInteger('registration')->default(1);
                $table->tinyInteger('display_stock')->default(1);
                $table->string('active_template', 191)->default('basic');
                $table->decimal('discount', 28, 8)->default(0);
                $table->tinyInteger('discount_type')->default(2); // 1 = fixed, 2 = percent
                $table->string('sys_version', 191)->default('1.0');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('code', 191)->nullable();
                $table->string('icon', 191)->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('slug', 191)->nullable();
                $table->string('tempname', 191)->nullable();
                $table->text('secs')->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->text('seo_content')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('frontends')) {
            Schema::create('frontends', function (Blueprint $table) {
                $table->id();
                $table->string('data_keys', 191)->nullable();
                $table->longText('data_values')->nullable();
                $table->text('seo_content')->nullable();
                $table->string('tempname', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('extensions')) {
            Schema::create('extensions', function (Blueprint $table) {
                $table->id();
                $table->string('act', 191)->nullable();
                $table->string('name', 191)->nullable();
                $table->text('description')->nullable();
                $table->string('image', 191)->nullable();
                $table->text('script')->nullable();
                $table->text('shortcode')->nullable();
                $table->string('support', 191)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('firstname', 191)->nullable();
                $table->string('lastname', 191)->nullable();
                $table->string('username', 191)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('country_code', 40)->nullable();
                $table->string('mobile', 191)->nullable();
                $table->string('password', 191)->nullable();
                $table->string('image', 191)->nullable();
                $table->text('address')->nullable();
                $table->decimal('balance', 28, 8)->default(0);
                $table->integer('ref_by')->default(0);
                $table->string('ver_code', 191)->nullable();
                $table->timestamp('ver_code_send_at')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('ev')->default(1);
                $table->tinyInteger('sv')->default(1);
                $table->tinyInteger('ts')->default(0);
                $table->tinyInteger('tv')->default(1);
                $table->string('tsc', 191)->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('username', 191)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('image', 191)->nullable();
                $table->string('password', 191)->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admin_password_resets')) {
            Schema::create('admin_password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191)->nullable();
                $table->string('token', 191)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191)->nullable();
                $table->string('token', 191)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        // NOTE: price_per_liter / liter / price_per_milliliter / milliliter are
        // intentionally omitted — the existing 2024 ALTER migrations add them.
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('product_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscribers')) {
            Schema::create('subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_galleries')) {
            Schema::create('product_galleries', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id')->nullable();
                $table->string('image', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gateways')) {
            Schema::create('gateways', function (Blueprint $table) {
                $table->id();
                $table->integer('form_id')->nullable();
                $table->string('code', 191)->nullable();
                $table->string('name', 191)->nullable();
                $table->string('alias', 191)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->text('gateway_parameters')->nullable();
                $table->text('supported_currencies')->nullable();
                $table->tinyInteger('crypto')->default(0);
                $table->text('extra')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gateway_currencies')) {
            Schema::create('gateway_currencies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->integer('gateway_id')->nullable();
                $table->string('currency', 191)->nullable();
                $table->string('symbol', 191)->nullable();
                $table->string('method_code', 191)->nullable();
                $table->string('gateway_alias', 191)->nullable();
                $table->decimal('min_amount', 28, 8)->default(0);
                $table->decimal('max_amount', 28, 8)->default(0);
                $table->decimal('percent_charge', 28, 8)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->string('image', 191)->nullable();
                $table->text('gateway_parameters')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('deposits')) {
            Schema::create('deposits', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('method_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('method_currency', 28, 8)->default(0);
                $table->decimal('charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->decimal('final_amo', 28, 8)->default(0);
                $table->string('detail', 191)->nullable();
                $table->string('btc_amo', 191)->nullable();
                $table->string('btc_wallet', 191)->nullable();
                $table->string('trx', 191)->nullable();
                $table->tinyInteger('try', false)->default(0);
                $table->tinyInteger('status')->default(0);
                $table->text('admin_feedback')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('charge', 28, 8)->default(0);
                $table->string('post_balance', 191)->nullable();
                $table->string('trx_type', 191)->nullable();
                $table->string('trx', 191)->nullable();
                $table->string('details', 191)->nullable();
                $table->string('remark', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('withdraw_methods')) {
            Schema::create('withdraw_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('image', 191)->nullable();
                $table->decimal('min_limit', 28, 8)->default(0);
                $table->decimal('max_limit', 28, 8)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->decimal('percent_charge', 28, 8)->default(0);
                $table->string('currency', 191)->nullable();
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->text('user_data')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('withdrawals')) {
            Schema::create('withdrawals', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('method_id')->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('currency', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(0);
                $table->decimal('charge', 28, 8)->default(0);
                $table->string('trx', 191)->nullable();
                $table->decimal('final_amount', 28, 8)->default(0);
                $table->text('withdraw_information')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->text('admin_feedback')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('name', 191)->nullable();
                $table->string('email', 191)->nullable();
                $table->integer('ticket')->nullable();
                $table->string('subject', 191)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('priority')->default(1);
                $table->timestamp('last_reply')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_messages')) {
            Schema::create('support_messages', function (Blueprint $table) {
                $table->id();
                $table->integer('support_ticket_id')->nullable();
                $table->integer('admin_id')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_attachments')) {
            Schema::create('support_attachments', function (Blueprint $table) {
                $table->id();
                $table->integer('support_message_id')->nullable();
                $table->string('attachment', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('act', 191)->nullable();
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

        if (!Schema::hasTable('sms_templates')) {
            Schema::create('sms_templates', function (Blueprint $table) {
                $table->id();
                $table->string('act', 191)->nullable();
                $table->string('name', 191)->nullable();
                $table->text('sms_body')->nullable();
                $table->text('shortcodes')->nullable();
                $table->tinyInteger('sms_status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_logins')) {
            Schema::create('user_logins', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('user_ip', 191)->nullable();
                $table->string('city', 191)->nullable();
                $table->string('country', 191)->nullable();
                $table->string('country_code', 191)->nullable();
                $table->string('longitude', 191)->nullable();
                $table->string('latitude', 191)->nullable();
                $table->string('browser', 191)->nullable();
                $table->string('os', 191)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }

    public function down()
    {
        foreach ([
            'failed_jobs', 'user_logins', 'sms_templates', 'email_templates',
            'support_attachments', 'support_messages', 'support_tickets',
            'withdrawals', 'withdraw_methods', 'transactions', 'deposits',
            'gateway_currencies', 'gateways', 'product_galleries', 'subscribers',
            'carts', 'password_resets', 'admin_password_resets', 'admins',
            'users', 'extensions', 'frontends', 'pages', 'languages',
            'general_settings',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
}
