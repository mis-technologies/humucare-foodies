<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ManageOrderController extends Controller {

    public function allOrder(Request $request) {
        $pageTitle    = 'All Orders';
        $emptyMessage = 'No order found';

        $orders = Order::query();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function pendingOrder(Request $request) {
        $pageTitle    = 'Pending Orders';
        $emptyMessage = 'No order found';

        $orders = Order::pending();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function confirmOrder(Request $request) {
        $pageTitle    = 'Confirmed Orders';
        $emptyMessage = 'No order found';

        $orders = Order::confirmed();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function shippedOrder(Request $request) {
        $pageTitle    = 'Shipped Orders';
        $emptyMessage = 'No order found';

        $orders = Order::shipped();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function deliveredOrder(Request $request) {
        $pageTitle    = 'Delivered Orders';
        $emptyMessage = 'No order found';

        $orders = Order::delivered();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function cancelOrder(Request $request) {
        $pageTitle    = 'Cancel Orders';
        $emptyMessage = 'No order found';

        $orders = Order::cancel();

        if ($request->search) {
            $search = request()->search;
            $orders = $orders->where(function($q) use($search){
                    $q->where('order_no',$search)->orWhereHas('user',function($query) use ($search){
                        $query->where('username',$search);
                    });
            });
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));

    }

    public function userOrders(Request $request,$id){

        $user = User::findOrFail($id);

        $pageTitle    = 'Order Logs of'.' '.$user->username;
        $emptyMessage = 'No order found';

        $orders = Order::where('user_id',$user->id);

        if ($request->search) {
            $orders->where('order_no', $request->search);
        }

        $orders = $orders->with('user')->latest()->paginate(getPaginate());
        return view('admin.order.index', compact('pageTitle', 'emptyMessage', 'orders'));
    }

    public function userTransactions(Request $request , $id){
        $user = User::findOrFail($id);
        $pageTitle = 'Transaction Logs of'.' '.$user->username;
        $transactions = Transaction::where('user_id',$user->id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions.';
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'emptyMessage')); 
    }



    public function orderDetail($id) {
        $pageTitle    = 'Order Detail';
        $emptyMessage = 'No product found';
        $order        = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user'])->firstOrFail();
        return view('admin.order.detail', compact('pageTitle', 'order', 'emptyMessage'));
    }

    public function orderStatus(Request $request, $id) {
        $request->validate([
            'order_status' => 'required|integer',
        ]);

        $order               = Order::where('id', $id)->with('user', 'orderDetail')->firstOrFail();
        $order->order_status = $request->order_status;

        if ($request->order_status == 1) {
            $status = 'Confirmed';
        } elseif ($request->order_status == 2) {
            $status = 'Shipped';
        } elseif ($request->order_status == 3) {
            $status = 'Delivered';

            foreach ($order->orderDetail as $detail) {
                $product = Product::findOrFail($detail->product_id);
                $product->increment('sale_count', $detail->quantity);
                $product->save();
            }

            if ($order->payment_type == 2) {
                $order->payment_status = 1;
            }

        } else {
            $status = 'Cancelled';

            if ($order->payment_status == 0) {
                $order->payment_status = 9;
            }

            foreach ($order->orderDetail as $detail) {
                $product = Product::findOrFail($detail->product_id);
                $product->increment('quantity', $detail->quantity);
                $product->save();
            }
        }

        $order->save();

        $user    = $order->user;
        $general = GeneralSetting::first();

        notify($user, 'ORDER_STATUS', [
            'method_name' => 'Your order has now ' . $status,
            'user_name'   => $user->username,
            'order_no'    => $order->order_no,
            'total'       => showAmount($order->total),
            'currency'    => $general->cur_text,
        ]);

        $notify[] = ['success', 'Order status change successfully.'];
        return back()->withNotify($notify);
    }

    public function invoice($id) {
        $pageTitle    = 'Print Invoice';
        $emptyMessage = 'No order found';
        $order        = Order::where('id', $id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user'])->firstOrFail();
        return view('admin.order.invoice', compact('order', 'pageTitle', 'emptyMessage'));
    }

}
