<?php

namespace Database\Seeders;

use App\Models\Gateway;
use App\Models\GatewayCurrency;
use Illuminate\Database\Seeder;

/**
 * Seeds the three payment methods the restaurant wants. The vendor script
 * already ships the processors + IPN routes — records here just make them
 * appear in Admin → Payment Gateways (automatic) / Manual Gateways so the
 * admin can enter their own credentials.
 *
 * - Stripe (card)          : admin pastes Secret Key + Webhook secret
 * - PayPal (redirect)      : admin enters their PayPal business email
 * - Bank Transfer (manual) : works immediately; admin approves each payment
 *
 * Automatic gateways use code < 1000; manual use code >= 1000. Currencies join
 * to a gateway on method_code = code.
 */
class GatewaySeeder extends Seeder
{
    public function run()
    {
        $cur = 'GBP';
        $sym = '£';

        /* ---------------------------------------------------- Stripe (card) */
        $this->automatic(
            code: 101,
            name: 'Stripe',
            alias: 'StripeV3',
            params: [
                'secret_key' => ['title' => 'API Secret Key',        'global' => true, 'value' => ''],
                'end_point'  => ['title' => 'Webhook Signing Secret', 'global' => true, 'value' => ''],
            ],
            currency: $cur,
            symbol: $sym,
            // seeded disabled — enable in admin once keys are entered
            status: 0
        );

        /* --------------------------------------------------- PayPal (redirect) */
        $this->automatic(
            code: 102,
            name: 'PayPal',
            alias: 'Paypal',
            params: [
                'paypal_email' => ['title' => 'PayPal Business Email', 'global' => true, 'value' => ''],
            ],
            currency: $cur,
            symbol: $sym,
            status: 0
        );

        /* -------------------------------------------------- Bank Transfer (manual) */
        // Manual gateways carry an instruction blob + a customer field form.
        $inputForm = [
            'sender_name' => [
                'field_name' => 'sender_name', 'field_level' => 'Account Name Used',
                'type' => 'text', 'validation' => 'required',
            ],
            'reference' => [
                'field_name' => 'reference', 'field_level' => 'Transfer Reference',
                'type' => 'text', 'validation' => 'required',
            ],
        ];

        $manual = Gateway::updateOrCreate(
            ['code' => 1001],
            [
                'name'                 => 'Bank Transfer',
                'alias'                => 'bank_transfer',
                'status'               => 1,
                'gateway_parameters'   => json_encode([]),
                'input_form'           => json_encode($inputForm),
                'supported_currencies' => json_encode([]),
                'crypto'               => 0,
                'description'          => "Pay by bank transfer to:\n\nAccount name: Foodies Ltd\nSort code: 00-00-00\nAccount number: 00000000\n\nUse your order number as the payment reference, then submit the form below. Your order is confirmed once we verify the transfer.",
            ]
        );
        GatewayCurrency::updateOrCreate(
            ['method_code' => 1001, 'currency' => $cur],
            [
                'name'              => 'Bank Transfer',
                'gateway_alias'     => 'bank_transfer',
                'symbol'            => $sym,
                'min_amount'        => 0,
                'max_amount'        => 1000000,
                'fixed_charge'      => 0,
                'percent_charge'    => 0,
                'rate'              => 1,
                'gateway_parameter' => json_encode($inputForm),
            ]
        );
    }

    private function automatic($code, $name, $alias, $params, $currency, $symbol, $status)
    {
        Gateway::updateOrCreate(
            ['code' => $code],
            [
                'name'                 => $name,
                'alias'                => $alias,
                'status'               => $status,
                'gateway_parameters'   => json_encode($params),
                'supported_currencies' => json_encode(['GBP' => 'GBP', 'USD' => 'USD', 'EUR' => 'EUR']),
                'crypto'               => 0,
                'description'          => null,
                'input_form'           => json_encode([]),
            ]
        );

        // one currency row, with empty credential values for the admin to fill
        $emptyParams = [];
        foreach ($params as $k => $v) {
            $emptyParams[$k] = '';
        }

        GatewayCurrency::updateOrCreate(
            ['method_code' => $code, 'currency' => $currency],
            [
                'name'              => $name,
                'gateway_alias'     => $alias,
                'symbol'            => $symbol,
                'min_amount'        => 0.50,
                'max_amount'        => 1000000,
                'fixed_charge'      => 0,
                'percent_charge'    => 0,
                'rate'              => 1,
                'gateway_parameter' => json_encode($emptyParams),
            ]
        );
    }
}
