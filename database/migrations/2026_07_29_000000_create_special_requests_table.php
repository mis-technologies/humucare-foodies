<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Special order" enquiries — a customer asking for something the menu does not
 * sell by the plate: 5 litres of jollof rice, a tray of egusi for a party,
 * office lunch for 40. These are quotes, not orders: no price is charged here.
 * The restaurant replies with an offer from the admin panel.
 */
class CreateSpecialRequestsTable extends Migration {
    public function up() {
        if (!Schema::hasTable('special_requests')) {
            Schema::create('special_requests', function (Blueprint $table) {
                $table->id();
                // short human reference the customer can quote on the phone
                $table->string('request_no', 20)->unique();
                $table->unsignedInteger('user_id')->default(0); // 0 = guest

                $table->string('name', 120);
                $table->string('email', 191);
                $table->string('phone', 40);

                // catering | bulk | party | office | other
                $table->string('request_type', 20)->default('bulk');
                $table->string('item', 191);        // "Jollof rice", "Egusi soup"
                $table->string('quantity', 120);    // "5 litres", "2 large trays"

                $table->date('needed_on')->nullable();
                $table->unsignedSmallInteger('people')->nullable();
                $table->decimal('budget', 28, 8)->nullable();
                $table->text('details')->nullable();

                // 0 new | 1 quoted | 2 confirmed | 3 closed
                $table->tinyInteger('status')->default(0);
                $table->text('admin_reply')->nullable();
                $table->timestamp('replied_at')->nullable();

                $table->timestamps();
                $table->index(['status', 'created_at']);
            });
        }

        // Templates live in the DB so the admin can reword them without a deploy.
        // Seeded here (not only in FoodieSeeder) because the seeder is destructive
        // and is never run against an existing install.
        if (Schema::hasTable('email_sms_templates')) {
            $rows = [
                [
                    'act'          => 'ADMIN_SPECIAL_REQUEST',
                    'name'         => 'Special Order Request (restaurant)',
                    'subj'         => 'NEW SPECIAL REQUEST {{request_no}} — {{item}}',
                    'email_body'   => '<p><b>A customer has asked for a special order.</b></p>'
                        . '<p><b>Reference:</b> {{request_no}}<br>'
                        . '<b>Type:</b> {{request_type}}<br>'
                        . '<b>Item:</b> {{item}}<br>'
                        . '<b>Quantity:</b> {{quantity}}<br>'
                        . '<b>Needed on:</b> {{needed_on}}<br>'
                        . '<b>Guests:</b> {{people}}<br>'
                        . '<b>Budget:</b> {{budget}}</p>'
                        . '<p><b>Customer</b><br>{{name}}<br>{{email}}<br>{{phone}}</p>'
                        . '<p><b>Details</b><br>{{details}}</p>',
                    'sms_body'     => 'NEW SPECIAL REQUEST {{request_no}}: {{quantity}} {{item}} — {{phone}}',
                    'shortcodes'   => json_encode([
                        'request_no'   => 'Reference number',
                        'request_type' => 'Kind of request',
                        'item'         => 'Dish requested',
                        'quantity'     => 'Quantity or size',
                        'needed_on'    => 'Date needed',
                        'people'       => 'Number of guests',
                        'budget'       => 'Customer budget',
                        'name'         => 'Customer name',
                        'email'        => 'Customer email',
                        'phone'        => 'Customer phone',
                        'details'      => 'Extra details',
                    ]),
                    'email_status' => 1,
                    'sms_status'   => 0,
                ],
                [
                    'act'          => 'SPECIAL_REQUEST_RECEIVED',
                    'name'         => 'Special Order Received (customer)',
                    'subj'         => 'We got your request {{request_no}}',
                    'email_body'   => '<p>Hi {{name}},</p>'
                        . '<p>Thanks for getting in touch. We have your request for '
                        . '<b>{{quantity}} of {{item}}</b> and will come back to you shortly with a price.</p>'
                        . '<p>Your reference is <b>{{request_no}}</b> — quote it if you call us.</p>',
                    'sms_body'     => 'Thanks {{name}}! Request {{request_no}} received. We will send you a quote shortly.',
                    'shortcodes'   => json_encode([
                        'request_no' => 'Reference number',
                        'name'       => 'Customer name',
                        'item'       => 'Dish requested',
                        'quantity'   => 'Quantity or size',
                    ]),
                    'email_status' => 1,
                    'sms_status'   => 0,
                ],
                [
                    'act'          => 'SPECIAL_REQUEST_REPLY',
                    'name'         => 'Special Order Quote (customer)',
                    'subj'         => 'Your quote for request {{request_no}}',
                    'email_body'   => '<p>Hi {{name}},</p>'
                        . '<p>About your request for <b>{{quantity}} of {{item}}</b>:</p>'
                        . '<p>{{reply}}</p>'
                        . '<p>Reply to this email or call us to confirm. Reference {{request_no}}.</p>',
                    'sms_body'     => 'Quote for {{request_no}}: {{reply}}',
                    'shortcodes'   => json_encode([
                        'request_no' => 'Reference number',
                        'name'       => 'Customer name',
                        'item'       => 'Dish requested',
                        'quantity'   => 'Quantity or size',
                        'reply'      => 'Your message to the customer',
                    ]),
                    'email_status' => 1,
                    'sms_status'   => 0,
                ],
            ];

            foreach ($rows as $row) {
                if (!DB::table('email_sms_templates')->where('act', $row['act'])->exists()) {
                    DB::table('email_sms_templates')->insert($row + [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down() {
        Schema::dropIfExists('special_requests');

        if (Schema::hasTable('email_sms_templates')) {
            DB::table('email_sms_templates')->whereIn('act', [
                'ADMIN_SPECIAL_REQUEST',
                'SPECIAL_REQUEST_RECEIVED',
                'SPECIAL_REQUEST_REPLY',
            ])->delete();
        }
    }
}
