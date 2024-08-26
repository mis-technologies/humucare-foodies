<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller {
    public function index(Request $request) {
        $pageTitle    = 'All Shipping Methods';
        $emptyMessage = 'No shipping methods found';
        $shippings    = ShippingMethod::query();

        if ($request->search) {
            $shippings->where('name', 'LIKE', "%$request->search%");
        }

        $shippings = $shippings->latest()->paginate(getPaginate());
        return view('admin.shipping.index', compact('pageTitle', 'emptyMessage', 'shippings'));
    }

    public function store(Request $request, $id = 0) {

        $request->validate([
            'name'  => 'required|max: 40|unique:shipping_methods,name,' . $id,
            'price' => 'required|numeric|min:0',
        ]);

        if ($id) {
            $shipping         = ShippingMethod::findOrFail($id);
            $shipping->status = $request->status ? 1 : 0;
            $notification     = 'Shipping method updated successfully.';
        } else {
            $shipping     = new ShippingMethod();
            $notification = 'Shipping method added successfully.';
        }

        $shipping->name  = $request->name;
        $shipping->price = $request->price;
        $shipping->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

}
