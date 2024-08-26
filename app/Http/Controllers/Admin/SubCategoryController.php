<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller {

    public function index(Request $request) {

        $pageTitle     = 'All Subcategories';
        $emptyMessage  = 'No subcategory found';
        $subcategories = SubCategory::query();


        if ($request->search) {
           $search = request()->search;
           $subcategories = $subcategories->where(function($q) use($search){
                $q->where('name','like',"%$search%")->orWhereHas('category',function($query) use ($search){
                    $query->where('name','like',"%$search%");
                });
           });
           
        }

        $subcategories = $subcategories->with('category')->latest()->paginate(getPaginate());
        $categories    = Category::where('status', 1)->orderBy('name')->get();

        return view('admin.subcategory.index', compact('pageTitle', 'emptyMessage', 'subcategories', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required|unique:sub_categories,name,' . $id,
        ]);

        if ($id) {
            $subcategory         = SubCategory::findOrFail($request->id);
            $subcategory->status = $request->status ? 1 : 0;
            $notification        = 'Subcategory updated successfully.';

        } else {
            $subcategory  = new SubCategory();
            $notification = 'Subcategory added successfully.';
        }

        $subcategory->category_id = $request->category_id;
        $subcategory->name        = $request->name;
        $subcategory->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

}
