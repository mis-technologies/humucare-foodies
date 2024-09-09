<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductPricePerLiter;
use App\Models\ProductPricePerMilliliter;
use App\Models\ProductPricePerVolume;
use App\Models\Review;
use App\Models\VolumeRange;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function index(Request $request) {
        $pageTitle    = 'All Products';
        $emptyMessage = 'No product found';
        $products     = Product::query();

        if ($request->search) {
            $products->where('name', 'LIKE', "%$request->search%")
                ->orWhere('price', 'LIKE', "%$request->search%")
                ->orWhere('product_sku', 'LIKE', "%$request->search%");
        }

        $products = $products->latest()->paginate(getPaginate());
        return view('admin.product.index', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function create() {
        $pageTitle   = 'Create New Product';
        $allCategory = Category::where('status', 1)->with(['subcategories' => function ($q) {
            $q->where('status', 1);
        },
        ])->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();

        $volRanges = VolumeRange::all();
        return view('admin.product.create', compact('volRanges','pageTitle', 'allCategory', 'brands'));
    }

    public function store(Request $request) {

        if ($request->hasFile('files')) {

            if (count($request->file('files')) >= 8) {
                $notify[] = ['error', 'Maximum 8 files can be uploaded.'];
                return back()->withNotify($notify)->withInput();
            }

        }

        $request->validate([
            'name'           => 'required|max:255',
            'product_sku'    => 'nullable|string|max:40|unique:products',
            'category_id'    => 'required',
            'subcategory_id' => 'required',
            'brand'          => 'required',
            'price'          => 'required|numeric|gt:0',
            'quantity'       => 'required|integer|gt:0',
            'discount'       => 'numeric|min:0|max:100',
            'discount_type'  => 'required|in:1,2',
            'digital_item'   => 'required',
            'file_type'      => 'required_if:digital_item,1',
            'image'          => ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'files'          => 'nullable|array',
            'files.*'        => ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'digi_file'      => ['required_if:file_type,1', new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            'digi_link'      => 'required_if:file_type,2',
        ]);



        $filename = '';
        $path     = imagePath()['product']['thumb']['path'];
        $size     = imagePath()['product']['thumb']['size'];

        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($request->image, $path, $size);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }

        }

        $galleryFileName = [];

        if ($request->hasFile('files')) {

            $path = imagePath()['product']['gallery']['path'];
            $size = imagePath()['product']['gallery']['size'];

            foreach ($request->file('files') as $file) {
                try {
                    $arr[] = uploadImage($file, $path, $size);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Image could not be uploaded.'];
                    return back()->withNotify($notify)->withInput();
                }

            }

            $galleryFileName = $arr;
        }

        $digiFile = '';
        $path     = imagePath()['digital_item']['path'];

        if ($request->hasFile('digi_file')) {
            try {
                $digiFile = uploadFile($request->digi_file, $path);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload your file'];
                return back()->withNotify($notify)->withInput();
            }

        }

        $input_feature = [];

        if ($request->has('feature_title')) {

            for ($a = 0; $a < count($request->feature_title); $a++) {
                $arr                  = [];
                $arr['feature_title'] = $request->feature_title[$a];
                $arr['feature_desc']  = $request->feature_desc[$a];
                $input_feature[]      = $arr;
            }

        }

        $product                   = new Product();
        $product->name             = $request->name;
        $product->product_sku      = $request->product_sku;
        $product->category_id      = $request->category_id;
        $product->subcategory_id   = $request->subcategory_id;
        $product->brand_id         = $request->brand;
        $product->slug             = slug($request->name);
        $product->price            = $request->price;
        $product->discount         = $request->discount;
        $product->discount_type    = $request->discount_type;
        $product->quantity         = $request->quantity;
        $product->hot_deals        = $request->hot_deals ? 1 : 0;
        $product->featured_product = $request->featured_product ? 1 : 0;
        $product->today_deals      = $request->today_deals ? 1 : 0;
        $product->description      = $request->description;
        $product->summary          = $request->summary;
        $product->features         = json_encode($input_feature);
        $product->digital_item     = $request->digital_item;
        $product->file_type        = $request->file_type;
        $product->digi_file        = $digiFile;
        $product->digi_link        = $request->digi_link;
        $product->image            = $filename;
        $product->save();

        foreach ($galleryFileName as $file) {
            $files             = new ProductGallery();
            $files->product_id = $product->id;
            $files->image      = $file;
            $files->save();
        }


        foreach ($request->volumes as $volumeData) {
            if (isset($volumeData['volume']) && isset($volumeData['price'])) {
                $volume = $volumeData['volume'];
                $price = $volumeData['price'];

                // Ensure the volume and price are not empty before saving
                if (!empty($volume) && !empty($price)) {
                    $productPrice = new ProductPricePerVolume();
                    $productPrice->product_id = $product->id; // Reference the saved product
                    $productPrice->volume = $volume;
                    $productPrice->price = $price;
                    $productPrice->save();
                }
            }
        }



        // if ($request->hasAny('price_per_liter')) {

        //     $product_price_per_liter = new ProductPricePerLiter();
        //     $product_price_per_liter->price = $request->price_per_liter;
        //     $product_price_per_liter->liter = 1;
        //     $product_price_per_liter->product_id = $product->id;
        //     $product_price_per_liter->save();

        // }

        // if ($request->hasAny('price_per_milliliter')) {

        //     $product_price_per_liter = new ProductPricePerMilliliter();
        //     $product_price_per_liter->price = $request->price_per_milliliter;
        //     $product_price_per_liter->milliliter = 1;
        //     $product_price_per_liter->product_id = $product->id;
        //     $product_price_per_liter->save();

        // }

        $notify[] = ['success', 'Product added successfully.'];
        return redirect()->back()->withNotify($notify);
    }

    public function edit($id) {
        $pageTitle   = 'Update Product';
        $product     = Product::where('id', $id)->with('ProductGallery')->first();
        $allCategory = Category::where('status', 1)->with(['subcategories' => function ($q) {
            $q->where('status', 1);
        },
        ])->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $ppl = ProductPricePerLiter::whereProductId($id)->first();

        $ppml = ProductPricePerMilliliter::whereProductId($id)->first();

        $volRanges = ProductPricePerVolume::where('product_id', $id)->get();

        if ($volRanges->isEmpty()) {

            $volRanges = VolumeRange::all();

        }

        return view('admin.product.edit', compact('volRanges','ppml','ppl','pageTitle', 'product', 'allCategory', 'brands'));
    }

    public function update(Request $request, $id) {
        $product = Product::where('id', $id)->with('ProductGallery')->first();
        $request->validate([
            'name'           => 'required',
            'product_sku'    => 'nullable|unique:products,product_sku,' . $product->id,
            'category_id'    => 'required',
            'subcategory_id' => 'required',
            'brand'          => 'required',
            'price'          => 'required|numeric|gt:0',
            'quantity'       => 'required|integer|gt:0',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'digital_item'   => 'required',
            'file_type'      => 'required_if:digital_item,1',
            'image'          => ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'files'          => 'nullable|array',
            'files.*'        => ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'digi_file'      => ['nullable', new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            'digi_link'      => 'required_if:file_type,2',
        ]);

        $filename = $product->image;

        $path = imagePath()['product']['thumb']['path'];
        $size = imagePath()['product']['thumb']['size'];

        $galleryPath = imagePath()['product']['gallery']['path'];
        $gallerySize = imagePath()['product']['gallery']['size'];

        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($request->image, $path, $size,$filename);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify)->withInput();
            }

        }

        $oldImages   = $product->ProductGallery->pluck('id')->toArray();
        $imageRemove = array_values(array_diff($oldImages, $request->imageId ?? []));

        foreach ($imageRemove as $remove) {
            $singleImage = ProductGallery::find($remove);
            $location    = $galleryPath;
            removeFile($location . '/' . $singleImage->image);
            $singleImage->delete();
        }

        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $key => $image) {

                if (isset($request->imageId[$key])) {
                    $singleImage = ProductGallery::find($request->imageId[$key]);
                    $location    = $galleryPath;
                    removeFile($location . '/' . $singleImage->image);
                    $singleImage->delete();
                    $newImage           = uploadImage($image, $galleryPath, $gallerySize);
                    $singleImage->image = $newImage;
                    $singleImage->save();
                } else {
                    try {
                        $newImage = uploadImage($image, $galleryPath, $gallerySize);
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Image could not be uploaded.'];
                        return back()->withNotify($notify);
                    }

                    $productImage             = new ProductGallery();
                    $productImage->product_id = $product->id;
                    $productImage->image      = $newImage;
                    $productImage->save();
                }

            }

        }

        $input_feature = [];

        if ($request->has('feature_title')) {

            for ($a = 0; $a < count($request->feature_title); $a++) {
                $arr                  = [];
                $arr['feature_title'] = $request->feature_title[$a];
                $arr['feature_desc']  = $request->feature_desc[$a];
                $input_feature[]      = $arr;
            }

        }

        $digiFile = $product->digi_file;

        if ($request->hasFile('digi_file')) {
            $path = imagePath()['digital_item']['path'];
            try {
                $digiFile = uploadFile($request->digi_file, $path);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload your file'];
                return back()->withNotify($notify)->withInput();
            }

        }

        $product->name             = $request->name;
        $product->category_id      = $request->category_id;
        $product->subcategory_id   = $request->subcategory_id;
        $product->brand_id         = $request->brand;
        $product->price            = $request->price;
        $product->discount         = $request->discount;
        $product->discount_type    = $request->discount_type;
        $product->quantity         = $request->quantity;
        $product->hot_deals        = $request->hot_deals ? 1 : 0;
        $product->featured_product = $request->featured_product ? 1 : 0;
        $product->today_deals      = $request->today_deals ? 1 : 0;
        $product->status           = $request->status ? 1 : 0;
        $product->summary          = $request->summary;
        $product->description      = $request->description;
        $product->features         = json_encode($input_feature);
        $product->digital_item     = $request->digital_item;
        $product->file_type        = $request->file_type;
        $product->digi_file        = $digiFile;
        $product->digi_link        = $request->digi_link;
        $product->image            = $filename;
        $product->save();

        // for the liter price
        if ($request->price_per_liter != null) {

            ProductPricePerLiter::updateOrCreate(['product_id'=> $id],[

                'price'=>$request->price_per_liter,
                'product_id'=>$id,
                'liter'=>1,
            ]);

        }
        // for the milliliter price
        if ($request->price_per_milliliter != null) {


            ProductPricePerMilliliter::updateOrCreate(['product_id'=>$id], [

                'price'=> $request->price_per_milliliter,
                'product_id'=>$id,
                'milliliter'=>1,
            ]);

        }
        foreach ($request->volumes as $volumeData) {
            if (isset($volumeData['volume']) && isset($volumeData['price'])) {
                $volume = $volumeData['volume'];
                $price = $volumeData['price'];

                // dd($volume);
                if ($price != 0) {


                // Check both product_id and volume for the update or create operation
                ProductPricePerVolume::updateOrCreate(
                    [
                        'product_id' => $id,
                        'volume' => $volume  // Ensure it checks both product_id and volume
                    ],

                    [
                        'price' => $price,
                        'product_id' => $id,
                        'volume' => $volume
                    ]
                );
                }
            }
        }


        $notify[] = ['success', 'Product updated successfully'];
        return redirect()->back()->withNotify($notify)->withInput();
    }

    public function digitalFileDownload($id) {
        $product   = Product::findOrFail($id);
        $file      = $product->digi_file;
        $path      = imagePath()['digital_item']['path'];
        $full_path = $path . '/' . $file;
        return response()->download($full_path);
    }

    public function todayDeals(Request $request) {
        $pageTitle    = 'Today Deals Products';
        $emptyMessage = 'No product found';
        $products     = Product::where('today_deals', 1);

        if ($request->search) {
            $products->where('name', 'LIKE', "%$request->search%")
                ->orWhere('price', 'LIKE', "%$request->search%")
                ->orWhere('product_sku', 'LIKE', "%$request->search%");
        }

        $products = $products->latest()->paginate(getPaginate());
        return view('admin.product.index', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function todayDealsDiscount(Request $request) {
        $request->validate([
            'discount'      => 'required|numeric|min:0|max:100',
            'discount_type' => 'required|integer|in:1,2',
        ]);

        $general                = GeneralSetting::first();
        $general->discount      = $request->discount;
        $general->discount_type = $request->discount_type;
        $general->save();

        $notify[] = ['success', 'Today deal discount updated successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function reviews($id) {
        $product      = Product::findOrFail($id);
        $pageTitle    = 'Reviews of ' . $product->name;
        $emptyMessage = 'Data not found';
        $reviews      = Review::where('product_id', $id)->with('user')->paginate(getPaginate());
        return view('admin.product.reviews', compact('pageTitle', 'reviews', 'emptyMessage'));
    }

    public function reviewRemove($id) {
        $review = Review::findOrFail($id);
        $review->delete();

        $product     = Product::with('reviews')->findOrFail($review->product_id);

        if($product->reviews->count() > 0){

            $totalReview = $product->reviews->count();
            $totalStar   = $product->reviews->sum('stars');
            $avgRating   = $totalStar / $totalReview;
        }else{
            $avgRating = 0;
        }

        $product->avg_rate = $avgRating;
        $product->save();

        $notify[] = ['success', 'Review remove successfully'];
        return back()->withNotify($notify);
    }

}
