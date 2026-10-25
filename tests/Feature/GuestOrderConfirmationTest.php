<?php

namespace Tests\Feature;

use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Guest checkout end to end: the order records the guest's items and contact
 * details at a server-side price, payment completion works without an account,
 * and the guest lands on a confirmation page instead of the login screen.
 */
class GuestOrderConfirmationTest extends TestCase
{
    use DatabaseTransactions;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Open all day so the "kitchen closed" guard doesn't reject test orders.
        $general = GeneralSetting::first();
        $config  = (array) ($general->order_config ?: []);
        $config['accepting_orders'] = 1;
        $config['collection_enabled'] = 1;
        foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
            $config['hours'][$day] = ['closed' => 0, 'open' => '00:00', 'close' => '23:59'];
        }
        $general->order_config = $config;
        $general->save();

        $product = Product::active()->first();
        if (!$product) {
            $this->markTestSkipped('Needs an active product in the local database.');
        }
        $product->quantity = 100;
        $product->save();
        $this->product = $product;
    }

    private function guestCart(int $qty = 2): array
    {
        return ['cart' => [$this->product->id => [
            'product_id' => $this->product->id, 'quantity' => $qty, 'options_price' => 0,
        ]]];
    }

    private function orderPayload(array $overrides = []): array
    {
        return array_merge([
            'firstname'       => 'Grace',
            'lastname'        => 'Hopper',
            'mobile'          => '07700 900123',
            'email'           => 'grace@example.test',
            'fulfilment_type' => 'collection',
            'payment_type'    => 2, // cash on delivery
            'price'           => 0.01, // hidden field a guest could tamper with
        ], $overrides);
    }

    public function test_guest_cash_order_is_recorded_and_lands_on_confirmation(): void
    {
        $unit = productPrice($this->product);

        $res = $this->withSession($this->guestCart(2))->post('/user/checkout/order', $this->orderPayload());
        $res->assertRedirect(route('order.confirmation'));

        $order = Order::latest('id')->first();
        $this->assertSame(0, (int) $order->user_id);
        $this->assertSame('Grace Hopper', $order->customer_name);
        $this->assertSame('grace@example.test', $order->customer_email);
        $this->assertSame('07700 900123', $order->customer_mobile);
        // Priced on the server from the session cart, not the 0.01 hidden field.
        $this->assertEqualsWithDelta($unit * 2, (float) $order->total, 0.001);
        $this->assertSame(1, $order->orderDetail()->count());
        $this->assertSame(2, (int) $order->orderDetail()->first()->quantity);
        $this->assertSame(98, (int) $this->product->fresh()->quantity);
        $this->assertNull(session('cart'));

        $page = $this->get(route('order.confirmation'));
        $page->assertOk()
            ->assertSee('Thank you')
            ->assertSee('Grace')
            ->assertSee($order->order_no)
            ->assertSee('grace@example.test')
            ->assertSee('Pay on delivery')
            ->assertSee('Track your order');
    }

    public function test_guest_card_payment_completes_and_confirms(): void
    {
        $gateway = GatewayCurrency::first();
        if (!$gateway) {
            $this->markTestSkipped('Needs a payment gateway currency in the local database.');
        }

        $this->withSession($this->guestCart(3))
            ->post('/user/checkout/order', $this->orderPayload(['payment_type' => 1]))
            ->assertRedirect(route('user.deposit'));

        $order = Order::latest('id')->first();
        // Lines recorded up front (the webhook has no session), stock untouched until paid.
        $this->assertSame(1, $order->orderDetail()->count());
        $this->assertSame(100, (int) $this->product->fresh()->quantity);
        $this->assertNotEmpty(session('cart'));

        // While the gateway hasn't confirmed yet, the page waits instead of claiming success.
        $this->get(route('order.confirmation'))->assertOk()->assertSee('Confirming your payment');

        $deposit = Deposit::create([
            'user_id' => 0, 'order_id' => $order->id, 'method_code' => $gateway->method_code,
            'method_currency' => $gateway->currency, 'amount' => $order->total, 'charge' => 0,
            'rate' => 1, 'final_amo' => $order->total, 'trx' => 'TESTGUEST' . $order->id,
            'status' => 0, 'try' => 0, 'btc_amo' => 0, 'btc_wallet' => '',
        ]);

        // Used to fatal on $user->id for guests, after the card was charged.
        PaymentController::userDataUpdate($deposit->trx);

        $this->assertSame(1, (int) $order->fresh()->payment_status);
        $this->assertSame(97, (int) $this->product->fresh()->quantity);

        $this->get(route('order.confirmation'))->assertOk()
            ->assertSee('Thank you')->assertSee('Paid online')->assertSee($order->order_no);
        $this->assertNull(session('cart'));
    }

    public function test_confirmation_without_an_order_goes_home(): void
    {
        $this->get(route('order.confirmation'))->assertRedirect(route('home'));
    }

    public function test_confirmation_ignores_other_peoples_orders(): void
    {
        $this->withSession($this->guestCart())->post('/user/checkout/order', $this->orderPayload());
        $orderNo = Order::latest('id')->first()->order_no;

        // A fresh visitor (new session) can't see it, and there's no id in the URL to guess.
        $this->flushSession();
        $this->get(route('order.confirmation') . '?order=' . $orderNo)->assertRedirect(route('home'));
    }

    public function test_signed_in_shoppers_still_go_to_my_orders(): void
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Needs a user in the local database.');
        }
        $this->actingAs($user)->get(route('order.confirmation'))
            ->assertRedirect(route('user.order.history'));
    }

    public function test_guest_returning_from_a_cancelled_payment_gets_checkout(): void
    {
        // Stripe's cancel URL is a GET to the checkout (was a 405 error page).
        $this->withSession($this->guestCart())->get('/user/checkout')->assertOk();
    }
}
