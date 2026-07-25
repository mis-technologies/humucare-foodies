<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Fresh install data for the Foodies food-ordering storefront.
 * Idempotent: safe to re-run (truncates the tables it owns).
 */
class FoodieSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $this->seedAll();
        });
    }

    private function seedAll()
    {
        $now = now();

        // NOTE: `truncate` is DDL in MySQL and commits implicitly, which would
        // expose a window where general_settings is empty and every request
        // 500s. `delete` keeps this inside the transaction so the swap is atomic.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (['general_settings', 'languages', 'pages', 'frontends', 'brands',
                  'categories', 'sub_categories', 'products', 'shipping_methods'] as $t) {
            DB::table($t)->delete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ---------------------------------------------------------------- settings
        DB::table('general_settings')->insert([
            'sitename'        => 'Foodies',
            'cur_text'        => 'GBP',
            'cur_sym'         => '£',
            'email_from'      => 'noreply@foodies.test',
            'base_color'      => 'f5a623',
            'seo_description' => 'Freshly cooked, chef-made meals delivered to your door.',
            // global HTML wrapper — notification bodies are injected at {{message}}.
            // Must be non-empty or every email sends with an empty body.
            'email_template'  => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden">'
                . '<div style="background:#14142e;color:#fff;padding:18px 24px;font-size:20px;font-weight:bold">Foodies</div>'
                . '<div style="padding:24px;color:#333;line-height:1.6">{{message}}</div>'
                . '<div style="background:#f6f6fb;color:#888;padding:14px 24px;font-size:12px">Foodies &middot; This is an automated message.</div>'
                . '</div>',
            'mail_config'     => json_encode(['name' => 'php', 'enc' => '', 'host' => '', 'port' => '', 'username' => '', 'password' => '']),
            'sms_config'      => json_encode(['name' => 'clickatell', 'clickatell' => ['api_key' => '']]),
            'ev' => 0, 'en' => 0, 'sv' => 0, 'sn' => 0,
            'force_ssl' => 0, 'secure_password' => 0, 'agree' => 0, 'registration' => 1,
            'display_stock'   => 1,
            'active_template' => 'basic',
            'discount'        => 0,
            'discount_type'   => 2,
            'sys_version'     => '1.0',
            'created_at'      => $now, 'updated_at' => $now,
        ]);

        DB::table('languages')->insert([
            'name' => 'English', 'code' => 'en', 'icon' => '', 'is_default' => 1,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // ---------------------------------------------------------------- pages
        DB::table('pages')->insert([
            ['name' => 'Home', 'slug' => 'home', 'tempname' => 'templates.basic.', 'secs' => json_encode([]), 'is_default' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'About Us', 'slug' => 'about-us', 'tempname' => 'templates.basic.', 'secs' => json_encode([]), 'is_default' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------------------- frontend content
        $frontends = [];

        // hero banner — the hero renders the first banner.element row
        $frontends[] = ['data_keys' => 'banner.element', 'data_values' => json_encode(['image' => 'hero.png', 'url' => '#'])];

        $frontends[] = ['data_keys' => 'contact_us.content', 'data_values' => json_encode([
            'address'        => '12 Kitchen Street, Birmingham, UK',
            'contact_email'  => 'hello@foodies.test',
            'contact_number' => '+44 7485 705519',
            'short_details'  => 'We would love to hear from you.',
        ])];

        $frontends[] = ['data_keys' => 'footer.content', 'data_values' => json_encode([
            'subscribe_title' => 'Get the latest offers and new dishes straight to your inbox.',
            'connect_title'   => 'Follow us for daily specials.',
        ])];

        foreach ([
            ['Facebook', '<i class="lab la-facebook-f"></i>'],
            ['Instagram', '<i class="lab la-instagram"></i>'],
            ['Twitter', '<i class="lab la-twitter"></i>'],
        ] as [$title, $icon]) {
            $frontends[] = ['data_keys' => 'social_icon.element', 'data_values' => json_encode([
                'title' => $title, 'social_icon' => $icon, 'url' => '#',
            ])];
        }

        foreach ([
            ['Fast Delivery', 'Hot food within 45 minutes', 'las la-shipping-fast'],
            ['Fresh Ingredients', 'Sourced fresh every morning', 'las la-leaf'],
            ['24/7 Support', 'We are always here to help', 'las la-headset'],
            ['Best Prices', 'Great value on every meal', 'las la-tags'],
        ] as $i => [$title, $detail, $icon]) {
            $frontends[] = ['data_keys' => 'service.element', 'data_values' => json_encode([
                'title' => $title, 'short_detail' => $detail, 'icon' => $icon, 'image' => 'service' . ($i + 1) . '.png',
            ])];
        }

        foreach ([
            ['Privacy Policy', 'Your privacy matters to us.'],
            ['Terms of Service', 'Please read our terms carefully.'],
        ] as [$title, $details]) {
            $frontends[] = ['data_keys' => 'policy_pages.element', 'data_values' => json_encode([
                'title' => $title, 'details' => $details,
            ])];
        }

        $frontends[] = ['data_keys' => 'cookie.data', 'data_values' => json_encode([
            'status' => 0, 'description' => 'We use cookies to improve your experience.', 'link' => '#',
        ])];

        // Required by partials/seo.blade.php — it dereferences $seo->description
        // and implode()s $seo->keywords, so both must exist (keywords as array).
        $frontends[] = ['data_keys' => 'seo.data', 'data_values' => json_encode([
            'description'        => 'Order freshly cooked, chef-made meals from Foodies and get them delivered hot to your door.',
            'keywords'           => ['food delivery', 'jollof rice', 'african food', 'takeaway', 'foodies'],
            'social_title'       => 'Foodies — The best meals for your taste buds',
            'social_description' => 'Freshly cooked, chef-made meals delivered to your door.',
            'image'              => 'seo.png',
            'image_size'         => '600x315',
        ])];

        DB::table('frontends')->insert(array_map(function ($f) use ($now) {
            return $f + ['tempname' => 'templates.basic.', 'created_at' => $now, 'updated_at' => $now];
        }, $frontends));

        // ---------------------------------------------------------------- brand
        // Product::scopeActive() requires an ACTIVE brand, category AND subcategory.
        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Foodies Kitchen', 'status' => 1, 'featured' => 1,
            'web_url' => '#', 'image' => null,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // ---------------------------------------------------------------- menu
        $menu = [
            'Starters' => [
                ['Suya Skewers',      6.50, 8.00],
                ['Puff Puff',         3.50, 0],
                ['Spring Rolls',      4.00, 5.00],
            ],
            'Main Course' => [
                ['Jollof Rice',       6.00, 10.00],
                ['Egusi & Pounded Yam', 9.50, 0],
                ['Grilled Chicken Platter', 11.00, 13.00],
                ['Seafood Okra',      12.50, 0],
            ],
            'Desserts' => [
                ['Chin Chin',         3.00, 0],
                ['Coconut Cake',      4.50, 6.00],
            ],
            'Drinks' => [
                ['Chapman',           3.50, 0],
                ['Zobo Punch',        3.00, 4.00],
            ],
            'Specials' => [
                ['Party Barbecue Platter', 24.00, 30.00],
                ['Weekend Feast Box', 32.00, 40.00],
            ],
        ];

        $imgs = ['food1.jpg', 'food2.jpg', 'food3.jpg', 'food4.jpg', 'food5.jpg', 'food6.jpg'];
        $i = 0;

        foreach ($menu as $catName => $dishes) {
            $catId = DB::table('categories')->insertGetId([
                'name' => $catName, 'status' => 1, 'featured' => 1,
                'image' => null,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            $subId = DB::table('sub_categories')->insertGetId([
                'category_id' => $catId, 'name' => $catName, 'status' => 1, 'featured' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            foreach ($dishes as [$name, $price, $oldPrice]) {
                $discount = $oldPrice > 0 ? round($oldPrice - $price, 2) : 0;

                DB::table('products')->insert([
                    'category_id'      => $catId,
                    'subcategory_id'   => $subId,
                    'brand_id'         => $brandId,
                    'name'             => $name,
                    'slug'             => \Illuminate\Support\Str::slug($name),
                    'product_id'       => strtoupper(\Illuminate\Support\Str::random(8)),
                    // when a discount exists, `price` holds the ORIGINAL price and
                    // productPrice() subtracts the discount to get the sale price
                    'price'            => $oldPrice > 0 ? $oldPrice : $price,
                    'discount'         => $discount,
                    'discount_type'    => 1, // 1 = fixed amount
                    'quantity'         => 50,
                    'min_order'        => 1,
                    'unit'             => 'plate',
                    'hot_deals'        => $discount > 0 ? 1 : 0,
                    'today_deals'      => 0,
                    'featured_product' => $i % 2 === 0 ? 1 : 0,
                    'sale_count'       => (5 - ($i % 5)) * 7,
                    'avg_rate'         => [5, 4.5, 4, 5, 4.5][$i % 5],
                    'status'           => 1,
                    'description'      => "Freshly prepared {$name}, cooked to order by our chefs.",
                    'features'         => json_encode([]),
                    'image'            => null, // upload real photos via admin; nulls show a clean placeholder
                    'files'            => json_encode([]),
                    'created_at'       => $now, 'updated_at' => $now,
                ]);
                $i++;
            }
        }

        // ---------------------------------------------------------------- shipping
        DB::table('shipping_methods')->insert([
            ['name' => 'Standard Delivery', 'price' => 3.99, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Express Delivery',  'price' => 6.99, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------------------- demo modifiers
        // Re-point the sample groups at the freshly inserted dishes. Products are
        // re-created with new ids on every seed, so attach by NAME, not id.
        $demoDish = DB::table('products')->where('name', 'Jollof Rice')->first();
        if ($demoDish) {
            $groupIds = DB::table('option_groups')
                ->whereIn('name', ['Choose your size', 'Add extras'])->pluck('id');

            foreach ($groupIds as $gid) {
                DB::table('product_option_group')->updateOrInsert(
                    ['product_id' => $demoDish->id, 'option_group_id' => $gid],
                    ['sort_order' => 0]
                );
            }
        }

        // ---------------------------------------------------------------- notification templates
        // Shared table: EmailTemplate and SmsTemplate both read `email_sms_templates`.
        DB::table('email_sms_templates')->delete();
        DB::table('email_sms_templates')->insert([
            [
                'act'          => 'ORDER_COMPLETE',
                'name'         => 'Order Confirmation (customer)',
                'subj'         => 'Your ' . 'Foodies' . ' order {{order_no}} is confirmed',
                'email_body'   => '<p>Hi {{user_name}},</p><p>Thanks for your order — we are preparing it now.</p>'
                    . '<p><b>Order:</b> {{order_no}}<br><b>Payment:</b> {{method_name}}<br>'
                    . '<b>Subtotal:</b> {{currency}}{{subtotal}}<br><b>Delivery:</b> {{currency}}{{shipping_charge}}<br>'
                    . '<b>Total:</b> {{currency}}{{total}}</p><p>We will let you know when it is on its way.</p>',
                'sms_body'     => 'Order {{order_no}} confirmed. Total {{currency}}{{total}}. Thank you!',
                'shortcodes'   => json_encode([
                    'user_name' => 'Customer name', 'order_no' => 'Order number', 'method_name' => 'Payment method',
                    'subtotal' => 'Subtotal', 'shipping_charge' => 'Delivery fee', 'total' => 'Order total', 'currency' => 'Currency',
                ]),
                'email_status' => 1, 'sms_status' => 1,
                'created_at'   => $now, 'updated_at' => $now,
            ],
            [
                'act'          => 'ADMIN_NEW_ORDER',
                'name'         => 'New Order Alert (restaurant)',
                'subj'         => 'NEW ORDER {{order_no}} — {{currency}}{{total}}',
                'email_body'   => '<p><b>A new order has come in.</b></p>'
                    . '<p><b>Order:</b> {{order_no}}<br><b>Customer:</b> {{user_name}}<br>'
                    . '<b>Payment:</b> {{method_name}}<br><b>Total:</b> {{currency}}{{total}}</p>'
                    . '<p><b>Items</b><br>{{items}}</p><p><b>Deliver to</b><br>{{address}}</p>',
                'sms_body'     => 'NEW ORDER {{order_no}} {{currency}}{{total}}',
                'shortcodes'   => json_encode([
                    'order_no' => 'Order number', 'user_name' => 'Customer name', 'method_name' => 'Payment method',
                    'total' => 'Order total', 'currency' => 'Currency', 'items' => 'Ordered items', 'address' => 'Delivery address',
                ]),
                'email_status' => 1, 'sms_status' => 0,
                'created_at'   => $now, 'updated_at' => $now,
            ],
        ]);

        // ---------------------------------------------------------------- admin
        if (!DB::table('admins')->where('username', 'admin')->exists()) {
            DB::table('admins')->insert([
                'name' => 'Administrator', 'username' => 'admin',
                'email' => 'admin@foodies.test', 'image' => null,
                'password' => Hash::make('password'),
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
