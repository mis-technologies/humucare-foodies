<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\ProductPricePerLiter;
use App\Models\ProductPricePerMilliliter;
use App\Models\User;
use App\Models\VolumeRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class CartController extends Controller {

    public function __construct() {
        $this->activeTemplate = activeTemplate();
    }

    public function addToCart(Request $request) {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|gt:0',
            // 'volume'   => 'numeric|gt:0',
            // 'price'   => 'numeric|gt:0',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id ?? null;

        if ($request->quantity > $product->quantity) {
            return response()->json(['error' => 'Requested quantity is not available in our stock.']);
        }

        // Validate + price the chosen modifiers server-side. Options that are
        // not attached to this dish are ignored, so prices can't be smuggled.
        $resolved = resolveProductOptions($product, $request->options);

        if ($resolved['error']) {
            return response()->json(['error' => $resolved['error']]);
        }

        $options      = $resolved['options'];
        $optionsPrice = $resolved['price'];
        // Same dish with different modifiers = a different basket line.
        $signature    = md5(json_encode($options));

        if ($user_id) {
            $cart = Cart::where('user_id', $user_id)
                ->where('product_id', $request->product_id)
                ->get()
                ->first(function ($row) use ($signature) {
                    return md5(json_encode($row->options ?: [])) === $signature;
                });

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
                // $cart->liter   = $request->liter;
                // $cart->price_per_liter = $request->pricePerLiter;
                $cart->milliliter   = $request->volume;
                $cart->price_per_milliliter = $request->price;
                $cart->options       = $options;
                $cart->options_price = $optionsPrice;
                $cart->save();

            }

        } else {
            $cart = session()->get('cart', []);
            // key by dish + modifiers so "Large" and "Regular" are separate lines
            $key  = $options ? $product->id . '-' . substr($signature, 0, 8) : $product->id;

            if (isset($cart[$key])) {

                if ($cart[$key]['quantity'] >= $product->quantity) {
                    return response()->json(['error' => 'Requested quantity is not available in our stock.']);
                }

                $cart[$key]['quantity'] += $request->quantity;
            } else {
                $general = GeneralSetting::first();
                $cart[$key] = [
                    "name"          => $product->name,
                    "price"         => $product->price,
                    "discount"      => ($product->today_deals == 1) ? $general->discount : $product->discount,
                    "discount_type" => ($product->today_deals == 1) ? $general->discount_type : $product->discount_type,
                    "image"         => $product->image,
                    "product_id"    => $product->id,
                    "quantity"      => $request->quantity,
                    // "liter"         => $request->liter,
                    // "price_per_liter" => $request->pricePerLiter,
                    "milliliter"         => $request->volume,
                    "price_per_milliliter" => $request->price,
                    "options"        => $options,
                    "options_price"  => $optionsPrice,
                    "cart_key"       => $key,
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

        // A dish can occupy several basket lines (same dish, different
        // modifiers), so prefer the line key and only fall back to product_id.
        if ($user_id != null) {
            $cart = $request->cart_key
                ? Cart::where('user_id', $user_id)->where('id', $request->cart_key)->first()
                : Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();

            if (!$cart) {
                return response()->json(['error' => 'Cart item not found.']);
            }

            $cart->quantity = $request->quantity;
            $cart->save();
        } else {
            $cart = session()->get('cart', []);
            $key  = $request->cart_key && isset($cart[$request->cart_key])
                ? $request->cart_key
                : $request->product_id;

            if (!isset($cart[$key])) {
                return response()->json(['error' => 'Cart item not found.']);
            }

            $cart[$key]["quantity"] = $request->quantity;
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
            $cart = $request->cart_key
                ? Cart::where('user_id', $user_id)->where('id', $request->cart_key)->first()
                : Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->first();

            if (!$cart) {
                return response()->json(['error' => 'Cart item not found.']);
            }

            $cart->delete();
        } else {
            $cart = session()->get('cart', []);
            $key  = $request->cart_key && isset($cart[$request->cart_key])
                ? $request->cart_key
                : $request->product_id;

            unset($cart[$key]);
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
            // product.category is rendered as the row tag on the cart page
            $carts = Cart::where('user_id', $user_id)->with('product.category')->orderBy('id', 'asc')->get();
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
                $ppl = $cart['liter'] * $cart['pricePerLiter'];
                $ppml = $cart['milliliter'] * $cart['pricePerMilliliter'];
                $sumPrice = $sumPrice + ($price * $cart['quantity']) + ($ppl) + ($ppml);
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

    public function alert()
    {
        
        Alert::success('Success', 'You are eligible for Home service Discount, Use Homeservice below.');

        return response()->json([

            'success'=>'alert',

        ], 200);
    }

}
