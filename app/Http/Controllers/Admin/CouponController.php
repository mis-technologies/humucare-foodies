<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request) {
        $pageTitle    = 'All Coupons';
        $emptyMessage = 'No coupon found';
        $coupons      = Coupon::query();

        if ($request->search) {
            $coupons->where('name', 'LIKE', "%$request->search%");
        }

        $coupons = $coupons->latest()->paginate(getPaginate());
        return view('admin.coupon.index', compact('pageTitle', 'emptyMessage', 'coupons'));
    }

    public function store(Request $request, $id = 0) {

        $request->validate([
            'name'          =>  'required|max:7|unique:coupons,name,' . $id,
            'discount'      => 'required|numeric|gt:0',
            "start_date"    => 'required|date|date_format:Y-m-d',
            "end_date"      => 'required|date|date_format:Y-m-d',
            'discount_type' => 'required|in:1,2',
            'min_order'     => 'required|numeric|gte:0',
        ]);

        if($id){
            $coupon = Coupon::findOrFail($request->id);
            $coupon->status        = $request->status ? 1 : 0;
            $notification = 'Coupon updated successfully.';
        }else{
            $coupon                = new Coupon();
            $notification = 'Coupon added successfully.';
        }

        $coupon->name          = $request->name;
        $coupon->discount      = $request->discount;
        $coupon->start_date    = $request->start_date;
        $coupon->end_date      = $request->end_date;
        $coupon->discount_type = $request->discount_type;
        $coupon->min_order     = $request->min_order;
        $coupon->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
