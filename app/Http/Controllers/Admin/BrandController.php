<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class BrandController extends Controller {

    public function index(Request $request) {

        $pageTitle = 'All Brands';
        $emptyMessage = 'No brand found';
        $brands       = Brand::query();

        if ($request->search) {
            $brands->where('name', 'LIKE', "%$request->search%");
        }

        $brands = $brands->latest()->paginate(getPaginate());
        return view('admin.brand.index', compact('pageTitle', 'emptyMessage', 'brands'));
    }

    public function store(Request $request, $id = 0) {

        $validate = [
            'name'  => 'required|max: 40|unique:brands,name,'.$id,
        ];
        if($id == 0){
            $brand = new Brand();
            $validate['image'] = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
            $notification = 'Brand added successfully.';
            $oldFile = null;
        }else{
            $brand = Brand::findOrFail($id);
            $validate['image'] = ['image', new FileTypeValidate(['jpeg', 'jpg', 'png']),];
            $brand->status   = $request->status ? 1 : 0;
            $notification = $brand->name . ' has been updated.';
            $oldFile = $brand->image;
        }

        $request->validate($validate);

        $path = imagePath()['brand']['path'];
        $size = imagePath()['brand']['size'];

        if ($request->hasFile('image')) {
            try {
                $brand->image  = uploadImage($request->image, $path, $size, $oldFile);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $brand->name     = $request->name;
        $brand->featured = $request->featured ? 1 : 0;
        $brand->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

}
