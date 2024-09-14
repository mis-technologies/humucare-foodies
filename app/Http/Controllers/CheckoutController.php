<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Cart;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CheckoutController extends Controller {
    public function __construct() {
        $this->activeTemplate = activeTemplate();
    }

    public function checkout(Request $request) {

        $pageTitle = 'Checkout';
        $total     = session()->get('total');

            // Initialize $user_id to null
            $user_id = null;

            // Check if user is authenticated
            if (auth()->check()) {
                $user_id = auth()->user()->id;
            }

        // dd($request->all());
        if ($request->has('total')) {
            $data['subtotal'] = $request->total;
            $data['discount'] = 0;
            $data['total']    = $request->total;
        } else {
            $subtotal = $this->cartSubTotal($user_id);
            if ($subtotal == 0) {
                abort(404);
            }
            $data['subtotal'] = $subtotal;
            $data['discount'] = showAmount(0.00);
            $data['total']    = $subtotal;
        }
        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $shippingMethod = ShippingMethod::where('status', 1)->get();
        return view($this->activeTemplate . 'checkout', compact('pageTitle', 'data', 'countries', 'shippingMethod'));

    }

    public function milCheckout(Request $request) {

        $pageTitle = 'Checkout';
        $total     = session()->get('total');

         // Initialize $user_id to null
            $user_id = null;

            // Check if user is authenticated
            if (auth()->check()) {
                $user_id = auth()->user()->id;
            }


        if ($request->has('total_hs')) {
            $data['subtotal'] = $request->total_hs;
            $data['discount'] = $request->toatl_hs;
            $data['total']    = $request->toatl_hs;
        } else {
            $subtotal = $this->cartSubTotal($user_id);
            if ($subtotal == 0) {
                abort(404);
            }
            $data['subtotal'] = $subtotal/2;
            $data['discount'] = showAmount(0.00);
            $data['total']    = $subtotal/2;
        }
        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $shippingMethod = ShippingMethod::where('status', 1)->get();
        return view($this->activeTemplate . 'checkout', compact('pageTitle', 'data', 'countries', 'shippingMethod'));

    }


    public function lCheckout(Request $request) {
        $pageTitle = 'Checkout';
        $total     = session()->get('total');
        $user_id   = auth()->user()->id;

        $hasManyItemsWith1l = $this->checkCartItemsForUserLiter($user_id);

        if ($hasManyItemsWith1l || $total) {
            $data['subtotal'] = 350;
            $data['discount'] = showAmount(0.00);
            $data['total']    = 350;
        }else {

            Alert::info('info', 'Your cart items doesnt meet the home service requirement.');
            return back();
            // $subtotal = $this->cartSubTotal($user_id);
            // if ($subtotal == 0) {
            //     abort(404);
            // }
            // $data['subtotal'] = $subtotal;
            // $data['discount'] = showAmount(0.00);
            // $data['total']    = $subtotal;
        }



        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $shippingMethod = ShippingMethod::where('status', 1)->get();

        return view($this->activeTemplate . 'checkout', compact('pageTitle', 'data', 'countries', 'shippingMethod'));
    }
    private function checkCartItemsForUserLiter($userId)
    {
        $cartItemsCount = Cart::where('user_id', $userId)
                                ->where('liter', 1)
                                ->count();

        return $cartItemsCount >= 15;
    }



    public function shippingMethod(Request $request) {
        $request->validate([
            'ship_id'     => 'required|integer',
            'totalAmount' => 'required|numeric|gt:0',
        ]);
        $shipping = ShippingMethod::where('id', $request->ship_id)->where('status', 1)->first();

        if (!$shipping) {
            return response()->json(['error' => 'Shipping method unable to locate']);
        }

        $grandTotal = $request->totalAmount + $shipping->price;
        return response()->json([
            'success'       => true,
            'grandTotal'    => $grandTotal,
            'shippingPrice' => $shipping->price,
        ]);

    }

    public function order(Request $request) {


        $request->validate([
            'firstname'       => 'required',
            'lastname'        => 'required',
            'mobile'          => 'required',
            'email'           => 'required',
            'country'         => 'required',
            'address'         => 'required',
            'state'           => 'required',
            'city'            => 'required',
            'zip'             => 'required',
            'shipping_method' => 'required|integer',
            'payment_type'    => 'required|integer|in:1,2',
        ]);

        if (Auth::check()) {

            $user       = auth()->user();
            $subtotal   = $this->cartSubTotal($user->id);
        }


        if (!Auth::check()) {
            $subtotal   =0;
        }


        $shipping   = ShippingMethod::where('id', $request->shipping_method)->where('status', 1)->first();
        if(!$shipping){
            $notify[] = ['error', 'Shipping method unable to locate.'];
            return back()->withNotify($notify)->withInput();
        }
        $grandTotal = $subtotal + $shipping->price;

        $total = session()->get('total');

        if ($total) {
            $discount   = $total['discount'];
            $coupon_id  = $total['coupon_id'];
            $grandTotal = $grandTotal - $discount;
        }


        if ($request->has('hs_price')) {

            $grandTotal = $request->hs_price + $shipping->price;


        }
        if (!Auth::check() && $request->has('price')) {

            $grandTotal = $request->price + $shipping->price;
        // dd($grandTotal);

        }

        $address = [
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'country' => $request->country,
            'city'    => $request->city,
        ];

        $order                  = new Order();
        $order->user_id         = $user->id ?? 0;
        $order->order_no        = getTrx();
        $order->subtotal        = $subtotal;
        $order->discount        = $discount ?? 0;
        $order->shipping_charge = $shipping->price;
        $order->total           = $grandTotal;
        $order->coupon_id       = $coupon_id ?? 0;
        $order->shipping_id     = $shipping->id;
        $order->address         = json_encode($address);
        $order->payment_type    = $request->payment_type;

        if ($request->payment_type == 1) {
            $order->save();
            session()->put('order_id', $order->id);


            return redirect()->route('user.deposit');
        }

        $order->order_status = 0;
        $order->save();

        $cart = session()->get('cart', []);
        // dd($cart);
        if (Auth::check()) {

            $carts = Cart::where('user_id', $user->id)->get();
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

        }else {

            foreach ($cart as $sessionCart) {

                $product = Product::active()->findOrFail($sessionCart['product_id']);
                $price = productPrice($product);

                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $sessionCart['product_id'];
                $orderDetail->quantity = $sessionCart['quantity'];
                $orderDetail->price = $price;
                $orderDetail->save();

                $product->decrement('quantity', $sessionCart['quantity']);
                $product->save();
            }

            session()->forget('cart');
        }



        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id ?? 0;
        $adminNotification->title     = 'Order successfully done via Cash on delivery.';
        $adminNotification->click_url = urlPath('admin.orders.detail',$order->id);
        $adminNotification->save();

        if (Auth::check()) {

            notify($user, 'ORDER_COMPLETE', [
                'method_name'     => 'Order successfully done via Cash on delivery.',
                'user_name'       => $user->username ?? 0,
                'subtotal'        => showAmount($subtotal),
                'shipping_charge' => showAmount($shipping->price),
                'total'           => showAmount($grandTotal),
                'currency'        => $general->cur_text,
                'order_no'        => $order->order_no,
            ]);
        }

        $notify[] = ['success', 'Order successfully completed.'];
        return redirect()->route('user.order.history')->withNotify($notify);
    }

    protected function cartSubTotal($user_id) {

        $carts = Cart::where('user_id', $user_id)->with('product')->get();

        $total = [0];

        foreach ($carts as $cart) {
            $sumPrice = 0;
            $product  = Product::active()->where('id', $cart->product->id)->first();
            $price = productPrice($product);

            $sumPrice = $sumPrice + ($price * $cart->quantity);
            $total[]  = $sumPrice;
        }

        $subtotal = array_sum($total);
        return $subtotal;
    }

}
