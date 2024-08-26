<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Cart;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function __construct() {
        return $this->activeTemplate = activeTemplate();
    }

    public function deposit() {
        $order_id        = session()->get('order_id');
        $order           = Order::where('id', $order_id)->where('order_status', 0)->first();
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Payment Methods';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'order'));
    }

    public function depositInsert(Request $request) {

        $request->validate([
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency'    => 'required',
        ]);

        $order_id = session()->get('order_id');
        $order    = Order::where('id', $order_id)->where('order_status', 0)->first();
        $user     = auth()->user();
        $gate     = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $order->total || $gate->max_amount < $order->total) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge    = $gate->fixed_charge + ($order->total * $gate->percent_charge / 100);
        $payable   = $order->total + $charge;
        $final_amo = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->order_id        = $order_id;
        $data->user_id         = $user->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->charge          = $charge;
        $data->amount          = $order->total;
        $data->rate            = $gate->rate;
        $data->final_amo       = $final_amo;
        $data->btc_amo         = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->try             = 0;
        $data->status          = 0;
        $data->save();
        session()->put('Track', $data->trx);
        return redirect()->route('user.deposit.preview');
    }

    public function depositPreview() {

        $track     = session()->get('Track');
        $data      = Deposit::where('trx', $track)->where('status', 0)->orderBy('id', 'DESC')->firstOrFail();
        $pageTitle = 'Payment Preview';
        return view($this->activeTemplate . 'user.payment.preview', compact('data', 'pageTitle'));
    }

    public function depositConfirm() {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', 0)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {

            $this->userDataUpdate($deposit);
            $notify[] = ['success', 'Your deposit request is queued for approval.'];
            return back()->withNotify($notify);
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

// for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($trx) {

        $general = GeneralSetting::first();
        $data    = Deposit::where('trx', $trx)->first();
        $order   = Order::where('id', $data->order_id)->where('order_status', 0)->first();
        $user    = User::find($data->user_id);

        if ($data->status == 0) {
            $data->status = 1;
            $data->save();

            $order->payment_status = 1;
            $order->save();

            $carts = Cart::where('user_id', $user->id)->get();

            foreach ($carts as $cart) {

                $product = Product::active()->findOrFail($cart->product_id);

                $price = productPrice($product);

                $orderDetail             = new OrderDetail();
                $orderDetail->order_id   = $order->id;
                $orderDetail->product_id = $cart->product_id;
                $orderDetail->quantity   = $cart->quantity;
                $orderDetail->price      = $price;
                $orderDetail->save();

                $product->decrement('quantity', $cart->quantity);
                $product->save();

                $cart->delete();
            }

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'Order successfully done via ' . $data->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
            $adminNotification->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $data->user_id;
            $transaction->amount       = $data->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $data->charge;
            $transaction->trx_type     = '-';
            $transaction->details      = 'Order successfully done via ' . $data->gatewayCurrency()->name;
            $transaction->trx          = $data->trx;
            $transaction->save();

            notify($user, 'ORDER_COMPLETE', [
                'method_name'     => 'Order successfully done via ' . $data->gatewayCurrency()->name,
                'user_name'       => $user->username,
                'subtotal'        => showAmount($order->subtotal),
                'total'           => showAmount($order->total),
                'shipping_charge' => showAmount($order->shipping_charge),
                'currency'        => $general->cur_text,
                'order_no'        => $order->order_no,
            ]);

        }

    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }

        if ($data->method_code > 999) {

            $pageTitle = 'Manual Payment Confirm';
            $method    = $data->gatewayCurrency();
            return view($this->activeTemplate . 'user.manual_payment.manual_confirm', compact('data', 'pageTitle', 'method'));
        }

        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        $order = Order::where('id', $data->order_id)->where('order_status', 0)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }

        $params = json_decode($data->gatewayCurrency()->gateway_parameter);

        $rules        = [];
        $inputField   = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $custom) {
                $rules[$key] = [$custom->validation];
                if ($custom->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg', 'jpeg', 'png']));
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }

                if ($custom->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }

                if ($custom->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }

                $inputField[] = $key;
            }

        }

        $this->validate($request, $rules);

        $directory  = date("Y") . "/" . date("m") . "/" . date("d");
        $path       = imagePath()['verify']['deposit']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField   = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type'       => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }

                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type'       => $inVal->type,
                            ];
                        }

                    }

                }

            }

            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }

        $data->status = 2; // pending
        $data->save();

        $order->payment_status = 0;
        $order->save();

        $user    = auth()->user();
        $carts   = Cart::where('user_id', $user->id)->get();
        $general = GeneralSetting::first();

        foreach ($carts as $cart) {

            $product = Product::active()->findOrFail($cart->product_id);
            $price = productPrice($product);

            $orderDetail             = new OrderDetail();
            $orderDetail->order_id   = $order->id;
            $orderDetail->product_id = $cart->product_id;
            $orderDetail->quantity   = $cart->quantity;
            $orderDetail->price      = $price;
            $orderDetail->save();

            $product->decrement('quantity', $cart->quantity);
            $product->save();

            $cart->delete();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Order successfully done via ' . $data->gatewayCurrency()->name;
        $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
        $adminNotification->save();

        notify($user, 'ORDER_COMPLETE', [
            'method_name'     => 'Order successfully done via ' . $data->gatewayCurrency()->name,
            'user_name'       => $user->username,
            'subtotal'        => showAmount($order->subtotal),
            'total'           => showAmount($order->total),
            'shipping_charge' => showAmount($order->shipping_charge),
            'currency'        => $general->cur_text,
            'order_no'        => $order->order_no,
        ]);

        $notify[] = ['success', 'Your payment request has been processed.'];
        return redirect()->route('user.order.history')->withNotify($notify);
    }
}
