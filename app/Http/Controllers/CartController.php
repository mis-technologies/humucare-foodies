<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller {

    public function __construct() {
        $this->activeTemplate = activeTemplate();
    }

    public function addToCart(Request $request) {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id ?? null;


        if ($request->quantity > $product->quantity) {
            return response()->json(['error' => 'Requested quantity is not available in our stock.']);
        }

        if ($user_id) {
            $cart = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();

            if ($cart) {

                if ($cart->quantity >= $product->quantity) {
                    return response()->json(['error' => 'Requested quantity is not available in our stock.']);
                }

                $cart->quantity += $request->quantity;
                $cart->save();

            } else {

                $cart             = new Cart();
                $cart->user_id    = auth()->user()->id;
                $cart->product_id = $request->product_id;
                $cart->quantity   = $request->quantity;
                $cart->save();

            }

        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$product->id])) {

                if ($cart[$product->id]['quantity'] >= $product->quantity) {
                    return response()->json(['error' => 'Requested quantity is not available in our stock.']);
                }

                $cart[$product->id]['quantity'] += $request->quantity;
            } else {
                $general = GeneralSetting::first();
                $cart[$product->id] = [
                    "name"          => $product->name,
                    "price"         => $product->price,
                    "discount"      => ($product->today_deals == 1) ? $general->discount : $product->discount,
                    "discount_type" => ($product->today_deals == 1) ? $general->discount_type : $product->discount_type,
                    "image"         => $product->image,
                    "product_id"    => $product->id,
                    "quantity"      => $request->quantity,
                ];
            }

        }

        session()->put('cart', $cart);
        return response()->json(['success' => 'Product added to shopping cart']);

    }

    public function updateCart(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id ?? null;

        if ($request->quantity > $product->quantity) {
            return response()->json(['error' => 'Requested quantity is not available in our stock.']);
        }

        if ($user_id != null) {
            $cart           = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();
            $cart->quantity = $request->quantity;
            $cart->save();
        } else {
            $cart                                   = session()->get('cart');
            $cart[$request->product_id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json(['success' => 'Cart was successfully updated.']);

    }

    public function deleteCart(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user_id = auth()->user()->id ?? null;

        if ($user_id) {
            $cart = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();
            $cart->delete();
        } else {
            $cart = session()->get('cart');
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => 'Product was successfully removed.']);
    }

    public function getCartCount() {

        $user_id = auth()->user()->id ?? null;

        if ($user_id) {
            return Cart::where('user_id', $user_id)->count();
        }

        $cart    = session()->get('cart');
        if($cart){
            return count($cart);
        }
        return 0;
    }

    public function cartProducts() {
        $pageTitle    = 'My Cart';
        $emptyMessage = 'There is no product in the cart.';
        $user_id      = auth()->user()->id ?? null;
        $carts        = [];

        $cart  = session()->get('cart');
        $carts = json_decode(json_encode($cart)) ?? [];

        if ($user_id) {
            $carts = Cart::where('user_id', $user_id)->with('product')->orderBy('id', 'asc')->get();
        }

        session()->forget('total');
        return view($this->activeTemplate . 'cart', compact('pageTitle', 'emptyMessage', 'carts'));
    }

    public function couponApply(Request $request) {

        $coupon = Coupon::where('name', $request->coupon)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->first();

        if (!$coupon) {
            return response()->json(['error' => 'There was no coupon found.']);
        }

        $user_id = auth()->user()->id ?? null;
        $general = GeneralSetting::first();

        if ($user_id) {
            $carts = Cart::where('user_id', $user_id)->with('product')->get();

            foreach ($carts as $cart) {
                $sumPrice = 0;
                $product  = Product::active()->where('id', $cart->product->id)->first();
                $price = productPrice($product);
                $sumPrice = $sumPrice + ($price * $cart->quantity);
                $total[]  = $sumPrice;
            }

        } else {
            $carts = session()->get('cart');
            foreach ($carts as $cart) {
                $sumPrice = 0;
                $product  = Product::active()->where('id', $cart['product_id'])->first();
                $price = productPrice($product);
                $sumPrice = $sumPrice + ($price * $cart['quantity']);
                $total[]  = $sumPrice;
            }
        }

        $subtotal = array_sum($total);

        if ($coupon->min_order > $subtotal) {
            return response()->json(['error' => 'Sorry, you have to order a minimum amount of ' . $general->cur_sym . showAmount($coupon->min_order)]);
        }

        if ($coupon->discount_type == 1) {
            $discount = $coupon->discount;
        } else {
            $discount = $subtotal * $coupon->discount / 100;
        }

        $totalAmount = $subtotal - $discount;

        $total = [
            'coupon_name'   => $coupon->name,
            'coupon_id'     => $coupon->id,
            'discount_type' => $coupon->discount_type,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'totalAmount'   => $totalAmount,
        ];
        session()->put('total', $total);
        return response()->json([
            'success'     => 'Coupon has been successfully added.',
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'totalAmount' => $totalAmount,
        ]);
    }

}
