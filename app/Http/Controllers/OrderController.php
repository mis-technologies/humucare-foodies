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

class OrderController extends Controller
{

    public function __construct() {
        $this->activeTemplate = activeTemplate();
    }

    public function orderHistory() {
        $pageTitle    = 'My Orders';
        $emptyMessage = 'No order found';
        $orders       = Order::where('user_id', auth()->id())->latest()->with('deposit')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.order.history', compact('pageTitle', 'emptyMessage', 'orders'));
    }

    public function orderDetail($id) {
        $pageTitle    = 'Order Detail';
        $emptyMessage = 'No product found';
        $order        = Order::where('id', $id)->where('user_id', auth()->user()->id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit.gateway', 'user'])->firstOrFail();
        return view($this->activeTemplate . 'user.order.detail', compact('pageTitle', 'emptyMessage', 'order'));
    }

    public function fileDownload($id,$order_id) {

        $order     = Order::where('id', $order_id)->where('user_id', auth()->user()->id)->firstOrFail();
        $product   = Product::active()->findOrFail($id);
        $file      = $product->digi_file;
        $path      = imagePath()['digital_item']['path'];
        $full_path = $path . '/' . $file;
        return response()->download($full_path);
    }


}
