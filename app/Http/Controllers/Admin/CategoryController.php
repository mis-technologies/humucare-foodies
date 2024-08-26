<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function index(Request $request) {

        $pageTitle  = 'All Categories';
        $categories   = Category::query();

        if ($request->search) {
            $categories->where('name', 'LIKE', "%$request->search%");
        }

        $emptyMessage = 'No category found';
        $categories = $categories->latest()->paginate(getPaginate());
        return view('admin.category.index', compact('pageTitle', 'emptyMessage', 'categories'));
    }

    public function store(Request $request, $id = 0) {

        $validate = [
            'name'  => 'required|max: 40|unique:categories,name,'.$id,
        ];

        if($id == 0){
            $category = new Category();
            $validate['image'] = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
            $notification = 'Category added successfully.';
            $oldFile = null;
        }else{
            $category = Category::findOrFail($id);
            $validate['image'] = ['image', new FileTypeValidate(['jpeg', 'jpg', 'png']),];
            $category->status   = $request->status ? 1 : 0;
            $notification = $category->name . ' has been updated.';
            $oldFile = $category->image;
        }

        $request->validate($validate);

        $path = imagePath()['category']['path'];
        $size = imagePath()['category']['size'];


        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($request->image, $path, $size, $oldFile);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
            $category->image = $filename;
        }

        $category->name     = $request->name;
        $category->featured = $request->featured ? 1 : 0;
        $category->save();

        $notify[] = ['success',$notification];
        return back()->withNotify($notify);
    }
}
